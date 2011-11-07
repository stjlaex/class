<?php
/**									column_save_action.php
 *
 */

$action='student_list.php';
$action_post_vars=array('savedview');

include('scripts/sub_action.php');

$displayfields=array();
$displayfields_no=$_POST['colno'];
$savedview=$_POST['name'];

trigger_error($sub,E_USER_WARNING);

if($sub=='Submit'){
	for($dindex=0;$dindex < ($displayfields_no);$dindex++){
		trigger_error($savedview.' NO '.$dindex.' ',E_USER_WARNING);
		if(isset($_POST['displayfield'.$dindex])){
			$displayfields[$dindex]=$_POST['displayfield'.$dindex];
			if(!isset($taglist)){$taglist=$displayfields[$dindex];}
			else{$taglist.=':::'.$displayfields[$dindex];}
			}
		}
	if($savedview!=''){
		mysql_query("INSERT INTO categorydef SET name='$savedview', type='col', comment='$taglist';");
		}
	}
else{

	$catids=(array)$_POST['catids'];
	foreach($catids as $catid){
		mysql_query("DELETE FROM categorydef WHERE id='$catid' AND type='col';");
		}

	}

include('scripts/redirect.php');	
?>
