<?php 
/**						list_section.php
 *
 */

if(!isset($listname)){$listname='newsecid';}
if(!isset($listlabel)){$listlabel='section';}
include('scripts/set_list_vars.php');
if(sizeof($_SESSION['srespons'])>0){
	$sections=list_sections(false,$_SESSION['srespons']);
	}
else{
	$sections=list_sections();
	}
list_select_list($sections,$listoptions,$book);
unset($listoptions);
unset($sections);
?>
