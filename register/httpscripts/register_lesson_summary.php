<?php
/**			   					httpscripts/register_class_summary.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$sids=(array) $_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}
if(isset($_GET['startdate'])){$startdate=$_GET['startdate'];}else{$startdate='';}
if(isset($_POST['startdate'])){$startdate=$_POST['startdate'];}
if(isset($_GET['enddate'])){$enddate=$_GET['enddate'];}else{$enddate='';}
if(isset($_POST['enddate'])){$enddate=$_POST['enddate'];}


	if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{

		/* Start the week on a Monday. */
		$startdate=date('Y-m-d',strtotime('last sunday  ',strtotime($startdate)));
		$nodays=dateDiff($startdate,$enddate);//number of days for the period
		$weekdate=date('Y-m-d',strtotime('last sunday  ',strtotime($enddate)));//first date of this week
		$startday=dateDiff(date('Y-m-d'),$weekdate);//number days relative to today, -ve for the past

		$Students=array();
		$Students['Student']=array();
		/*doing one student at a time*/
		for($c=0;$c<sizeof($sids);$c++){
			$sid=$sids[$c];
			$Student=(array)fetchStudent_short($sid);

			/* Use a multi-dimesional arrray indexed by (day,period)
			 *   to store attendance totals
			 */
			$days=array();
			$lessonperiods=array();
			$periods=(array)get_class_periods(get_currentevent(),1);
			$lessonperiods[0]=array('title'=>'AM','time'=>'','late'=>0,'absent'=>0,'present'=>0,'class'=>'');
			foreach($periods as $period_seq => $thisperiod){
				$lessonperiods[$period_seq]=$lessonperiods[0];
				$lessonperiods[$period_seq]['title']=$thisperiod['title'];
				$lessonperiods[$period_seq]['time']=$thisperiod['time'];
				}

			$table_html=array();
			$rows=array();
			$row=array();
			$row['th'][]=display_date($enddate);
			for($weekday=6;$weekday>-1;$weekday--){
				$atday=$startday-$weekday;
				$thisday=date('D',mktime(0,0,0,date('m'),date('d')+$atday,date('Y')));
				$days[$thisday]=$lessonperiods;
				$row['th'][]=$thisday;
				}
			$rows['tr'][]=$row;//the table header row

			for($countday=$nodays-7;$countday>-7;$countday=$countday-7){
				for($weekday=6;$weekday>-1;$weekday--){
					$atday=$startday-$countday-$weekday;
					$atdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$atday,date('Y')));
					$thisday=date('D',mktime(0,0,0,date('m'),date('d')+$atday,date('Y')));

					$Attendances=fetchAttendances($sid,$atday,1);
					foreach($Attendances['Attendance'] as $Attendance){

						$thisperiod=$Attendance['Period']['value'];

						if($Attendance['Status']['value']=='p'){
							if($Attendance['Late']['value']>0){
								$days[$thisday][$thisperiod]['late']++;
								}
							$days[$thisday][$thisperiod]['present']++;
							}
						elseif($Attendance['Status']['value']=='a'){
							$absent_event=get_event($Attendance['Date']['value'],$Attendance['Session']['value']);
							$SessionAttendance=fetchcurrentAttendance($sid,$absent_event['id']);
							if($SessionAttendance['Status']['value']=='p'){
								$content=' X ';
								}
							else{
								$content=$Attendance['Code']['value'];
								}
							$days[$thisday][$thisperiod]['absent']++;
							}
						if($days[$thisday][$thisperiod]['class']==''){
							$days[$thisday][$thisperiod]['class']=get_this_class($Attendance['Class']['value']);
							}
						}
					}
				}

			foreach($lessonperiods as $period_seq => $thisperiod){
				$row=array();
				$row['th'][]=$thisperiod['title'].' - '.$thisperiod['time'];
				foreach($days as $day){
					$content=$day[$period_seq];
					$cell=array();
					if($period_seq>0){
						$cell['div'][]=$content['class']['name'];
						}
					if($content['absent']>0 or $content['present']>0){
						$cell['div'][]=$content['absent'].' / '.$content['present'];
						}
					$row['td'][]=$cell;
					}
				$rows['tr'][]=$row;
				}


			$table_html['table'][]=$rows;
			$Student['AttendanceTable']=$table_html;
			$Students['Student'][]=$Student;
			}


		$Students['Paper']='portrait';
		$Students['Transform']='attendance_lesson_summary';
		$returnXML=$Students;
		$rootName='Students';
		}

require_once('../../scripts/http_end_options.php');
exit;
?>