<?php
/**									register_print.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$comids=(array)$_GET['sids'];}else{$comids=array();}
if(isset($_POST['sids'])){$comids=(array)$_POST['sids'];}
if(isset($_GET['eveid'])){$eveid=$_GET['eveid'];}else{$eveid='';}
if(isset($_POST['eveid'])){$eveid=$_POST['eveid'];}
if(isset($_GET['evedate'])){$date=$_GET['evedate'];}else{$date='';}
if(isset($_POST['evedate'])){$date=$_POST['evedate'];}

if(sizeof($comids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{

	if($eveid==''){
		$currentevent=get_currentevent();
		$eveid=$currentevent['id'];
		}

	$Students=array();
	$Students['Community'];


	foreach($comids as $comid){

		/* Passed as comid:::yid so yid is already defined*/
		list($comid,$yid)=explode(':::',$comid);

		if($comid!=''){
			$com=(array)get_community($comid);
			$com['yeargroup_id']=$yid;
			$Community=(array)fetchCommunity($comid);
			$Community['Student']=array();
			$students=(array)listin_community($com);
			foreach($students as $student){
				$Student=fetchStudent_short($student['id']);
				$sectionid=get_student_section($student['id']);
				if($CFG->schooltype=='ela'){
					$firstmonday=date('Y-m-d',strtotime('last Monday'));
					for($i=0;$i<5;$i++){
						$weekday=date( "Y-m-d", strtotime($firstmonday.'+'.$i.' days'));
						if(date('l',strtotime($weekday))!='Saturday' and date('l',strtotime($weekday))!='Sunday'){$Students['Dates'][]=array('display'=>display_date($weekday),'value'=>date('Y-m-d',strtotime($weekday)),'day'=>date('l',strtotime($weekday)));}
						}
					}
				else{
					for($i=0;$i<7;$i++){
						$weekday=date( "Y-m-d", strtotime('-'.$i.' days'));
						if(isset($CFG->registration[$sectionid])){$secid=$sectionid;}
						else{$secid=1;}
						if($CFG->registration[$secid]=='single'){$sessions=array('AM');}
						else{$sessions=array('AM','PM');}
						foreach($sessions as $session){
							$e=get_event($weekday,$session);
							if($e['id']!='' and date('l',strtotime($weekday))!='Saturday' and date('l',strtotime($weekday))!='Sunday'){
								$att=fetchcurrentAttendance($student['id'],$e['id']);
								if($att['Date']['value']==''){$att['Date']['value']=$weekday;$att['Session']['value']=$session;}
								$Student['Attendances']['Attendance'][]=$att;
								}
							if($e['id']=='' and date('l',strtotime($weekday))!='Saturday' and date('l',strtotime($weekday))!='Sunday'){
								$Student['Attendances']['Attendance'][]['Date']['value']=$weekday;
								}
							}

						if(isset($CFG->regperiods)){
							if(isset($CFG->regperiods[$sectionid])){$secid=$sectionid;}
							else{$secid=1;}
							if(isset($CFG->regperiods[$secid][$session])){
								foreach($CFG->regperiods[$secid][$session] as $periodid=>$period){
									$Students['Periods'][]=array('section'=>$secid,'session'=>$session,'title'=>$periodid);
									$e=get_event($weekday,$session,$periodid);
									if($e['id']!='' and date('l',strtotime($weekday))!='Saturday' and date('l',strtotime($weekday))!='Sunday'){
										$att=fetchcurrentAttendance($student['id'],$e['id']);
										if($att['Date']['value']==''){$att['Date']['value']=$weekday;$att['Session']['value']=$session;$att['Period']['value']=$periodid;}
										$Student['Attendances']['Attendance'][]=$att;
										}
									if($e['id']=='' and date('l',strtotime($weekday))!='Saturday' and date('l',strtotime($weekday))!='Sunday'){
										$Student['Attendances']['Attendance'][]['Date']['value']=$weekday;
										$Student['Attendances']['Attendance'][]['Period']['value']=$periodid;
										}
									}
								}
							}
						$week[$weekday]=$weekday;
						}
					}
					$Community['Student'][]=$Student;
				}
				foreach($week as $day){
					if(date('l',strtotime($day))!='Saturday' and date('l',strtotime($day))!='Sunday'){$Students['Dates'][]=array('display'=>display_date($day),'value'=>date('Y-m-d',strtotime($day)),'day'=>date('l',strtotime($day)));}
					}
				$Students['Community'][]=$Community;
			}
		}
	$AttendanceEvent=fetchAttendanceEvent($eveid);
	$Students['AttendanceEvent']=$AttendanceEvent;
	if($CFG->schooltype=='ela'){
		$Students['Transform']='register_week_print_ela';
		}
	else{
		$Students['Transform']='register_week_print';
		}

	$Students['Paper']='landscape';

	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
