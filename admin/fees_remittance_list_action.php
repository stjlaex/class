<?php
/**									fees_concept_list_action.php
 */

$action='fees_concept_list.php';
$feeyear=$_POST['feeyear'];
$action_post_vars=array('feeyear');

include('scripts/sub_action.php');

include('scripts/redirect.php');
?>
