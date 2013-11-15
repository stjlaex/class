<?php
/**									student_list.php
 *
 *   	Lists students with MedicalFlag set.
 */

$action='med_student_list.php';
//$choice='med_student_list.php';

include('scripts/sub_action.php');

$displayfields=array();
$displayfields[]='Gender';
$displayfields[]='DOB';

if(isset($_POST['colno'])){
	$displayfields_no=$_POST['colno'];
	}
else{
	$displayfields_no=2;
	}
for($dindex=0;$dindex < ($displayfields_no);$dindex++){
	if(isset($_POST['displayfield'.$dindex])){$displayfields[$dindex]=$_POST['displayfield'.$dindex];}
	}

if(isset($_POST['extracol']) and $_POST['extracol']=='yes'){
	$displayfields_no++;
	$displayfields[]='';
	}

/* Approximate to saving 40% of table width for fixed columns. */
$displayfields_width=60/$displayfields_no.'%';

if($list=='visit'){$extrabuttons['export']=array('name'=>'current','title'=>'exportvisits','value'=>'meals_export.php');two_buttonmenu($extrabutton,'medbook');}
else{two_buttonmenu();}

	$sids=array();

	if($medtype!='' or $newyid!=''){
		/*these are the filter vars form the sideoptions*/
		if($medtype!='' and $newyid!=''){
			mysql_query("CREATE TEMPORARY TABLE students
				(SELECT info.student_id FROM info JOIN background
				ON background.student_id=info.student_id WHERE background.type='$medtype'
				AND info.medical='Y' AND info.enrolstatus='C')");
			$d_info=mysql_query("SELECT student_id FROM students JOIN student
				ON student.id=students.student_id WHERE student.yeargroup_id='$newyid';");
			mysql_query('DROP TABLE students;');
			}
		elseif($medtype!=''){
			$d_info=mysql_query("SELECT info.student_id FROM info JOIN background
				ON background.student_id=info.student_id WHERE background.type='$medtype'
				AND info.medical='Y' AND info.enrolstatus='C';");
			}
		elseif($newyid!=''){
			$d_info=mysql_query("SELECT info.student_id FROM info JOIN student
				ON student.id=info.student_id WHERE student.yeargroup_id='$newyid'
				AND info.medical='Y' AND info.enrolstatus='C';");
			}
		}
	elseif($list=='all'){
		/*
		 * Just list all if requested.
		 */
		$d_info=mysql_query("SELECT info.student_id FROM info JOIN student
				ON student.id=info.student_id WHERE 
				info.medical='Y' AND info.enrolstatus='C' ORDER BY student.surname;");
		}
	elseif($list=='new'){
		/*
		 * List all new students.  use filter A% to grab
		 * A,AT,ATD,AP,ACP but will exclude rejected, waiting list,
		 * cancelled etc.
		 */
		$d_info=mysql_query("SELECT info.student_id FROM info JOIN student
				ON student.id=info.student_id WHERE 
				info.medical='Y' AND (info.enrolstatus LIKE 'A%') ORDER BY student.surname;");
		}
	elseif($list=='visit'){
		$d_info=mysql_query("SELECT DISTINCT medical_log.student_id FROM medical_log JOIN student
				ON student.id=medical_log.student_id ORDER BY student.surname;");
		}


if(isset($d_info)){
	while($info=mysql_fetch_array($d_info,MYSQL_ASSOC)){
		$sids[]=$info['student_id'];
		}
	}
?>

<div id="viewcontent" class="content">
<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
<table class="listmenu sidtable">
	<th colspan="2"><?php print_string('checkall'); ?><input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" /></th>
	<th><?php print_string('student'); ?></th>
	<th><?php print_string('formgroup'); ?></th>
<?php

	$d_catdef=mysql_query("SELECT name, subtype FROM categorydef WHERE 
				type='med' AND (rating='1' OR rating='0') ORDER BY rating DESC, name;");
	while($medcat=mysql_fetch_array($d_catdef,MYSQL_ASSOC)){
		$extra_studentfields['Medical'.$medcat['subtype']]=strtolower($medcat['name']);
		}
	$extra_studentfields['NextReviewDate']='nextreviewdate';

	while(list($index,$displayfield)=each($displayfields)){

?>
		<th style="width:<?php print $displayfields_width;?>;"><?php include('scripts/list_studentfield.php');?></th>
<?php
		}

	foreach($sids as $sindex => $sid){
		$display='yes';
		$Student=fetchStudent_short($sid);
		$comment=comment_display($sid);
		/*		$d_senhistory=mysql_query("SELECT id, reviewdate FROM senhistory WHERE 
				student_id='$sid' ORDER BY reviewdate DESC");
		$senhistory=mysql_fetch_array($d_senhistory,MYSQL_ASSOC);
		$Student['NextReviewDate']=array();
		$Student['NextReviewDate']['label']='nextreviewdate';
		$Student['NextReviewDate']['value']=$senhistory['reviewdate'];
		*/

		if($display=='yes'){
?>
		<tr>
		  <td>
			<input type="checkbox" name="sids[]" value="<?php print $sid; ?>" />
			<?php print $sindex+1;?>
		  </td>
		  <td>
			<span title="<?php print $comment['body'];?>">
			<a onclick="parent.viewBook('infobook');" target="viewinfobook"  
			  href="infobook.php?current=comments_list.php&sid=<?php print $sid;?>"
			  class="<?php print $comment['class'];?>">C</a> 
			</span>
		  </td>
		  <td>
		  <?php
		  if($list=='visit'){$curr='med_view_visits.php';$choice='med_student_list.php';}
		  else{$curr='med_view.php';}
		  ?>
			<a href="medbook.php?current=<?php print $curr;?>&sid=<?php print $sid;?>">
			  <?php print $Student['DisplayFullName']['value']; ?>
			</a>
		  </td>
		  <td>
<?php 
				print $Student['RegistrationGroup']['value']; 
?>
		  </td>
<?php
	reset($displayfields);
	while(list($index,$displayfield)=each($displayfields)){
		if(array_key_exists($displayfield,$Student)){
			print '<td>'.$Student[$displayfield]['value'].'</td>';
			}
		else{
			$field=fetchStudent_singlefield($sid,$displayfield);
			print '<td>'.$field[$displayfield]['value'].'</td>';
			}
		}
?>
		</tr>
<?php
				  }
		}
	/* Must unset or the host page thinks a single sid is being displayed*/
	unset($sid);
?>
<tr>
<th colspan="<?php print $displayfields_no+3;?>">&nbsp;</th>
<th>
		  <div class="rowaction">
<?php
$extrabuttons=array();
$extrabuttons['addcolumn']=array('title'=>'addcolumn','name'=>'extracol','value'=>'yes');
all_extrabuttons($extrabuttons,'infobook','processContent(this)')
?>
		  </div>

</th>
</tr>
	  </table>

	  <input type="hidden" name="colno" value="<?php print $displayfields_no;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>
