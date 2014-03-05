<?php
/**									student_list.php
 *
 *   	Lists students flagged as SEN and list their ids in array sids.
 */

$action='sen_student_list.php';
//$choice='sen_student_list.php';

include('scripts/sub_action.php');

$displayfields=array();
$displayfields[]='Gender';
$displayfields[]='NextReviewDate';

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



	$sids=array();
	if($sentype!='' or $newyid!='' or $sensupport!=''){
		/* 
		 * These are the filter vars form the sideoptions
		 */
		if($sentype!='' and $newyid!=''){
			mysql_query("CREATE TEMPORARY TABLE tempstudents
				(SELECT info.student_id FROM info JOIN sentype
				ON sentype.student_id=info.student_id WHERE sentype.sentype='$sentype'
				AND info.sen='Y' AND info.enrolstatus='C')");
			$d_info=mysql_query("SELECT student_id FROM tempstudents JOIN student
				ON student.id=tempstudents.student_id WHERE
				student.yeargroup_id='$newyid' ORDER BY student.surname;");
			mysql_query('DROP TABLE tempstudents;');
			}
		elseif($sentype!=''){
			$d_info=mysql_query("SELECT info.student_id FROM info JOIN sentype
				ON sentype.student_id=info.student_id WHERE sentype.sentype='$sentype'
				AND info.sen='Y' AND info.enrolstatus='C';");
			}
		elseif($newyid!=''){
			$d_info=mysql_query("SELECT info.student_id FROM info JOIN student
				ON student.id=info.student_id WHERE student.yeargroup_id='$newyid'
				AND info.sen='Y' AND info.enrolstatus='C' ORDER BY student.surname;");
			}
		elseif($sensupport!=''){
			$d_info=mysql_query("SELECT info.student_id FROM info WHERE info.student_id=ANY(SELECT student_id FROM senhistory
				JOIN sencurriculum ON sencurriculum.senhistory_id=senhistory.id WHERE sencurriculum.categorydef_id='$sensupport')
				AND info.sen='Y' AND info.enrolstatus='C';");
			}

		}
	elseif($list=='all'){
		/*
		 * Just list all if requested.
		 */
		$d_info=mysql_query("SELECT info.student_id FROM info JOIN student
				ON student.id=info.student_id WHERE 
				info.sen='Y' AND info.enrolstatus='C' ORDER BY student.surname;");
		}

if(isset($d_info)){
	while($info=mysql_fetch_array($d_info,MYSQL_ASSOC)){
		$sids[]=$info['student_id'];
		}
	}


/* Nothing to do unless their are sids to list. */
if(sizeof($sids)>0){
	$extrabuttons['exportstudentrecords']=array('name'=>'current',
											'title'=>'exportstudentrecords',
											'value'=>'export_students.php');

	two_buttonmenu($extrabuttons,'infobook');
?>

	<div id="viewcontent" class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu sidtable">
		  <th colspan="2"><?php print_string('checkall'); ?><input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" /></th>
		  <th><?php print_string('student'); ?></th>
		  <th><?php print_string('formgroup'); ?></th>
<?php
	$extra_studentfields=array('NextReviewDate'=>'nextreviewdate');
	foreach($displayfields as $dno => $displayfield){
?>
					<th style="width:<?php print $displayfields_width;?>;">
					<?php include('scripts/list_studentfield.php');?>
					</th>
<?php
		}
?>
				</tr>
				<tr>
<?php
		foreach($displayfields as $dno => $displayfield){
		$sortno=$dno+4;
		//a=age, d=date, i=integer, s=string
		if($displayfield=='Age'){$sort_types.=",'a'";}
		elseif($displayfield=='DOB'  or $displayfield=='IdExpiryDate' or $displayfield=='LeavingDate' or $displayfield=='EntryDate' or $displayfield=='EnrolmentApplicationDate'){$sort_types.=",'d'";}
		elseif($displayfield=='YearGroup' or $displayfield=='EnrolmentYearGroup' or $displayfield=='EnrolNumber'){$sort_types.=",'i'";}
		else{$sort_types.=",'s'";}
?>
					<th class="noprint">
						<div class="rowaction">
							<input class="underrow" type='button' name='action' value='v' onClick='tsDraw("<?php print $sortno;?>A", "sidtable");' />
							<input class="underrow"  type='button' name='action' value='-' onClick='tsDraw("<?php print $sortno;?>U", "sidtable");' />
							<input class="underrow"  type='button' name='action' value='^' onClick='tsDraw("<?php print $sortno;?>D", "sidtable");' />
						</div>
					</th>
		
<?php
		}
?>
				</tr>
			</thead>
			<tbody>
<?php

	foreach($sids as $sindex => $sid){
		$display='yes';
		$Student=fetchStudent_short($sid);
		$comment=comment_display($sid);
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
			<?php print $sindex+1;?>
		  </td>
		  <td>
			<span title="<?php print $comment['body'];?>">
			 <a onclick="parent.viewBook('infobook');" target="viewinfobook"  href="infobook.php?current=comments_list.php&sid=<?php print $sid;?>" class="<?php print $comment['class'];?>">
                <span title="" class="fa fa-comment"></span>
             </a> 
			</span>
		  </td>
		  <td class="student">
			<a href="seneeds.php?current=sen_view.php&sid=<?php print $sid;?>">
			  <?php print $Student['DisplayFullName']['value']; ?>
			</a>
		  </td>
		  <td>
			<?php	print $Student['RegistrationGroup']['value']; ?>
		  </td>
<?php
				foreach($displayfields as $displayfield){
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
			</tbody>
			<tfoot class="noprint">
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
			</tfoot>
		</table>

		<input type="hidden" name="colno" value="<?php print $displayfields_no;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
</div>

<?php
	}
?>

<script type="text/javascript">
	var TSort_Data = new Array ('sidtable','','','',''<?php print $sort_types;?>);
	tsRegister();
</script>
