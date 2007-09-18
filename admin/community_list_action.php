<?php 
/**									 community_list_action.php
 */

$action='community_list.php';
$action_post_vars=array('type','comid','enrolyear','date');

if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['type'])){$type=$_POST['type'];}
if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}
else{$sids=array();}
if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}
if(isset($_POST['date'])){$date=$_POST['date'];}

include('scripts/sub_action.php');

if($sub=='Submit'){

	$com=get_community($comid);
	$comtype=$com['type'];
	if($comtype=='applied' or $comtype=='enquired' or 
	   $comtype=='accepted'){
		$enrolyear=$com['year'];
		list($enrolstatus,$yid)=split(':',$com['name']);
		if(isset($_POST['enrolstatus'])){$newenrolstatus=$_POST['enrolstatus'];}
		
		/*see student_view_enrolment_action for the same - needs to be moved out*/
		/*crucial to th elogic of enrolments*/
		if($newenrolstatus=='EN'){$newtype='enquired';}
		elseif($newenrolstatus=='AC'){$newtype='accepted';}
		else{$newtype='applied';}
		$newcom=array('id'=>'','type'=>$newtype, 
					  'name'=>$newenrolstatus.':'.$yid,'year'=>$enrolyear);
		if($newenrolstatus=='C'){
			$newcom=array('id'=>'','type'=>'year', 'name'=>$yid);
			}
		/**/
		}

	if(isset($newcom)){
		/*sids selected to move*/
		while(list($index,$sid)=each($sids)){
			$oldcommunities=join_community($sid,$newcom);
			}
		}
	}

include('scripts/redirect.php');
?>
