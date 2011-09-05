<?php
/**									inventory_list_action.php
 */

$action='inventory_list.php';
$budgetyear=$_POST['budgetyear'];
if(isset($_POST['budid'])){$budid=$_POST['budid'];}else{$buid=-1;}

$action_post_vars=array('budgetyear','budid');

include('scripts/sub_action.php');

include('scripts/redirect.php');
?>
