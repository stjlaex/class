<?php
/**									ents_list_action.php
 *
 */

$action='ents_list.php';
$action_post_vars=array('tagname');

if(isset($_POST['tagname'])){$tagname=$_POST['tagname'];}

include('scripts/sub_action.php');

if($sub=='edit'){
	$action_post_vars=array('tagname','entid');
	$action='ents_new.php';
	if(isset($_POST['recordid'])){
		$entid=$_POST['recordid'];
		}
	else{$entid=-1;}
	}

include('scripts/redirect.php');
?>
