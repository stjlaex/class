<?php
/**										scripts/list_community.php
 *
 * Set the $listtype to list communities of that type
 * and will default to listtype=yeargroups if not set.
 * Special $listtype=admissions to list all communities not on roll and
 * another appropriate to the InfoBook side-bar search.
 */

if(!isset($listname)){$listname='newcomid';}
if(!isset($listlabel)){$listlabel='communities';}
include('scripts/set_list_vars.php');

	$listcomtypes=array();
	if(!isset($listtype)){
		$listcomtypes[]='academic';
		$listcomtypes[]='accomodation';
		$listcomtypes[]='year';
	  		if($_SESSION['role']=='office' 
						   or $_SESSION['role']=='admin'){
			$listcomtypes[]='accepted';
			}
		}
	elseif($listtype=='yeargroups'){
		$listcomtypes[]='year';
	  		if($_SESSION['role']=='office' 
						   or $_SESSION['role']=='admin'){
			$listcomtypes[]='accepted';
			}
		}
	elseif($listtype=='admissions'){
		$listcomtypes[]='enquired';
		$listcomtypes[]='applied';
		$listcomtypes[]='accepted';
		$listcomtypes[]='alumni';
		}
	elseif($listtype=='infosearch'){
		$listcomtypes[]='accomodation';
		$listcomtypes[]='academic';
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
			if($listcomids[$listcom['id']]['detail']!=''){
				$listcomids[$listcom['id']]['name']=$listcomids[$listcom['id']]['detail'];
				}
			elseif($listtype=='applied' or $listtype=='enquired' or $listtype=='accepted'){
				$listcomids[$listcom['id']]['name']=$display . 
												' - '.$listcomids[$listcom['id']]['name'].
												' '.$listcomids[$listcom['id']]['year'];
				}
			else{
				$listcomids[$listcom['id']]['name']=$display . 
						' - '.$listcomids[$listcom['id']]['name'];
				}
			}
		}

list_select_list($listcomids,$listoptions,$book);
unset($listoptions);
?>