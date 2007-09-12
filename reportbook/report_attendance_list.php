<?php
/**									report_attendance_list.php
 *
 *	Lists students and summarises their attendance over a period of time.
 */

$action='report_attendance.php';

$date0=$_POST['date0'];
if(isset($_POST['newyid'])){$yid=$_POST['newyid'];}else{$yid='';}
if(isset($_POST['newfid'])){$fid=$_POST['newfid'];}else{$fid='';}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['year'])){$year=$_POST['year'];}


include('scripts/sub_action.php');

	if($fid!=''){
		$students=listin_community(array('id'=>'','type'=>'form','name'=>$fid));
		}
	elseif($yid!=''){
		$students=listin_community(array('id'=>'','type'=>'year','name'=>$yid));
		}
	else{
		if($rcrid=='%'){
			/*User has a subject not a course responsibility selected*/
			$d_course=mysql_query("SELECT DISTINCT cohort.course_id FROM
				cohort JOIN cridbid ON cridbid.course_id=cohort.course_id WHERE
				cridbid.subject_id='$rbid' AND cohort.stage='$stage' AND cohort.year='$year'");
			$rcrid=mysql_result($d_course,0);
			}

		$students=listin_cohort(array('id'=>'','course_id'=>$rcrid,'year'=>$year,'stage'=>$stage));
		}

	if(sizeof($students)<1){
		$error[]=get_string('needselectstudents',$book);
		$action='report_attendance.php';
    	include('scripts/results.php');
	    include('scripts/redirect.php');
		exit;
		}

	/*no of days between today and date selected*/
	$todate=date('Y-m-d');
	$diff=strtotime($todate)-strtotime($date0);
	$nodays=round($diff/86400);

twoplusprint_buttonmenu();
?>
<div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 
	  <table class="listmenu">
		<tr>
		  <th>
			<label id="checkall"><?php print_string('checkall');?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll	(this);" />
			</label>
		  </th>
		  <th colspan="2"><?php print_string('student');?></th>
		  <th><?php print_string('formgroup');?></th>
		  <th><?php print_string('attendancesummary',$book);?></th>
		</tr>
<?php	
	while(list($index,$student)=each($students)){
		$summary=array();
		$sid=$student['id'];
		$Student=fetchStudent_short($sid);
		$fid=$Student['RegistrationGroup']['value'];
?>
		<tr>
		  <td>
			<input type='checkbox' name='sids[]' value='<?php print $sid; ?>' />
		  </td>
		  <td>
			<a href="infobook.php?current=comments_list.php&sid=<?php 
			  print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">C</a>
		  </td>
		  <td>
			<a href="register.php?current=register_list.php&newfid=<?php
			  print $fid;?>"  target="viewregister"
			  onclick="parent.viewBook('register');"> 
			  <?php print $Student['DisplayFullName']['value']; ?>
			</a>
		  </td>
		  <td>
			<?php print $fid; ?>
		  </td>
		  <td>
<?php
		$summary='';
		$present=0;
		$absent=0;
		$Attendances=(array)fetchAttendances($sid,0,$nodays);
		$noevents=sizeof($Attendances['Attendance']);
		for($c=0;$c<$noevents;$c++){
			if($Attendances['Attendance'][$c]['Status']['value']=='p'){
				$present++;
				}
			else{
				$absent++;
				$summary.=$Attendances['Attendance'][$c]['Code']['value'];
				}
			}
		$average=round(($present/$noevents)*100);
		print $average.'% &nbsp;(Absences '.$absent.' &nbsp;'.$summary.')';
?>
		  </td>
		</tr>
<?php	
		}
	reset($sids);
?>
	  </table>

	</fieldset>

	<input type="hidden" name="newfid" value="<?php print $fid;?>" />
	<input type="hidden" name="newyid" value="<?php print $yid;?>" />
	<input type="hidden" name="date0" value="<?php print $date0;?>" />
 	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
	</form>
  </div>

