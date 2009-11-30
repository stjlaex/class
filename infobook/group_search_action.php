<?php 
/**										group_search_action.php
 *
 *
 */

$action=$choice;
$action_post_vars=array('groupsearch');
/* Lets the choice page know these sids came from group search. */
$groupsearch='yes';

if(isset($_POST['secids'])){$secids=(array)$_POST['secids'];}
if(isset($_POST['yids'])){$yids=(array)$_POST['yids'];}else{$yids=array();}

include('scripts/sub_action.php');

if(isset($secids)){
	$yids=array();
	foreach($secids as $index => $secid){
		$yeargroups=list_yeargroups($secid);
		foreach($yeargroups as $index => $yeargroup){
			$yids[]=$yeargroup['id'];
			}
		}
	}

$students=array();
foreach($yids as $index => $yid){
	$com=array('id'=>'','type'=>'year','name'=>$yid);
	$yearstudents=(array)listin_community($com);
	$students=array_merge($students,$yearstudents);
	}

$sids=array();
while(list($index,$student)=each($students)){
	$sids[]=$student['id'];
	}

$_SESSION['infosids']=$sids;
$_SESSION['infosearchgids']=array();
include('scripts/redirect.php');	

?>
