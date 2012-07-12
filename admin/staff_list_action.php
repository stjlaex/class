<?php
/**									staff_list_action.php
 */

$action='staff_list.php';


$action_post_vars=array('listsecids','listroles');

include('scripts/sub_action.php');


if($sub=='list'){
	if(isset($_POST['listsecids'])){$listsecids=(array)$_POST['listsecids'];}else{$listsecids=array();}
	if(isset($_POST['listroles'])){$listroles=(array)$_POST['listroles'];}else{$listroles=array();}
	}

include('scripts/redirect.php');
?>
