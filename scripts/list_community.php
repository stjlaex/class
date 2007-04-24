<?php
/**										scripts/list_community.php
 *
 * set $type to list communities of that type
 * will default to type=year
 * special $type=admissions to list all communities not on roll
 */

if(!isset($listname)){$listname='newcomid';}
if(!isset($listlabel)){$listlabel='communities';}
include('scripts/set_list_variables.php');

	$listcomtypes=array();
	if(!isset($type)){
		$listcomtypes[]='year';
		if($_SESSION['role']=='office' 
						   or $_SESSION['role']=='admin'){
			$listcomtypes[]='enquired';
			$listcomtypes[]='applied';
			$listcomtypes[]='accepted';
			$listcomtypes[]='alumni';
			}
		}
	elseif($type=='admissions'){
		$listcomtypes[]='academic';
		$listcomtypes[]='enquired';
		$listcomtypes[]='applied';
		$listcomtypes[]='accepted';
		$listcomtypes[]='alumni';
		}
	else{$listcomtypes[]=$type;}


	$listcomids=array();
	while(list($index,$listtype)=each($listcomtypes)){
		$listcoms=array();
   		$listcoms=(array)list_communities($listtype);
		while(list($index,$listcom)=each($listcoms)){
			$listcomids[$listcom['id']]=$listcom;
			/*a fix to display something meaningful until detail is used*/
			$listcomids[$listcom['id']]['name']=$listcomids[$listcom['id']]['type'] . 
												': '.$listcomids[$listcom['id']]['name'];
			}
		}

list_select_list($listcomids,$listoptions,$book);
unset($listoptions);
?>