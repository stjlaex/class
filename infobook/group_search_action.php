<?php 
/**										group_search_action.php
 *
 *
 */

if(isset($_POST['secids'])){$secids=(array)$_POST['secids'];}
if(isset($_POST['yids'])){$yids=(array)$_POST['yids'];}else{$yids=array();}
//if(isset($_POST['listtypes'])){$listtypes=$_POST['listtypes'];}else{$listtypes[]='year';}
if(isset($_POST['enrolstatuses']) and $_POST['enrolstatuses'][0]!='uncheck'){$enrolstatuses=(array)$_POST['enrolstatuses'];}
if(isset($_POST['enroldate']) and $_POST['enroldate']!='uncheck'){$enroldate=$_POST['enroldate'];}

$listtypes=array();

if(isset($enrolstatuses)){
	$enrolyear=get_curriculumyear()+1;
	foreach($enrolstatuses as $enrolstatus){
		if($enrolstatus=='EN'){$listtypes[]='enquired';}
		elseif($enrolstatus=='AC'){$listtypes[]='accepted';}
		elseif($enrolstatus=='C' or $enrolstatus=='P' or $enrolstatus=='L'){
			$listtypes[]='year';
			$AssDefs=(array)fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
			$reenrol_eid=$AssDefs[0]['id_db'];
			}
		else{$listtypes[]='applied';}
		}
	}
else{
	$listtypes[]='year';
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

/* Default to the whole school if no sections etc selected. */
if(sizeof($yids)==0){
	$yeargroups=list_yeargroups();
	foreach($yeargroups as $index => $yeargroup){
		$yids[]=$yeargroup['id'];
		}
	}


$students=array();

/* Then search the community groups for each yid. */
if(isset($enroldate)){
	if(isset($_POST['enroldate1'])){$startdate=$_POST['enroldate1'];}else{$startdate='';}
	if(isset($_POST['enroldate2'])){$enddate=$_POST['enroldate2'];}else{$enddate='';}
	$currentyear=get_curriculumyear();
	$todate=date("Y-m-d");

	$comtype='year';$comname='';$comyear='0000';
	if($enroldate=='leave'){
		/* Leaving in the future and only searching across accepted applications*/
		if($startdate!='' and $startdate<$todate){$comtype='alumni';$comname='P:';$comyear=$currentyear;}
		elseif($startdate>=$todate){$extra="$startdate<=info.leavingdate AND info.leavingdate!='0000-00-00'";}
		elseif($enddate>=$todate){$extra="$enddate<=info.leavingdate AND info.leavingdate!='0000-00-00'";}
		}
	elseif($enroldate=='start'){
		/* Joining in the future and only searching across accepted applications*/
		$comtype='accepted';$comname='AC:';$comyear=$currentyear;
		if($startdate!='' and $startdate>=$todate){$extra="$startdate<=info.entrydate AND info.entrydate!='0000-00-00'";}
		elseif($enddate!='' and $enddate>=$todate){$extra="$enddate<=info.entrydate AND info.entrydate!='0000-00-00'";}
		}

	foreach($yids as $index => $yid){

		$comid=update_community(array('id'=>'','type'=>$comtype,'name'=>$comname.$yid,'year'=>$comyear));
		$com=get_community($comid);

		/* all students who joined the community after startdate and before enddate*/
		if(!$extra){
			$yearstudents=(array)listin_community_new($com,$startdate,$enddate);
			}
		else{
			$yearstudents=(array)listin_community_extra($com,$extra);
			}

		$students=array_merge($students,$yearstudents);

		}


	}
else{
	foreach($yids as $yid){
		foreach($listtypes as $index => $listtype){
			trigger_error($yid.' : '.$enrolstatuses[$index],E_USER_WARNING);
			if($listtype!='year'){
				$com=array('id'=>'','type'=>$listtype, 
						   'name'=>$enrolstatuses[$index].':'.$yid,'year'=>$enrolyear);
				}
			else{
				$com=array('id'=>'','type'=>$listtype,'name'=>$yid);
				}
			$yearstudents=(array)listin_community($com);
			
			$students=array_merge($students,$yearstudents);
			}
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
