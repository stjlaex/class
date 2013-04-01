<?php
/**								list_form.php
 *
 *	returns $newfid
 */
	if(!isset($selfid)){$selfid='';}
	if(isset($fid)){$selfid=$fid;}
	if(isset($newfid)){$selfid=$newfid;}
	if(!isset($listname)){$listname='newfid';}
	if(!isset($listlabel)){$listlabel='formgroup';}
	if(sizeof($_SESSION['srespons'])>0){
		$yeargroups=list_yeargroups($_SESSION['srespons']);
		$forms=array();
		foreach($yeargroups as $yeargroup){
			$forms=array_merge($forms,list_formgroups($yeargroup['id']));
			}
		}
	else{
		$forms=list_formgroups();
		}
	include('scripts/set_list_vars.php');
	list_select_list($forms,$listoptions,$book);
	unset($listoptions);
	unset($forms);
	unset($yeargroup);
	unset($yeargroups);
?>
