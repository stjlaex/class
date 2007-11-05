<?php
/**						list_componentstatus.php
 */

if(!isset($listid)){$listid='Componentstatus';}
if(!isset($listname)){$listname='componentstatus';}
if(!isset($listlabel)){$listlabel='usesubjectcomponents';}
if(!isset($required)){$required='yes';}
include('scripts/set_list_vars.php');
list_select_enum('componentstatus',$listoptions,'reportbook');
unset($listoptions);
?>
