<?php
/**									report_attendance_list.php
 *
 *	Lists students and summarises their attendance between to given dates.
 */

$action='report_attendance.php';

$startdate=$_POST['date0'];
$enddate=$_POST['date1'];
$reporttype=$_POST['reporttype'];
if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['formid']) and $_POST['formid']!=''){$comid=$_POST['formid'];}
elseif(isset($_POST['houseid'])  and $_POST['houseid']!=''){$comid=$_POST['houseid'];}else{$comid='';}

$reporttypes=array('P'=>'classes','S'=>'registrationsession');
$curryear=get_curriculumyear();
$students=array();

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
		$pastorals=(array)list_pastoral_respon();
		$ryids=$pastorals['years'];
		if(sizeof($ryids)>0){
			foreach($ryids as $ryid){
				if($ryid>-100){
					$students=array_merge($students,listin_community(array('id'=>'','type'=>'year','name'=>$ryid)));
					}
				}
			}
		}





	if(sizeof($students)<1){
		$error[]=get_string('needselectstudents',$book);
		$action='report_attendance.php';
    	include('scripts/results.php');
	    include('scripts/redirect.php');
		exit;
		}

$extrabuttons=array();
$extrabuttons['studentsummary']=array('name'=>'current',
									   'value'=>'report_attendance_print.php',
									   'onclick'=>'checksidsAction(this)');
$extrabuttons['lessonsummary']=array('name'=>'current',
									 'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/register/',
									 'title'=>'printreportsummary',
									 'value'=>'register_lesson_summary.php',
									 'onclick'=>'checksidsAction(this)'
									 );
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
		  <th colspan="3" style="text-align:center;"><?php print get_string($reporttypes[$reporttype],'register').' '. 
						get_string('attendance','register').' '.get_string('reports','report');?></th>
		  <th class="smalltable"><?php print_string('attended','register');?></th>
		  <th class="smalltable"><?php print_string('authorisedabsence','register');?></th>
		  <th class="smalltable"><?php print_string('unauthorisedabsence','register');?></th>
		  <th class="smalltable"><?php print_string('late','register');?></th>
		</tr>
<?php	
	$sids=array();
	foreach($students as $student){
		$sid=$student['id'];
		$sids[]=$sid;
		$Student=fetchStudent_short($sid);
?>
		<tr>
		  <td>
			<input type='checkbox' name='sids[]' value='<?php print $sid; ?>' />
		  </td>
		  <td>
			&nbsp;
		  </td>
		  <td>
			<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"  target="viewinfobook"
			  onclick="parent.viewBook('infobook');"> 
			  <?php print $Student['DisplayFullSurname']['value']; ?>
			</a>
		  </td>
		  <td>
			<?php print $Student['RegistrationGroup']['value']; ?>
		  </td>
<?php

	if($reporttype=='S'){
		$Attendance=fetchAttendanceSummary($sid,$startdate,$enddate);
		$noattended=$Attendance['Summary']['Attended']['value'];
		$nolate=$Attendance['Summary']['Lateunauthorised']['value'] + $Attendance['Summary']['Latetoregister']['value'];
		$noabsent_authorised=$Attendance['Summary']['Absentauthorised']['value'];
		$noabsent_unauthorised=$Attendance['Summary']['Absentunauthorised']['value'];
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
	else{
		$noattended=0;
		$nolate=0;
		$noabsent_authorised=0;
		$noabsent_unauthorised=0;
		$classlist='';

		$crids=(array)list_student_courses($sid);
		foreach($crids as $crid){
			$classes=(array)list_student_course_classes($sid,$crid);
			foreach($classes as $class){
				$classlist=$classlist.' '.$class['name'];
				$Attendance=fetch_classAttendanceSummary($class['id'],$sid,$startdate,$enddate);
				$noattended+=$Attendance['Summary']['Attended']['value'];
				$nolate+=$Attendance['Summary']['Lateunauthorised']['value'] + $Attendance['Summary']['Latetoregister']['value'];
				$noabsent_authorised+=$Attendance['Summary']['Absentauthorised']['value'];
				$noabsent_unauthorised+=$Attendance['Summary']['Absentunauthorised']['value'];
				}
			}

		$noabsent=$noabsent_authorised+$noabsent_unauthorised;
		$nosession=$noattended+$noabsent;
		$average=round(($noattended / $nosession)*100);
		if($average<80){$cssclass=' class="hilite"';}
		elseif($average<90){$cssclass=' class="midlite"';}
		elseif($average>98){$cssclass=' class="gomidlite"';}
		else{$cssclass='';}
		print '<td '.$cssclass.'><span title="'.$classlist.'">'.$average.'% &nbsp;('.$noattended.')</span></td>';
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

