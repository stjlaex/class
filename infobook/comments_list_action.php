<?php
/**							comments_list_action.php
 *
 */

$action='student_view.php';

//$key=array_search($sid,$sids);	

include('scripts/sub_action.php');

if($sub=='edit'){
	$action_post_vars=array('commentid');
	$action='comments_new.php';
	if(isset($_POST['recordid'])){
		$commentid=$_POST['recordid'];
		}
	else{$commentid=-1;}
	}
elseif($sub=='newaction'){
	$action_post_vars=array('commentid');
	$action='comments_new.php';
	if(isset($_POST['recordid'])){
		$commentid=$_POST['recordid'];
		}
	else{$commentid=-1;}
	}
/*
elseif($sub=='Previous'){
	if($key>1){$key=$key-1;}else{$key=0;}
	$sid=$sids[$key];
	$current=$action;
	}
elseif($sub=='Next'){
	$nosids=sizeof($sids);
	if($key<$nosids-1){$key=$key+1;}else{$key=$nosids-1;}
	$sid=$sids[$key];
	$current=$action;
	}
 */

include('scripts/redirect.php');	
?>
