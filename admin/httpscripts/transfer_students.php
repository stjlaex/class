<?php
/**                    httpscripts/transfer_students.php
 *
 */

require_once('../../scripts/http_head_options.php');

//if(!isset($xmlid)){print "Failed"; exit;}

$currentyear=get_curriculumyear();
if(isset($_POST['enrolyear']) and $_POST['enrolyear']!=''){$enrolyear=$_POST['enrolyear'];}
else{$enrolyear=$currentyear;}
if(isset($_POST['feeder_code']) and $_POST['feeder_code']!=''){$feeder_code=$_POST['feeder_code'];}
else{$feeder_code=-1;}
if(isset($_POST['yid']) and $_POST['yid']!=''){$yid=$_POST['yid'];}
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
		student its wrong so add this first duff student every time. */
	$Students['Student'][]=-1;

	$yeargroupname=get_yeargroupname($yid);
	if($yeargroupname!=''){
		$yearcom=array('id'=>'','type'=>'year','name'=>$yid);
		$yearcomid=update_community($yearcom);
		$sids=(array)list_reenrol_sids($yearcomid,$reenrol_eid,$feeder_code);
		while(list($sindex,$sid)=each($sids)){
			$Student=array();
			$Student=(array)fetchStudent($sid);
			unset($Student['Contacts']);
			$Student['Comments']=(array)fetchComments($sid,'0000','');
			/* This is a hack to fix a problem with xmlreader -  when only one
				entry its wrong so add this first duff record every time. */
			if(sizeof($Student['Comments']['Comment'])==1){
				$Student['Comments']['Comment'][]=-1;
				}


			/*TODO: Transfer backgrounds
			$Student['Backgrounds']=(array)fetchBackgrounds($sid);
			$Student['Medical']=(array)fetchMedical($sid);
			*/

			$Students['Student'][]=$Student;
			//trigger_error('TRANSFER '.$yid. ' : '.$Student['Surname']['value'].' : '.$sid,E_USER_WARNING);
			}
		}


$returnXML=$Students;
$rootName='Students';
require_once('../../scripts/http_end_options.php');
exit;
?>
