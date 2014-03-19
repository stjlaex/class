<?php
/**						list_subjectstatus.php
 */

if(!isset($listid)){$listid='Subjectstatus';}
if(!isset($listname)){$listname='subjectstatus';}
if(!isset($listlabel)){$listlabel='subject';}
if(!isset($required)){$required='yes';}
include('scripts/set_list_vars.php');
list_select_enum('subjectstatus',$listoptions,'reportbook');
unset($listoptions);

$comps=list_course_subjects($rcrid,'A');
$moreinfo='Determine which subjects this will apply to: ';
foreach($comps as $comp){
	$moreinfo.=$comp['status'].'.'.$comp['name'].' ';
	}
?>
		<span class="clicktohelp" title="<?php print $moreinfo;?>" name="help" value="" onclick="" ></span>
