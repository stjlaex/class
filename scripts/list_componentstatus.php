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

$comps=list_subject_components('%',$rcrid,'A');
$moreinfo='Determine which subject components this assessment will apply to: ';
foreach($comps as $index => $comp){
	$moreinfo.=$comp['status'].'.'.$comp['name'].' ';
	}
?>
		<div style="float:left;" title="<?php print $moreinfo;?>" 
			name="help" value="" onclick="" >
		<img class="clicktohelp" />
		</div>
