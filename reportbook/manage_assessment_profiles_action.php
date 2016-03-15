<?php
/**		       manage_assessment_profiles_action.php
 */

$action='manage_assessment_profiles.php';

$crid=$respons[$r]['course_id'];

include('scripts/sub_action.php');

if($sub=='Submit'){
    if(isset($_POST['name'])){$name=$_POST['name'];}else{$name='';}
    if(isset($_POST['componentstatus']) and $_POST['componentstatus']!='None'){$componentstatus=$_POST['componentstatus'];}else{$componentstatus='';}
    if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='';}
    if(isset($_POST['template'])){$template=$_POST['template'];}else{$template='';}

    if($name!=''){
	mysql_query("INSERT INTO categorydef (name, type, subtype,
		comment, subject_id, course_id) VALUES
		('$name', 'pro', '$componentstatus', '$template', '$bid', '$crid');");
	}
    }

include('scripts/redirect.php');
?>
