<?php 
/**					list_enrolstatus.php
 *
 */
 
if(!isset($listname)){$listname='enrolstatus';}
if(!isset($listlabel)){$listlabel='enrolstatus';}
include('scripts/set_list_variables.php');
list_select_enum('enrolstatus',$listoptions,$book);
unset($listoptions);
?>



















