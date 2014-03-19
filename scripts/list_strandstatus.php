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

$comps=list_subject_components('%',$rcrid,'A');
$moreinfo='Determine which strands will be assessed separately: 
';
foreach($comps as $comp){
	$strands=list_subject_components($comp['id'],$rcrid,'A');
	foreach($strands as $strand){
		$moreinfo.=$strand['status'].'.'.$strand['name'].' 
';
		}
	}
?>
		  <span class="clicktohelp" title="<?php print $moreinfo;?>" name="help" value="" onclick="" ></span>
