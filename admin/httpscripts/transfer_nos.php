<?php
/**                    httpscripts/transfer_nos.php
 *
 */

require_once('../../scripts/http_head_options.php');

//if(!isset($xmlid)){print "Failed"; exit;}

$currentyear=get_curriculumyear();
if(isset($_POST['enrolyear']) and $_POST['enrolyear']!=''){$enrolyear=$_POST['enrolyear'];}
else{$enrolyear=$currentyear;}
if(isset($_POST['currentyear']) and $_POST['currentyear']!=''){$remotecurrentyear=$_POST['currentyear'];}
else{$remotecurrentyear=$currentyear;}
if(isset($_POST['feeder_code']) and 
   $_POST['feeder_code']!=''){$feeder_code=$_POST['feeder_code'];}
else{$feeder_code=-1;}

	$reenrol_assdefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
	if(isset($reenrol_assdefs[0])){
		$reenrol_eid=$reenrol_assdefs[0]['id_db'];
		}
	else{
		$reenrol_eid=-1;
		}

		/* Take into account that the two databases may not be in
		 * sync, in fact very likely if year_end has already run for one and not
		 * the other. */
		$yeardif=$currentyear-$remotecurrentyear;

//trigger_error($feeder_code.' : '.$enrolyear.' : '.$reenrol_eid,E_USER_WARNING);

	$Transfers=array();
	$Transfers['Transfer']=array();
	$yeargroups=list_yeargroups();
	foreach($yeargroups as $yeargroup){
		$no=0;
		$yid=$yeargroup['id'];
		$comyid=$yid+$yeardif;
		/* Two possible places to find the transferees depending on
			whether the school has already reached year_end or not.*/
		$coms=array();
		$coms[]=array('id'=>'','type'=>'alumni', 
									 'name'=>'P:'.$yid,'year'=>$enrolyear-1);
		$coms[]=array('id'=>'','type'=>'year','name'=>(string)$comyid);
		foreach($coms as $com){
			$comid=update_community($com);
			$no+=count_reenrol_no($comid,$reenrol_eid,$feeder_code);
			}
		$Transfer=array('Yeargroup'=>$yid,'value'=>$no);
		$Transfers['Transfer'][]=$Transfer;
		}

$returnXML=$Transfers;
$rootName='Transfers';
require_once('../../scripts/http_end_options.php');
exit;
?>

















