<?php
/**
 *			   					httpscripts/student_targets_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

//if(!isset($xmlid)){print 'Failed'; exit;}

if(isset($_GET['sid'])){$sid=$_GET['sid'];}else{$sid=-1;}
if(isset($_POST['sid'])){$sid=$_POST['sid'];}

if($sid==-1){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{
	$asstable=array();
	/*
	$d_assessment=mysql_query("SELECT * FROM assessment JOIN
				rideid ON rideid.assessment_id=assessment.id 
				WHERE report_id='$rid' ORDER BY rideid.priority, assessment.label;");
	while($ass=mysql_fetch_array($d_assessment,MYSQL_ASSOC)){
		$asstable['ass'][]=array('name' => ''.$ass['description'],
								 'label' => ''.$ass['label'],
								 'date' => ''.$ass['date'],
								 'element' => ''.$ass['element']);
		}
	*/

	$Students=array();	
	$Students['Student']=array();
	$Student=fetchStudent_short($sid);
	$Targets=(array)fetchTargets($sid);
	$Student['Targets']=$Targets;
	/*
	$Student['Targets']['Target'][]=array('title'=>array('value'=>'Target One - Academic'),'detail'=>array('value'=>$Targets['Target'][1]['value_db']),'status'=>array('value'=>'active'));
	$Student['Targets']['Target'][]=array('title'=>array('value'=>'Target Two – Academic / Extra-curricular'),'detail'=>array('value'=>''),'status'=>array('value'=>'active'));
	$Student['Targets']['Target'][]=array('title'=>array('value'=>'Target Three – Extra-curricular / pastoral'),'detail'=>array('value'=>''),'status'=>array('value'=>'active'));
	*/

	$Students['Student'][]=$Student;
	$Students['Date']=date('Y-m-d');
	$Students['Paper']='landscape';
	$Students['Transform']='';
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>