<?php
/**						list_strandstatus.php
 */

if(!isset($listid)){$listid='strandstatus';}
if(!isset($listname)){$listname='strandstatus';}
if(!isset($listlabel)){$listlabel='usestrands';}
if(!isset($required)){$required='no';}
if(!isset($selstrandstatus)){$selstrandstatus='V';}
include('scripts/set_list_vars.php');
list_select_enum('strandstatus',$listoptions,'reportbook');
unset($listoptions);

?>