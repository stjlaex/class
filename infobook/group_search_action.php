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
if(isset($_POST['transportmodes']) and $_POST['transportmodes']!=''){$transportmodes=(array)$_POST['transportmodes'];}else{$transportmodes=array();}
if(isset($_POST['limit']) and $_POST['limit']!='uncheck'){$limit=$_POST['limit'];}else{$limit='';}

$action_post_vars=array('selsavedview');

$listtypes=array();
if(isset($enrolstatuses)){
	$enrolyear=$_POST['enrolyear'];
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
	foreach($secids as $secid){
		$yeargroups=list_yeargroups($secid);
		foreach($yeargroups as $yeargroup){
			$yids[]=$yeargroup['id'];
			}
		}
	}

/* Default to the whole school if no sections etc selected. */
if(sizeof($yids)==0){
	$yeargroups=list_yeargroups();
	foreach($yeargroups as $yeargroup){
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

	$totime=strtotime($todate);
	$starttime=strtotime($startdate);
	$endtime=strtotime($enddate);

	$comtype='year';
	$comname='';
	$comyear='0000';

	trigger_error($currentyear.' : '.$startdate.' : '.$enroldate,E_USER_WARNING);

	if($enroldate=='leave'){
		/* Left in the past and could be for a different academic year. */
		if($startdate!='' and $starttime < $totime){
			$comtype='alumni';$comname='P:';
			if($starttime > mktime(0,0,0,$CFG->enrol_cutoffmonth+1,1,$currentyear-1)){
				$comyear=$currentyear;
				}
			else{
				$comyear=$currentyear-1;
				}
			}

		/* Leaving in the future and only searching across accepted applications */
		elseif($startdate>=$todate){$extra="'$startdate'<=info.leavingdate AND info.leavingdate!='0000-00-00'";}
		elseif($enddate>=$todate){$extra="'$enddate'<=info.leavingdate AND info.leavingdate!='0000-00-00'";}

		//trigger_error($comyear.' : '.$extra,E_USER_WARNING);

		}
	elseif($enroldate=='start'){
		if($startdate!='' and $starttime>=$totime){
			/* Joining in the future and only searching across accepted applications*/
			$comtype='accepted';$comname='AC:';$comyear=$currentyear;
			$extra="'$startdate'<=info.entrydate AND info.entrydate!='0000-00-00'";
			}
		elseif($startdate!='' and $starttime<$totime){
			/* Joined in the past and only searching current*/
			$extra="'$startdate'<=info.entrydate AND info.entrydate!='0000-00-00'";
			}
		elseif($enddate!='' and $endtime>=$totime){
			/* Joining in the future and only searching current */
			$comtype='accepted';$comname='AC:';$comyear=$currentyear;
			$extra="'$enddate'<=info.entrydate AND info.entrydate!='0000-00-00'";
			}
		elseif($enddate!='' and $endtime<$totime){
			/* Joined in the pasted and only searching current */
			$extra="'$enddate'<=info.entrydate AND info.entrydate!='0000-00-00'";
			}
		}

	foreach($yids as $yid){

		//trigger_error($yid.' ::: '.$comyear.' : '.$extra,E_USER_WARNING);

		$comid=update_community(array('id'=>'','type'=>$comtype,'name'=>$comname.$yid,'year'=>$comyear));
		$com=get_community($comid);

		/* all students who joined the community after startdate and before enddate*/
		if(!isset($extra)){
			$yearstudents=(array)listin_community_new($com,$startdate,$enddate);
			}
		else{
			$yearstudents=(array)listin_community_extra($com,$extra);
			}

		//$yearstudents=(array)listin_community_new($com);
		
		$students=array_merge($students,$yearstudents);
		}


	}
else{
	foreach($yids as $yid){
		foreach($listtypes as $index => $listtype){
			//trigger_error($enrolyear.' : '.$yid.' : '.$enrolstatuses[$index],E_USER_WARNING);
			if($listtype!='year'){
				$com=array('id'=>'','type'=>$listtype, 
						   'name'=>$enrolstatuses[$index].':'.$yid,'year'=>$enrolyear);
				$selsavedview='enrolment';
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
$sids_index[]=array();
foreach($students as $student){
	$sid=$student['id'];
	if(isset($reenrol_eid)){
		$Assessments=(array)fetchAssessments_short($sid,$reenrol_eid,'G');
		/* Filter for their re-enrolment status but need extra option for LL when enrolstatus is L to get all leavers. */
		if(sizeof($Assessments)>0 and ($Assessments[0]['Result']['value']==$enrolstatus or ($Assessments[0]['Result']['value']=='LL' and $enrolstatus=='L'))){
			$sids[]=$sid;
			}
		}
	else{
		$selectstudent=false;
		$transportmodescount=count($transportmodes);
		$student_transportmode=fetchStudent_singlefield($sid,'TransportMode');
		foreach($transportmodes as $transportmode){
			if($student_transportmode['TransportMode']['value']==$transportmode){$selectstudent=true;}
			}
		if(($transportmodescount>0 and $selectstudent) or $transportmodescount==0){
			if($limit=='Y'){
				$Contacts=(array)fetchContacts($sid);
				foreach($Contacts as $cindex => $Contact){
					$Siblings=fetchDependents($Contact['id_db']);
					$Youngest=(array)array_pop($Siblings['Dependents']);
					if(!isset($sids_index[$Youngest['id_db']])){
						$sids[]=$Youngest['id_db'];
						$sids_index[$Youngest['id_db']]=$Youngest['id_db'];
						}
					}
				}
			elseif($limit=='E'){
				$Contacts=(array)fetchContacts($sid);
				foreach($Contacts as $cindex => $Contact){
					$Siblings=fetchDependents($Contact['id_db']);
					$Eldest=(array)array_shift($Siblings['Dependents']);
					if(!isset($sids_index[$Eldest['id_db']])){
						$sids[]=$Eldest['id_db'];
						$sids_index[$Eldest['id_db']]=$Eldest['id_db'];
						}
					}
				}
			else{
				$sids[]=$sid;
				}
			}
		}
	}


$_SESSION['infosids']=$sids;
$_SESSION['infosearchgids']=array();
if(!isset($nolist)){
	$action='student_list.php';
	include('scripts/redirect.php');
	}
?>
