<?php
/**									completion_list_action.php
 */

$action='completion_list.php';

if(isset($_POST['comids'])){$comids=(array) $_POST['comids'];}else{$comids=array();}

include('scripts/sub_action.php');

if(sizeof($comids)==0){
		$result[]=get_string('youneedtoselectstudents');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}

   	$result[]=get_string('seperateprintwindow');
  	include('scripts/results.php');
?>
  <div id="xmlStudent" style="visibility:hidden;">
<?php

  	$AttendanceEvent=fetchAttendanceEvent($currentevent['id']);
 	xmlpreparer('AttendanceEvent',$AttendanceEvent);

	while(list($index,$comid)=each($comids)){
		$Community=fetchCommunity($comid);
		$Community['Student']=array();
		$students=(array)listinCommunity(array('id'=>$comid));
		while(list($index,$student)=each($students)){
			$Student=fetchStudent_short($student['id']);
			$Student['Attendances']['Attendnace'][]=fetchcurrentAttendance($student['id']);
			$Community['Student'][]=$Student;
			}
		xmlpreparer('Community',$Community);
		}
?>
  </div>
  <script>openPrintReport('xmlStudent', 'register')</script>
<?php
		include('scripts/redirect.php');
?>
