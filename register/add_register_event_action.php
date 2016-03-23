<?php
/**									add_register_event_action.php
 *
 */

$action='register_list.php';

if(isset($_POST['session'])){$session=$_POST['session'];}else{$session='';}
if(isset($_POST['date'])){$eventdate=$_POST['date'];}else{$eventdate='';}

include('scripts/sub_action.php');

if($sub==''){
    $cancel=$action;
    include('scripts/redirect.php');
    exit;
    }

if($sub=='Submit' and $session!='' and $eventdate!=''){
    set_event($eventdate,$session);
    }

include('scripts/results.php');
include('scripts/redirect.php');
?>
