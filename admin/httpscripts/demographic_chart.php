<?php
/**									demographic_chart.php
 *
 */

require_once('../../scripts/http_head_options.php');

$book='admin';
if(isset($_GET['transform'])){$transform=$_GET['transform'];}else{$transform='demographic_chart';}
if(isset($_POST['transform'])){$transform=$_POST['transform'];}


$todate=date('Y-m-d');
$currentyear=get_curriculumyear();
$enrolyear=$currentyear+1;
$yeargroups=list_yeargroups();



$Stats=array();$Stats['Stat']=array();
$Stats['School']['value']=$CFG->schoolname;
$Stats['School']['id']=$CFG->shortname;
$Stats['School']['latlng']=$CFG->sitelatlng[0].','.$CFG->sitelatlng[1];

$Stats['tables']=array();
$Stats['tables']['table']=array();
$Table=array();
$Table['row']=array();
$Table['name']='center1';

$yeargroups=list_yeargroups();
foreach($yeargroups as $year){

	$yid=$year['id'];
	$row=array();
	$row['cell']=array();
	$row['name']=$yid;

	$comid=update_community(array('type'=>'year','name'=>$yid));
	$com=get_community($comid);
	$students=(array)listin_community($com);

	foreach($students as $student){
		$sid=$student['id'];
		$Contacts=(array)fetchContacts($sid);

		foreach($Contacts as $Contact){
			if($Contact['ReceivesMailing']['value']==1){
				$Add=$Contact['Addresses'][0];
				if($Add['Private']['value']!='Y' and $Add['Latitude']['value']>0 and $Add['Latitude']['value']<9999){
					$row['cell'][]=$Add['Latitude']['value'].','.$Add['Longitude']['value'];
					}
				}
			}
		}

	$Table['row'][]=$row;
	}

$Stats['tables']['table'][]=$Table;


/**
 * Two ways the script can be called...
 */
if(isset($transform) and $transform!=''){
	/*
	 * (1) a standard xmlhttprequest for xml and an xslt transform to display the result
	 */
	$Centers=array();
	$Centers['api_key']=$CFG->api_key;
	$Centers['AdmissionCenter']=array();
	$Centers['AdmissionCenter'][]['Stats']=$Stats;
	$Centers['DateStamp']=display_date($todate);
	$Centers['Paper']='landscape';
	$Centers['Transform']=$transform;
	$returnXML=$Centers;
	$rootName='AdmissionCenters';
	}
else{
	/* 
	 * (2) This is repsonse to a curl call and are suppyling only the data to another site. 
	 */
	$returnXML=$Stats;
	$rootName='Stats';
	$xmlechoer=true;
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
