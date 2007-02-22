<?php
/**									incidents_list_action.php
 */

$action='incidents_list.php';

$yid=$Student['NCyearActual']['id_db'];
$id=$_POST['id_db'];
$detail=clean_text($_POST['detail']);
$entrydate=$_POST['entrydate'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='%';}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid='';}
if(isset($_POST['ratvalue'])){$ratvalue=$_POST['ratvalue'];}else{$ratvalue='';}
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid=$yid;}

include('scripts/sub_action.php');

	if($bid==''){$bid='%';}
	$category=$catid[$c] . ':' . $ratvalue . ';';

	if($id!=''){
		if(mysql_query("UPDATE incidents SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category',
		teacher_id='$tid' WHERE id='$id'")){}
		}
	else{
		if(mysql_query("INSERT INTO incidents SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category',
		teacher_id='$tid'")){$result[]=get_string('incidentrecorded',$book);}
		}

	$footer='--'. "\r\n" . get_string('pastoralemailfooterdisclaimer');
	$subject='Incident Report for '.$Student['Forename']['value']
				.' '.$Student['Surname']['value'].' ('. 
					$Student['RegistrationGroup']['value'].')'; 
	$message=$subject."\r\n".'Subject: '.$bid."\r\n".$detail."\r\n". 
				'Posted by '.$tid. "\r\n" .$footer;
	$recipients=findResponsibles($sid,$bid);
	if($recipients and $CFG->emailoff!='yes' and $CFG->emailincidents=='yes'){
		if(sizeof($recipients)>0){
			$headers=emailHeader();
			foreach($recipients as $key => $recipient){
					$recipient['email']=strtolower($recipient['email']);
					if(mail($recipient['email'],$subject,$message,$headers)){
						$result[]=get_string('emailsentto').' '.$recipient['username'];}
					}
			}
		}

//$_SESSION{'Student'}=fetchStudent($sid);
include('scripts/results.php');
include('scripts/redirect.php');	
?>
