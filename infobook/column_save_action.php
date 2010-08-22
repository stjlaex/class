<?php
/**									column_save__action.php
 *
 */

$action='student_list.php';
$action_post_vars=array('savedview');

include('scripts/sub_action.php');

$displayfields=array();
$displayfields_no=$_POST['colno'];
$savedview=$_POST['name'];
for($dindex=0;$dindex < ($displayfields_no);$dindex++){
	trigger_error($savedview.' NO '.$dindex.' ',E_USER_WARNING);
	if(isset($_POST['displayfield'.$dindex])){
		$displayfields[$dindex]=$_POST['displayfield'.$dindex];
		if(!isset($taglist)){$taglist=$displayfields[$dindex];}
		else{$taglist.=':::'.$displayfields[$dindex];}
		}
	}


if($sub=='Submit'){
	mysql_query("INSERT INTO categorydef SET name='$savedview', type='col', comment='$taglist';");
	trigger_error($savedview.' '.$taglist.' ',E_USER_WARNING);
	}


include('scripts/results.php');	
include('scripts/redirect.php');	
?>
