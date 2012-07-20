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

   
	/* Months will be either months or weeks(!) depending on the yeargroup. */
	$Months=array();
	$Months['Year']=$toyear;
	$Months['Month']=array();
	list($comid,$yid)=explode(':::',$comids[0]);
	if($yid<0){
		$startdate=strtotime($toyear.'-08-20');
		$firstmonday=strtotime('next Monday',$startdate);
		for($i=1;$i<=48;$i++){
			$sdate=date('Y-m-d', strtotime('+'.$i.' week',$firstmonday));
			$edate=date('Y-m-d', strtotime('+'.$i.' week + 6 days',$firstmonday));
			$Month['Label']=$i;
			$Month['StartDate']=$sdate;
			$Month['EndDate']=$edate;
			$Months['Month'][]=$Month;
			}
		}
	else{
		$months=array('August'=>'08','September'=>'09','October'=>'10','November'=>'11','December'=>'12','January'=>'01','February'=>'02','March'=>'03','April'=>'04','May'=>'05','June'=>'06','July'=>'07');
		$theyear=$toyear;
		foreach($months as $month => $monthno){
			if($monthno=='01'){$theyear=$toyear+1;}
			$sdate=$theyear.'-'.$monthno.'-'.'00';
			$edate=$theyear.'-'.$monthno.'-'.'31';
			$Month['Label']=$month;
			$Month['StartDate']=$sdate;
			$Month['EndDate']=$edate;
			$Months['Month'][]=$Month;
			}
		}
	$Students['Months']=$Months;


	$done_sids=array();
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
			unset($com['yeargroup_id']);
			$students=(array)listin_community($com,$Months['Month'][0]['StartDate'],$edate);

			foreach($students as $student){
				$sid=$student['id'];
				$include='no';
				if(!in_array($sid,$done_sids)){
					$oldcoms=(array)list_member_history($sid,'form');
					if($oldcoms[0]['id']==$comid){$include='yes';}
					}
				if($include=='yes'){
					$done_sids[]=$sid;
					$Student=(array)fetchStudent_short($student['id']);
					$EnrolStatus=fetchStudent_singlefield($student['id'],'EnrolmentStatus');
					$enrolstatus=$EnrolStatus['EnrolmentStatus']['value'];
					/* Only count students who have actually been on the school role at some point. */
					if($enrolstatus=='C' or $enrolstatus=='P'){
						$Student['Attendances']=array();
						$Attendances=array();
						$no_present=0;
						foreach($Months['Month'] as $Month){
							$Summary=fetchAttendanceSummary($sid,$Month['StartDate'],$Month['EndDate'],'AM');
							$no_x=count_attendance($sid,$Month['StartDate'],$Month['EndDate'],'X','AM');
							$Summary['Summary']['Untimetabled']=array('label'=>'untimetabled',
																	  'value'=>''.$no_x);
							$no_present+=$Summary['Summary']['Attended']['value'];
							$Attendances[]=$Summary['Summary'];
							$Summary=fetchAttendanceSummary($sid,$Month['StartDate'],$Month['EndDate'],'PM');
							$no_x=count_attendance($sid,$Month['StartDate'],$Month['EndDate'],'X','PM');
							$Summary['Summary']['Untimetabled']=array('label'=>'untimetabled',
																	  'value'=>''.$no_x);
							$no_present+=$Summary['Summary']['Attended']['value'];
							$Attendances[]=$Summary['Summary'];
							}
						$Student['Attendances']['Summary']=$Attendances;
						if($no_present>0){
							$Community['Student'][]=$Student;
							}
						}
					}
				}

			$Students['Community'][]=$Community;
			}
		}

	if($yid<0){
		$Students['Transform']='register_summary_weekly';
		}
	else{
		$Students['Transform']='register_summary_monthly';
        }
	
	//$Students['Transform']='register_summary';
	$Students['Paper']='landscape';
	
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
