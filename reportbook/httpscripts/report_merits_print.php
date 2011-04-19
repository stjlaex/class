<?php
/**									httpscripts/report_merits_print.php
 *
 *	Printout of selected students' merits	
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$sids=(array) $_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}
if(isset($_GET['startdate'])){$startdate=$_GET['startdate'];}else{$startdate='';}
if(isset($_POST['startdate'])){$startdate=$_POST['startdate'];}
if(isset($_GET['enddate'])){$enddate=$_GET['enddate'];}else{$enddate='';}
if(isset($_POST['enddate'])){$enddate=$_POST['enddate'];}

if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='%';}

$curryear=get_curriculumyear();


if(sizeof($sids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{
	$Students=array();
	$Students['Student']=array();
	for($c=0;$c<sizeof($sids);$c++){
		$sid=$sids[$c];
		$d_m=mysql_query("SELECT COUNT(id) FROM merits WHERE
			date>='$startdate' AND date<='$enddate' AND merits.subject_id LIKE '$bid' 
			AND student_id='$sid' AND year='$curryear';");
		$merit_count=mysql_result($d_m,0);

		$Student=fetchStudent_short($sid);
		
		$house=get_student_house($sid);
		$Merits=fetchMerits($sid,$merit_count,$bid,'%',$curryear);

		$Student['House']['value']=$house;
		$Student['Merits']=$Merits;
		$Students['Student'][]=$Student;
		}

	$Students['Paper']='portrait';
	$Students['Transform']='merit_certificate';
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
<div id='xmlStudent' style='visibility:hidden;'>
<?php
?>
