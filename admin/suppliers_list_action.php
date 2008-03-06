<?php
/**									suppliers_list_action.php
 */

$action='suppliers_list.php';
$budgetyear=$_POST['budgetyear'];
$action_post_vars=array('budgetyear');

include('scripts/sub_action.php');

include('scripts/redirect.php');
?>
