<?php
/**			       		list_pastoralgroup.php
 *
 * Used to distinguish selections available to year heads or form tutors
 *
 */


if(!isset($onchange)){$selonchange='no';}else{$selonchange=$onchange;}
if(!isset($required)){$required='yes';}
$required='no';

/* On first load select the teacher's pastoral group by default. */
if(sizeof($rhouses)>0){
	$selhouseid=$rhouses[0]['community_id'];
	$selyid=$rhouses[0]['yeargroup_id'];
	if(!in_array($selyid,$ryids)){$ryids[]=$selyid;}
	}
elseif(sizeof($rforms)>0){
	$selformid=$rforms[0]['community_id'];
	}

if($r>-1){
	/* If an acadeic respon is selected then try to list relevant pastoral groups. */
	$ryids=(array)list_course_yeargroups($respons[$r]['course_id']);
	}
else{
	/* Otherwise the users' pastoral respons will be used. */
	}

	print '<div class="left">';

if(sizeof($ryids)>0){
	$ryears=array();
	if(!isset($selyid)){$selyid='';}
	foreach($ryids as $ryid){
		if($ryid>-100){
			$ryears[]=array('id'=>$ryid,'name'=>get_yeargroupname($ryid));
			if($selyid=='' or $selyid==$ryid){$rforms=(array)array_merge($rforms,list_formgroups($ryid));}
		  	}
		}
	$rhouses=list_communities('HOUSE');
	$listlabel='yeargroup';
	$listname='yid';
	$listlabelstyle='eternal';
	$onchange=$selonchange;
	include('scripts/set_list_vars.php');
	//print '<div class="left">';
	list_select_list($ryears,$listoptions,$book);
	//print '</div>';
	unset($listoptions);
	}


if(sizeof($rforms)>0){
	$listlabel='form';
	$listname='formid';
	$listlabelstyle='eternal';
	$onchange=$selonchange;
	include('scripts/set_list_vars.php');
	print '<div class="right">';
	list_select_list($rforms,$listoptions,$book);
	print '</div>';
	unset($listoptions);
	}

	print '</div>';
	print '<div class="right">';

if(sizeof($rhouses)>0){
	$listlabel='house';
	$listlabelstyle='eternal';
	$listname='houseid';
	$onchange=$selonchange;
	include('scripts/set_list_vars.php');
	//print '<div class="right">';
	list_select_list($rhouses,$listoptions,$book);
	//print '</div>';
	unset($listoptions);
	}

	print '</div>';

if(sizeof($rforms)==0 and sizeof($ryids)==0 and sizeof($rhouses)==0){
	print '<label>'.get_string('youhaveno').' '.get_string('pastoralresponsibilities').'</label><br />';
	print '<label>'.get_string('chooseanacademicresponsibility').'</label>';
	}

?>