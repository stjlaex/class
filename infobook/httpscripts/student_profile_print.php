<?php
/**						httpscripts/student_profile_print.php
 *
 *
 */

require_once('../../scripts/http_head_options.php');
$book='infobook';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}
elseif(isset($_GET['sids'])){$sids=(array)$_GET['sids'];}
elseif(isset($_SESSION[$book.'sid'])){$sids=array($_SESSION[$book.'sid']);}


if(isset($_POST['template'])){$template=$_POST['template'];}
elseif(isset($_GET['template'])){$template=$_GET['template'];}
else{$template='student_profile_sheet';}


$Students['transform']=$template;
$Students['paper']='portrait';
$Students['homecountry']=strtoupper($CFG->sitecountry);
$Students['Student']=array();

if(isset($sids) and sizeof($sids)>0){

	if(isset($_SERVER['HTTPS'])){
		$http='https';
		}
	else{
		$http='http';
		}

	foreach($sids as $sid){

		$Student=(array)fetchStudent($sid);

		/* Translate a few of the strings... */
		$Student['EnrolNumber']['label']=strtoupper(get_string($Student['EnrolNumber']['label'],'infobook'));
		$PreviousSchool=fetchStudent_singlefield($sid,"EnrolmentPreviousSchool");
		$Student['PreviousSchool']['value']=$PreviousSchool['EnrolmentPreviousSchool']['value'];
		$Student['PersonalNumber']['label']=strtoupper(get_string($Student['PersonalNumber']['label'],'infobook'));

		if($Student['MedicalFlag']['value']=='Y'){
			$Medical=fetchMedical($sid);
			$Notes=$Medical['Notes'];
			while(list($index,$Note)=each($Notes['Note'])){
				if(is_array($Note) and $Note['MedicalRating']['value']==1){
					$Student['Medical'][]=$Note;
					}
				}
			}
		$SEN=fetchStudent_singlefield($sid,"SENFlag");
		$Student['SEN']['value']=get_string(displayEnum($SEN['SENFlag']['value'], 'sen'),'infobook');

		$Student['Language']['value']=get_string(displayEnum($Student['Language']['value'], 'language'),'infobook');
		$Student['SecondLanguage']['value']=get_string(displayEnum($Student['SecondLanguage']['value'], 'language'),'infobook');
		$Student['ThirdLanguage']['value']=get_string(displayEnum($Student['ThirdLanguage']['value'], 'language'),'infobook');

		$Student['TransportMode']['value']=get_string(displayEnum($Student['TransportMode']['value'], 'transportmode'),'infobook');
		$Student['YearGroup']['value']=get_yeargroupname($Student['YearGroup']['value']);
		$Student['Nationality']['value']=get_string(displayEnum($Student['Nationality']['value'], 'nationality'),'infobook');

		$Student['Photo']['url']=$http.'://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory. 
									'/scripts/photo_display.php?epfu='.$Student['EPFUsername']['value'].'&enrolno='.$Student['EnrolNumber']['value'].'&size=midi';

		$Student['Contacts'][0]['Nationality']['value']=get_string(displayEnum($Student['Contacts'][0]['Nationality']['value'], 'nationality'),'infobook');
		$Student['Contacts'][1]['Nationality']['value']=get_string(displayEnum($Student['Contacts'][1]['Nationality']['value'], 'nationality'),'infobook');

		$Students['Student'][]=$Student;
		}


	$returnXML=$Students;
	$rootName='Students';
	}
else{
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
