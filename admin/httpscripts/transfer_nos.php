<?php
/**                    httpscripts/transfer_nos.php
 *
 */

require_once('../../scripts/http_head_options.php');

//if(!isset($xmlid)){print "Failed"; exit;}

$currentyear=get_curriculumyear();
if(isset($_POST['enrolyear']) and $_POST['enrolyear']!=''){$enrolyear=$_POST['enrolyear'];}
else{$enrolyear=$currentyear;}
if(isset($_POST['feeder_code']) and 
   $_POST['feeder_code']!=''){$feeder_code=$_POST['feeder_code'];}
else{$feeder_code=-1;}

	$reenrol_assdefs=fetch_enrolmentAssessmentDefinitions('',$feeder_code,$enrolyear);
	if(isset($reenrol_assdefs[0])){
		$reenrol_eid=$reenrol_assdefs[0]['id_db'];
		}
	else{
		$reenrol_eid=-1;
		}

	$Transfers=array();
	$Transfers['Transfer']=array();
	$yeargroups=list_yeargroups();
	while(list($yearindex,$yeargroup)=each($yeargroups)){
		$yid=$yeargroup['id'];
		$yearcom=array('id'=>'','type'=>'year', 
					   'name'=>$yid);
		$yearcomid=update_community($yearcom);
		$no=count_reenrol_no($yearcomid,$reenrol_eid,$feeder_code);
		$Transfer=array('Yeargroup'=>$yid,'value'=>$no);
		$Transfers['Transfer'][]=$Transfer;
		}

$returnXML=$Transfers;
$rootName='Transfers';
require_once('../../scripts/http_end_options.php');
exit;
?>

















