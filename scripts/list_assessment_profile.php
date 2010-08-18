<?php
/**										scripts/list_assessment_profile.php
 *
 * Lists relevant assessment profiles based on selected academic
 * responsibility.
 */

if(!isset($required)){$required='yes';}
if(!isset($listname)){$listname='profid';}
if(!isset($listlabel)){$listlabel='assessmentprofile';}
if(!isset($profid)){$selprofid='';}else{$selprofid=$profid;}
if(!isset($profiles)){$profiles=(array)list_assessment_profiles($rcrid);}
include('scripts/set_list_vars.php');
list_select_list($profiles,$listoptions,$book);
unset($listoptions);
?>