<?php
/**									httpscripts/report_incidents_print.php
 *
 *	Printout of selected students' incidents	
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$sids=(array) $_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}
if(isset($_GET['startdate'])){$startdate=$_GET['startdate'];}else{$startdate='';}
if(isset($_POST['startdate'])){$startdate=$_POST['startdate'];}
if(isset($_GET['enddate'])){$enddate=$_GET['enddate'];}else{$enddate='';}
if(isset($_POST['enddate'])){$enddate=$_POST['enddate'];}

if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='';}


	if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{

		$Students=array();
		$Students['Student']=array();
		/*doing one student at a time*/
		for($c=0;$c<sizeof($sids);$c++){
			$sid=$sids[$c];
			$Student=(array)fetchStudent_short($sid);
			$Comments=(array)fetchIncidents($sid,$startdate);
			$Student['Incidents']=$Comments;
			$Students['Student'][]=$Student;
			}
		$Students['Paper']='portrait';
		$Students['Transform']='incident_summary';
		$returnXML=$Students;
		$rootName='Students';
		}

require_once('../../scripts/http_end_options.php');
exit;
?>
<div id='xmlStudent' style='visibility:hidden;'>
<?php
?>
