<?php 
/**						list_year.php
 *
 * TODO: rename as list_yeargroup
 */

if(!isset($listname)){$listname='newyid';}
if(!isset($listlabel)){$listlabel='yeargroup';}
include('scripts/set_list_vars.php');
if(sizeof($_SESSION['srespons'])>0){
	$yeargroups=list_yeargroups($_SESSION['srespons']);
	}
else{
	$yeargroups=list_yeargroups();
	}
list_select_list($yeargroups,$listoptions,$book);
unset($listoptions);
unset($yeargroups);
?>
