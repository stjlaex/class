<?php
/**									student_list.php
 *
 *   	Lists students flagged as SEN and list their ids in array sids.
 */

$action='sen_student_list.php';
$choice='sen_student_list.php';

include('scripts/sub_action.php');

$displayfields=array();
$displayfields[]='RegistrationGroup';$displayfields[]='Gender';$displayfields[]='NextReviewDate';
if(isset($_POST['displayfield'])){$displayfields[0]=$_POST['displayfield'];}
if(isset($_POST['displayfield1'])){$displayfields[1]=$_POST['displayfield1'];}
if(isset($_POST['displayfield2'])){$displayfields[2]=$_POST['displayfield2'];}

two_buttonmenu();

	/*these are the filter vars form the sideoptions*/
	if($sentype!='' and $newyid!=''){
		mysql_query("CREATE TEMPORARY TABLE students
				(SELECT info.student_id FROM info JOIN sentypes
				ON sentypes.student_id=info.student_id WHERE sentypes.sentype='$sentype'
				AND info.sen='Y' AND info.enrolstatus='C')");
		$d_info=mysql_query("SELECT student_id FROM students JOIN student
				ON student.id=students.student_id WHERE student.yeargroup_id='$newyid';");
		mysql_query('DROP TABLE students;');
		}
	elseif($sentype!=''){
		$d_info=mysql_query("SELECT info.student_id FROM info JOIN sentypes
				ON sentypes.student_id=info.student_id WHERE sentypes.sentype='$sentype'
				AND info.sen='Y' AND info.enrolstatus='C';");
		}
	elseif($newyid!=''){
		$d_info=mysql_query("SELECT info.student_id FROM info JOIN student
				ON student.id=info.student_id WHERE student.yeargroup_id='$newyid'
				AND info.sen='Y' AND info.enrolstatus='C';");
		}
	else{
		$d_info=mysql_query("SELECT student_id FROM info WHERE sen='Y' AND enrolstatus='C';");
		}

	$sids=array();
	while($info=mysql_fetch_array($d_info,MYSQL_ASSOC)){
		$sids[]=$info['student_id'];
		}
?>

<div id="viewcontent" class="content">
<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
<table class="listmenu sidtable">
	<th colspan="2"><?php print_string('checkall'); ?><input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" /></th>
	<th><?php print_string('student'); ?></th>
<?php
	$extra_studentfields=array('NextReviewDate'=>'nextreviewdate');
	while(list($index,$displayfield)=each($displayfields)){
?>
		<th><?php include('scripts/list_studentfield.php');?></th>
<?php
		}

	while(list($index,$sid)=each($sids)){
		$display='yes';
		$Student=fetchStudent_short($sid);
		$comment=commentDisplay($sid);
		$d_senhistory=mysql_query("SELECT id, reviewdate FROM senhistory WHERE 
				student_id='$sid' ORDER BY reviewdate DESC");
		$senhistory=mysql_fetch_array($d_senhistory,MYSQL_ASSOC);
		$Student['NextReviewDate']=array();
		$Student['NextReviewDate']['label']='nextreviewdate';
		$Student['NextReviewDate']['value']=$senhistory['reviewdate'];
		if($sensupport!=''){
			$senhid=$senhistory['id'];
			$d_senhistory=mysql_query("SELECT subject_id FROM sencurriculum WHERE 
				senhistory_id='$senhid' AND categorydef_id='$sensupport'");
			if(mysql_num_rows($d_senhistory)==0){$display='no';}
			}
		if($display=='yes'){
?>
		<tr>
		  <td>
			<input type="checkbox" name="sids[]" value="<?php print $sid; ?>" />
			<?php print $index+1;?>
		  </td>
		  <td>
			<a onclick="parent.viewBook('infobook');" target="viewinfobook" 
			  href="infobook.php?current=student_scores.php&sid=<?php print $sid;?>">T</a> 
			<span title="<?php print $comment['body'];?>">
			<a onclick="parent.viewBook('infobook');" target="viewinfobook"  
			  href="infobook.php?current=comments_list.php&sid=<?php print $sid;?>"
			  class="<?php print $comment['class'];?>">C</a> 
			</span>
			<a onclick="parent.viewBook('infobook');" target="viewinfobook"  
			  href="infobook.php?current=incidents_list.php&sid=<?php print $sid;?>">I</a>
		  </td>
		  <td>
			<a href="seneeds.php?current=sen_view.php&sid=<?php print $sid;?>">
			  <?php print $Student['DisplayFullName']['value']; ?>
			</a>
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
	reset($sids);
?>
	  </table>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>