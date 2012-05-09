<?php
/**									fees_invoice_list_action.php
 */

$action='fees_remittance_list.php';
$feeyear=$_POST['feeyear'];
$action_post_vars=array('feeyear');


include('scripts/sub_action.php');


include('scripts/redirect.php');
?>
