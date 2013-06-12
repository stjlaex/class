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
		$Student['EnrolNumber']['label']=get_string($Student['EnrolNumber']['label'],'infobook');

		$Student['Language']['value']=get_string(displayEnum($Student['Language']['value'], 'language'),'infobook');
		$Student['Nationality']['value']=get_string(displayEnum($Student['Nationality']['value'], 'nationality'),'infobook');

		$Student['Photo']['url']=$http.'://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory. 
									'/scripts/photo_display.php?epfu='.$Student['EPFUsername']['value'].'&enrolno='.$Student['EnrolNumber']['value'];

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