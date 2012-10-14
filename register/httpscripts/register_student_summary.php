<?php
/**			   					httpscripts/register_student_summary.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['cid'])){$cid=$_GET['cid'];}else{$cid=-1;}
if(isset($_POST['cid'])){$cid=$_POST['cid'];}
if(isset($_GET['sids'])){$sids=(array) $_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}
if(isset($_GET['startdate'])){$startdate=$_GET['startdate'];}else{$startdate='';}
if(isset($_POST['startdate'])){$startdate=$_POST['startdate'];}
if(isset($_GET['enddate'])){$enddate=$_GET['enddate'];}else{$enddate='';}
if(isset($_POST['enddate'])){$enddate=$_POST['enddate'];}

/** Taken from PHP manual and > PHP5? */
function dateDiff($startdate,$enddate){
	$startArry = date_parse($startdate);
	$endArry = date_parse($enddate);
	$start_date = gregoriantojd($startArry["month"], $startArry["day"], $startArry["year"]);
	$end_date = gregoriantojd($endArry["month"], $endArry["day"], $endArry["year"]);
	return round(($end_date - $start_date), 0);
	}
/***/

	if(sizeof($sids)==0 or $cid<1){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{

		/* The class for which attendnace is being reported. */
		$thisclass=(array)get_this_class($cid);


		/* Start the week on a Monday. */
		$startdate=date('Y-m-d',strtotime('last sunday  ',strtotime($startdate)));
		$nodays=dateDiff($startdate,$enddate);//number of days for the period
		$startday=dateDiff(date('Y-m-d'),$enddate);//number days relative to today, -ve for the past

		$Students=array();
		$Students['Student']=array();
		/*doing one student at a time*/
		for($c=0;$c<sizeof($sids);$c++){
			$sid=$sids[$c];
			$Student=(array)fetchStudent_short($sid);
			$AttendanceSummary=(array)fetchAttendanceSummary($sid,$startdate,$enddate);
			$Notes=array();
			$table_html=array();
			$rows=array();
			$row=array();
			$row['th'][]='Week Beginning';
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')+$startday-$nodays-7-6,date('Y')));
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')+$startday-$nodays-7-5,date('Y')));
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')+$startday-$nodays-7-4,date('Y')));
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')+$startday-$nodays-7-3,date('Y')));
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')+$startday-$nodays-7-2,date('Y')));
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')+$startday-$nodays-7-1,date('Y')));
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')+$startday-$nodays-7,date('Y')));

			$rows['tr'][]=$row;
			//for($countday=0;$countday<=$nodays;$countday++){
			for($countday=$nodays-7;$countday>-7;$countday=$countday-7){
				$atday=$startday-$countday-6;//number of days in the past for the current event
				$atdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$atday,date('Y')));
				$row=array();
				$row['th'][]=$atdate;

				for($weekday=6;$weekday>-1;$weekday--){
					$atday=$startday-$countday-$weekday;
					$atdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$atday,date('Y')));
					$d=date('D',mktime(0,0,0,date('m'),date('d')+$atday,date('Y')));
					$Attendances=fetchAttendances($sid,$atday,1);
					$classAttendances=fetch_classAttendances($cid,$atday,4,$sid);
					$content='';
					foreach($Attendances['Attendance'] as $aindex => $Attendance){
						if($Attendance['Status']['value']=='p' and $Attendance['Session']['value']=='AM' and $Attendance['Period']['value']=='0'){
							$content.='/';
							}
						elseif($Attendance['Status']['value']=='p' and $Attendance['Session']['value']=='PM' and $Attendance['Period']['value']=='0'){
							$content.="\\";
							}
						elseif($Attendance['Status']['value']=='a' and $Attendance['Period']['value']=='0'){
							$content.=$Attendance['Code']['value'];
							if(!empty($Attendance['Comment']['value'])){
								$Note=array('Date'=>display_date($Attendance['Date']['value']),
											'Session'=>$Attendance['Session']['value'],
											'Code'=>$Attendance['Code']['value'],
											'Comment'=>$Attendance['Comment']['value']
											);
								$Notes['Note'][]=$Note;
								}
							}
						}
					if($content==''){$content='#';}

					$row['td'][]=$content;
					}

				$rows['tr'][]=$row;
				}


			$table_html['table'][]=$rows;
			$Student['AttendanceTable']=$table_html;
			$Student['AttendanceNotes']=$Notes;
			$Student['Attendance']=$AttendanceSummary;
			$Students['Student'][]=$Student;
			}

		$Codes=array();
		$Codes['Code']=array();
		$enum=getEnumArray('absencecode');
		while(list($inval,$description)=each($enum)){	
			$Codes['Code'][]=array('value'=>''.$inval,
						   'description'=>''.get_string($description,'register')
						   );
			}

		$Students['AbsenceCodes']=$Codes;
		$Students['Paper']='portrait';
		$Students['Transform']='attendance_summary';
		$returnXML=$Students;
		$rootName='Students';
		}

require_once('../../scripts/http_end_options.php');
exit;
?>