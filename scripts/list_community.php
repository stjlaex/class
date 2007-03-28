<?php
/**										scripts/list_community.php
 *
 * $multi>1 returns newcomids[] or $multi=1 returns newcomid (default=10)
 * set $required='no' to make not required (default=yes)
 * first call returns newcomid, second call returns newcomid1
 */

if(!isset($listname)){$listname='newcomid';}
if(!isset($listlabel)){$listlabel='communities';}
include('scripts/set_list_variables.php');

	$listcomtypes=array();
	if(!isset($type)){$listcomtypes[]='year';}
	else{$listcomtypes[]=$type;}
	if($listcomtypes[0]=='year' and ($_SESSION['role']=='office' 
						   or $_SESSION['role']=='admin')){
		$listcomtypes[]='enquired';$listcomtypes[]='applied';$listcomtypes[]='accepted';
		}

	$listcomids=array();
	while(list($index,$listtype)=each($listcomtypes)){
		$listcoms=array();
   		$listcoms=(array)list_communities($listtype);
		while(list($index,$listcom)=each($listcoms)){
			$listcomids[$listcom['id']]=$listcom;
			}
		}

list_select_list($listcomids,$listoptions,$book);
unset($listoptions);
?>