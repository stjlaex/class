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
	if(!isset($listtype)){
		$listcomtypes[]='year';
		if($_SESSION['role']=='office' 
						   or $_SESSION['role']=='admin'){
			$listcomtypes[]='enquired';
			$listcomtypes[]='applied';
			$listcomtypes[]='accepted';
			$listcomtypes[]='alumni';
			}
		}
	elseif($listtype=='admissions'){
		$listcomtypes[]='academic';
		$listcomtypes[]='accomodation';
		$listcomtypes[]='enquired';
		$listcomtypes[]='applied';
		$listcomtypes[]='accepted';
		$listcomtypes[]='alumni';
		}
	else{$listcomtypes[]=$listtype;}


	$listcomids=array();
	while(list($index,$listtype)=each($listcomtypes)){
		$listcoms=array();
		$display=get_string($listtype);
   		$listcoms=(array)list_communities($listtype);
		while(list($index,$listcom)=each($listcoms)){
			$listcomids[$listcom['id']]=$listcom;
			/*a fix to display something meaningful until detail is used*/
			if($listtype!='year'){
				$listcomids[$listcom['id']]['name']=$display . 
												' - '.$listcomids[$listcom['id']]['name'];
				}
			else{
				$listcomids[$listcom['id']]['name']=$listcomids[$listcom['id']]['detail'];
				}
			}
		}

list_select_list($listcomids,$listoptions,$book);
unset($listoptions);
?>