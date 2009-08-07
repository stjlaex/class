<?php
/**                    httpscripts/transfer_students.php
 *
 */

require_once('../../scripts/http_head_options.php');

//if(!isset($xmlid)){print "Failed"; exit;}

$currentyear=get_curriculumyear();
if(isset($_POST['enrolyear']) and $_POST['enrolyear']!=''){$enrolyear=$_POST['enrolyear'];}
else{$enrolyear=$currentyear;}
if(isset($_POST['currentyear']) and $_POST['currentyear']!=''){$remotecurrentyear=$_POST['currentyear'];}
else{$remotecurrentyear=$currentyear;}
if(isset($_POST['feeder_code']) and $_POST['feeder_code']!=''){$feeder_code=$_POST['feeder_code'];}
else{$feeder_code=-1;}
if(isset($_POST['yid'])){$yid=(int)$_POST['yid'];}
else{$yid=-1000;}

	$reenrol_assdefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
	if(isset($reenrol_assdefs[0])){
		$reenrol_eid=$reenrol_assdefs[0]['id_db'];
		}
	else{
		$reenrol_eid=-1;
		}


	$Students=array();
	$Students['Student']=array();
	/* This is a hack to fix a problem with xmlreader -  when only one
	 * student its wrong so add this first duff student every time. 
	 */
	$Students['Student'][]=-1;

	$yeargroupname=get_yeargroupname($yid);
	if($yeargroupname!=''){

		/* Take into account that the two databases may not be in
		 * sync, in fact very likely if year_end has already run for one and not
		 * the other. 
		 */
		$yeardif=$currentyear-$remotecurrentyear;
		$comyid=$yid+$yeardif;

		/* Two possible places to find the transferees depending on
		 *	whether the school has already reached year_end or not.
		 */
		$coms=array();
		$coms[]=array('id'=>'','type'=>'alumni', 
									 'name'=>'P:'.$yid,'year'=>$enrolyear-1);
		$coms[]=array('id'=>'','type'=>'year','name'=>"$comyid");
		$sids=array();
		while(list($cindex,$com)=each($coms)){
			$comid=update_community($com);
			$sids=$sids+list_reenrol_sids($comid,$reenrol_eid,$feeder_code);
			}

		while(list($sindex,$sid)=each($sids)){
			$Student=array();
			$Student=(array)fetchStudent($sid);
			$Student['Contacts'];
			$Student['Comments']=(array)fetchComments($sid,'0000','');
			/* This is a hack to fix a problem with xmlreader -  when only one
			 * entry its wrong so add this first duff record every time. 
			 */
			if(sizeof($Student['Comments']['Comment'])==1){
				$Student['Comments']['Comment'][]=-1;
				}


			/*TODO: Transfer backgrounds
			$Student['Backgrounds']=(array)fetchBackgrounds($sid);
			$Student['Medical']=(array)fetchMedical($sid);
			*/

			$Students['Student'][]=$Student;
			}
		}


$returnXML=$Students;
$rootName='Students';
require_once('../../scripts/http_end_options.php');
exit;
?>
