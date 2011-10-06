<?php
/**			       		list_pastoralgroup.php
 *
 * Used to distinguish selections available to year heads or form tutors
 */


if(!isset($onchange)){$selonchange='no';}else{$selonchange=$onchange;}
if(!isset($required)){$required='yes';}
$required='no';

/* On first load select the teacher's pastoral group by default. */
if(sizeof($rhouses)>0){
	$selhouseid=$rhouses[0]['community_id'];
	$selyid=$rhouses[0]['yeargroup_id'];
	}
elseif(sizeof($rforms)>0){
	$selformid=$rforms[0]['community_id'];
	}


if(sizeof($ryids)>0){
	if(!isset($selyid)){$selyid=$ryids[0];}
	foreach($ryids as $ryid){
		if($ryid>-100){
			$years[]=array('id'=>$ryid,'name'=>get_yeargroupname($ryid));
			$rforms=(array)array_merge($rforms,list_formgroups($ryid));
		  	}
		}
	$houses=list_communities('HOUSE');
	$listlabel='yeargroup';
	$listname='yid';
	$onchange=$selonchange;
	include('scripts/set_list_vars.php');
	print '<div class="left">';
	list_select_list($years,$listoptions,$book);
	print '</div>';
	unset($listoptions);
	}


if(sizeof($rforms)>0){
	$listlabel='form';
	$listname='formid';
	$onchange=$selonchange;
	include('scripts/set_list_vars.php');
	print '<div class="right">';
	list_select_list($rforms,$listoptions,$book);
	print '</div>';
	unset($listoptions);
	}

if(sizeof($houses)>0){
	$listlabel='house';
	$listname='houseid';
	$onchange=$selonchange;
	include('scripts/set_list_vars.php');
	print '<div class="left">';
	list_select_list($houses,$listoptions,$book);
	print '</div>';
	unset($listoptions);
	}

if(sizeof($rforms)==0 and sizeof($ryids)==0 and sizeof($rhouses)==0){
	print '<label>'.get_string('youhavenopastoralresponsibilities').'</label>';
	}


?>