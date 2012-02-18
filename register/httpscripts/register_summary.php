<?php
/**									register_summary.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$comids=(array)$_GET['sids'];}else{$comids=array();}
if(isset($_POST['sids'])){$comids=(array)$_POST['sids'];}


if(sizeof($comids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{

	$toyear=get_curriculumyear()-1;//TODO: set a proper start of term date

	$Students=array();
	$Students['Community']=array();


	$months=array('August'=>'08','September'=>'09','October'=>'10','November'=>'11','December'=>'12','January'=>'01','February'=>'02','March'=>'03','April'=>'04','May'=>'05','June'=>'06','July'=>'07');
	$Months=array();
	$Months['Year']=$toyear;
	$Months['Month']=array();
	foreach($months as $month => $monthno){
		if($monthno=='01'){$toyear++;}
		$sdate=$toyear.'-'.$monthno.'-'.'00';
		$edate=$toyear.'-'.$monthno.'-'.'31';
		$Month['Label']=$month;
		$Month['StartDate']=$sdate;
		$Month['EndDate']=$edate;
		$Months['Month'][]=$Month;
		}
 	$Students['Months']=$Months;
	$sids=array();

	foreach($comids as $comid){

		/* Passed as comid:::yid so yid is already defined*/
		list($comid,$yid)=explode(':::',$comid);

		if($comid!=''){

			$com=(array)get_community($comid);
			$com['yeargroup_id']=$yid;
			$Community=(array)fetchCommunity($comid);
			$Community['Student']=array();

			/* This will collect all students who have been enrolled
			 * at any time during the course of the year - not just current students.
			 */
			$students=(array)listin_community($com,$Months['Month'][0]['StartDate'],$edate);

			foreach($students as $student){
				$sid=$student['id'];
				if(!in_array($sid,$sids)){
					$sids[]=$sid;
					$Student=(array)fetchStudent_short($student['id']);
					$EnrolStatus=fetchStudent_singlefield($student['id'],'EnrolmentStatus');
					$enrolstatus=$EnrolStatus['EnrolmentStatus']['value'];
					/* Only count students who have actually been on the school role at some point. */
					if($enrolstatus=='C' or $enrolstatus=='P'){
						$Student['Attendances']=array();
						$Attendances=array();
						foreach($Months['Month'] as $Month){
							$Summary=fetchAttendanceSummary($sid,$Month['StartDate'],$Month['EndDate'],'AM');
							$no_x=count_attendance($sid,$Month['StartDate'],$Month['EndDate'],'X','AM');
							$Summary['Summary']['Untimetabled']=array('label'=>'untimetabled',
																	  'value'=>''.$no_x);
							$Attendances[]=$Summary['Summary'];
							$Summary=fetchAttendanceSummary($sid,$Month['StartDate'],$Month['EndDate'],'PM');
							$no_x=count_attendance($sid,$Month['StartDate'],$Month['EndDate'],'X','PM');
							$Summary['Summary']['Untimetabled']=array('label'=>'untimetabled',
																	  'value'=>''.$no_x);
							$Attendances[]=$Summary['Summary'];
							}
						$Student['Attendances']['Summary']=$Attendances;
						$Community['Student'][]=$Student;
						}
					}
				}

			$Students['Community'][]=$Community;
			}
		}

	$Students['Transform']='register_summary';
	$Students['Paper']='landscape';
	
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
