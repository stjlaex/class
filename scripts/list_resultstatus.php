<?php
/**						list_resultstatus.php
 */

if(!isset($listid)){$listid='Resultstatus';}
if(!isset($listname)){$listname='resultstatus';}
if(!isset($listlabel)){$listlabel='status';}
if(!isset($required)){$required='yes';}
if(!isset($resultstatus)){$resultstatus='R';}
include('scripts/set_list_vars.php');
list_select_enum('resultstatus',$listoptions,'reportbook');
unset($listoptions);
?>
