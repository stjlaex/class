<?php
/**									report_attendance_list.php
 *
 *	Lists students and summarises their attendance between to given dates.
 */

$action='report_attendance.php';

$startdate=$_POST['date0'];
$enddate=$_POST['date1'];
if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['formid']) and $_POST['formid']!=''){$comid=$_POST['formid'];}
elseif(isset($_POST['houseid'])  and $_POST['houseid']!=''){$comid=$_POST['houseid'];}else{$comid='';}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['year'])){$year=$_POST['year'];}


include('scripts/sub_action.php');

	if($comid!=''){
		$com=get_community($comid);
		if($yid!=''){
			$com['yeargroup_id']=$yid;
			$students=listin_community($com);
			}
		else{
			$students=listin_community($com);
			}
		}
	elseif($yid!=''){
		$students=listin_community(array('id'=>'','type'=>'year','name'=>$yid));
		}
	else{
		if($rcrid=='%'){
			/*User has a subject not a course responsibility selected*/
			$d_course=mysql_query("SELECT DISTINCT cohort.course_id FROM
				cohort JOIN component ON component.course_id=cohort.course_id WHERE
				component.subject_id='$rbid' AND component.id='' AND cohort.stage='$stage' AND cohort.year='$year'");
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

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
								'value'=>'report_attendance_print.php',
								'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons,$book);
?>
<div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <div id="xml-checked-action" style="display:none;">
		<period>
		  <startdate><?php print $startdate;?></startdate>
		  <enddate><?php print $enddate;?></enddate>
		</period>
	  </div>

	  <table class="listmenu sidtable">
		<tr>
		  <th>
			<label id="checkall"><?php print_string('checkall');?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll	(this);" />
			</label>
		  </th>
		  <th colspan="2"><?php print_string('student');?></th>
		  <th><?php print_string('formgroup');?></th>
		  <th class="smalltable"><?php print_string('attended','register');?></th>
		  <th class="smalltable"><?php print_string('authorisedabsence','register');?></th>
		  <th class="smalltable"><?php print_string('unauthorisedabsence','register');?></th>
		  <th class="smalltable"><?php print_string('late','register');?></th>
		</tr>
<?php	
	$sids=array();
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
		$sids[]=$sid;
		$Student=fetchStudent_short($sid);
		$fid=$Student['RegistrationGroup']['value'];
?>
		<tr>
		  <td>
			<input type='checkbox' name='sids[]' value='<?php print $sid; ?>' />
		  </td>
		  <td>
			&nbsp;
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
<?php
		$Attendance=fetchAttendanceSummary($sid,$startdate,$enddate);
		$noattended=$Attendance['Summary']['Attended']['value'];
		$nolate=$Attendance['Summary']['Lateunauthorised']['value'] + $Attendance['Summary']['Latetoregister']['value'];
		$noabsent_authorised=$Attendance['Summary']['Absentauthorised']['value'];
		$noabsent_unauthorised=$Attendance['Summary']['Absentunauthorised']['value'];
		if($noattended>0){
			$noabsent=$noabsent_authorised+$noabsent_unauthorised;
			$nosession=$noattended+$noabsent;
			$average=round(($noattended / $nosession)*100);
			if($average<80){$cssclass=' class="hilite"';}
			elseif($average<90){$cssclass=' class="midlite"';}
			elseif($average>99){$cssclass=' class="gomidlite"';}
			else{$cssclass='';}
			print '<td '.$cssclass.'>'.$average.'% &nbsp;('.$noattended.')</td>';
			print '<td>'.$noabsent_authorised.'</td>';
			if(($noabsent_unauthorised/$nosession)>0.05){$cssclass=' class="midlite"';}
			else{$cssclass='';}
			print '<td '.$cssclass.'>'.$noabsent_unauthorised.'</td>';
			if(($nolate/$nosession)>0.16){$cssclass=' class="hilite"';}
			elseif(($nolate/$nosession)>0.08){$cssclass=' class="midlite"';}
			else{$cssclass='';}
			print '<td '.$cssclass.'>'.$nolate.'</td>';
			}
?>
		</tr>
<?php	
		}
	reset($sids);
?>
	  </table>

	</fieldset>

	<input type="hidden" name="date0" value="<?php print $startdate;?>" />
	<input type="hidden" name="date1" value="<?php print $enddate;?>" />
 	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
	</form>
  </div>

