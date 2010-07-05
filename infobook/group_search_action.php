<?php 
/**										group_search_action.php
 *
 *
 */

if(isset($_POST['secids'])){$secids=(array)$_POST['secids'];}
if(isset($_POST['yids'])){$yids=(array)$_POST['yids'];}else{$yids=array();}
if(isset($_POST['listtype'])){$listtype=$_POST['listtype'];}else{$listtype='year';}
if(isset($_POST['enrolstatus'])){$enrolstatus=$_POST['enrolstatus'];}

if(isset($enrolstatus)){
	$enrolyear=get_curriculumyear()+1;
	if($enrolstatus=='EN'){$listtype='enquired';}
	elseif($enrolstatus=='AC'){$listtype='accepted';}
	else{$listtype='applied';}
	}

if(isset($secids)){
	$yids=array();
	foreach($secids as $index => $secid){
		$yeargroups=list_yeargroups($secid);
		foreach($yeargroups as $index => $yeargroup){
			$yids[]=$yeargroup['id'];
			}
		}
	}


$students=array();
foreach($yids as $index => $yid){
	if($listtype!='year'){
		$com=array('id'=>'','type'=>$listtype, 
					   'name'=>$enrolstatus.':'.$yid,'year'=>$enrolyear);
		}
	else{
		$com=array('id'=>'','type'=>$listtype,'name'=>$yid);
		}
	$yearstudents=(array)listin_community($com);
	$students=array_merge($students,$yearstudents);
	}

$sids=array();
while(list($index,$student)=each($students)){
	$sids[]=$student['id'];	
	}


$_SESSION['infosids']=$sids;
$_SESSION['infosearchgids']=array();
if(!isset($nolist)){
	$action='student_list.php';
	include('scripts/redirect.php');
	}
?>
