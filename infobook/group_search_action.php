<?php 
/**										group_search_action.php
 *
 *
 */

if(isset($_POST['secids'])){$secids=(array)$_POST['secids'];}
if(isset($_POST['yids'])){$yids=(array)$_POST['yids'];}else{$yids=array();}
if(isset($_POST['listtype'])){$listtype=$_POST['listtype'];}else{$listtype='year';}
if(isset($_POST['enrolstatus'])){$enrolstatus=$_POST['enrolstatus'];}
if(isset($_POST['enroldate'])){$enroldate=$_POST['enroldate'];}


if(isset($enrolstatus)){
	$enrolyear=get_curriculumyear()+1;
	if($enrolstatus=='EN'){$listtype='enquired';}
	elseif($enrolstatus=='AC'){$listtype='accepted';}
	elseif($enrolstatus=='C' or $enrolstatus=='P' or $enrolstatus=='L'){
		$listtype='year';
		$AssDefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
		$reenrol_eid=$AssDefs[0]['id_db'];
		}
	else{$listtype='applied';}

	}
	
/* First list the yeargroups to search in yids*/
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

/* Then search the community groups for each yid. */
if(isset($enroldate)){
	if(isset($_POST['enroldate1'])){$startdate=$_POST['enroldate1'];}else{$startdate='';}
	if(isset($_POST['enroldate2'])){$enddate=$_POST['enroldate2'];}else{$enddate='';}
	$currentyear=get_curriculumyear();

	if($enroldate=='leave'){$comtype='alumni';$comname='P:';$comyear=$currentyear;}
	else{$comtype='year';$comname='';$comyear='0000';}
	foreach($yids as $index => $yid){
		$comid=update_community(array('id'=>'','type'=>$comtype,'name'=>$comname.$yid,'year'=>$comyear));
		$com=get_community($comid);
		/* all students who joined the community after startdate and before enddate*/
		$yearstudents=(array)listin_community_new($com,$startdate,$enddate);
		$students=array_merge($students,$yearstudents);
		}
	}
else{
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
	}


/* The list of sids which form the search result. */
$sids=array();
foreach($students as $student){
	$sid=$student['id'];
	if(isset($reenrol_eid)){
		$Assessments=(array)fetchAssessments_short($sid,$reenrol_eid,'G');
		if(sizeof($Assessments)>0 and $Assessments[0]['Result']['value']==$enrolstatus){
			$sids[]=$sid;
			}
		}
	else{
		$sids[]=$sid;
		}
	}


$_SESSION['infosids']=$sids;
$_SESSION['infosearchgids']=array();
if(!isset($nolist)){
	$action='student_list.php';
	include('scripts/redirect.php');
	}
?>
