<?php
/**			   					httpscripts/report_attendance_print.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$sids=(array) $_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}
if(isset($_GET['startdate'])){$startdate=$_GET['startdate'];}else{$startdate='';}
if(isset($_POST['startdate'])){$startdate=$_POST['startdate'];}
if(isset($_GET['enddate'])){$enddate=$_GET['enddate'];}else{$enddate='';}
if(isset($_POST['enddate'])){$enddate=$_POST['enddate'];}

function dateDiff($startdate,$enddate){
	// Parse dates for conversion
	$startArry = date_parse($startdate);
	$endArry = date_parse($enddate);
	
	// Convert dates to Julian Days
	$start_date = gregoriantojd($startArry["month"], $startArry["day"], $startArry["year"]);
	$end_date = gregoriantojd($endArry["month"], $endArry["day"], $endArry["year"]);
	
	// Return difference
	return round(($end_date - $start_date), 0);
	}

	if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{

		$nodays=dateDiff($startdate,$enddate);
		$startday=dateDiff(date('Y-m-d'),$enddate);

		$Students=array();
		$Students['Student']=array();
		/*doing one student at a time*/
		for($c=0;$c<sizeof($sids);$c++){
			$sid=$sids[$c];
			$Student=fetchStudent_short($sid);
			$Attendance=fetchAttendanceSummary($sid,$startdate,$enddate);

			$table_html=array();
			$rows=array();
			$row=array();
			$row['th'][]='Week Beginning';
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')-$startday-$nodays,date('Y')));
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')-$startday-$nodays+1,date('Y')));
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')-$startday-$nodays+2,date('Y')));
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')-$startday-$nodays+3,date('Y')));
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')-$startday-$nodays+4,date('Y')));
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')-$startday-$nodays+5,date('Y')));
			$row['th'][]=date('D',mktime(0,0,0,date('m'),date('d')-$startday-$nodays+6,date('Y')));

			$rows['tr'][]=$row;

			for($countday=$nodays;$countday>0;$countday--){
				$atdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-$startday-$countday,date('Y')));

				if($weekday==0){
					$row=array();
					$row['th'][]=$atdate;
					}
				$day=$startday-$countday;
				$Attendances=fetchAttendances($sid,$day,1);
				if($Attendances['Attendance'][0]['Status']['value']=='p'){
					$content='/';
					}
				elseif($Attendances['Attendance'][0]['Status']['value']=='a'){
					$content=$Attendances['Attendance'][0]['Code']['value'];
					}
				else{
					$content='#';
					}

				$row['td'][]=$content;

				if($weekday==6 or $countday==1){
					$rows['tr'][]=$row;
					$weekday=0;
					}
				else{$weekday++;}
				}



			$table_html['table'][]=$rows;
			$Student['AttendanceTable']=$table_html;


			$Student['Attendance']=$Attendance;
			$Students['Student'][]=$Student;
			}

		$Students['Paper']='portrait';
		$Students['Transform']='attendance_summary';
		$returnXML=$Students;
		$rootName='Students';
		}

require_once('../../scripts/http_end_options.php');
exit;
?>