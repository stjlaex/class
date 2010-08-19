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
	$forms=list_formgroups();
	include('scripts/set_list_vars.php');
	list_select_list($forms,$listoptions,$book);
	unset($listoptions);unset($forms);
?>
