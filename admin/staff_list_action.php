<?php
/**									staff_list_action.php
 */

$action='staff_list.php';


$action_post_vars=array('listsecid','listroles','listoption');

include('scripts/sub_action.php');


if($sub=='list'){
	if(isset($_POST['listsecid'])){$listsecid=$_POST['listsecid'];}else{$listsecid='';}
	if(isset($_POST['listroles'])){$listroles=(array)$_POST['listroles'];}else{$listroles=array();}
	if(isset($_POST['listoption'])){$listoption=$_POST['listoption'];}else{$listoption='current';}
	}
elseif($sub=='export'){
	}

include('scripts/redirect.php');
?>
