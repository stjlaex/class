<?php 
/**				   	   				   new_class_action.php
 */

$action='new_class.php';
$action_post_vars=array('bid','curryear');

$curryear=$_POST['curryear'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='';}
if(isset($_POST['newbid'])){$newbid=$_POST['newbid'];}else{$newbid='';}
if(isset($_POST['crid'])){$crid=$_POST['crid'];}else{$crid='';}
if(isset($_POST['overwrite0'])){$overwrite=$_POST['overwrite0'];}

include('scripts/sub_action.php');

if($sub=='Submit'){
	
	$stages=list_course_stages($crid);
	foreach($stages as $stage){
		$stagename=$stage['name'];
		$newclassdef=array('crid'=>$crid,'bid'=>$bid,'stage'=>$stagename);
		$ing=$stagename. '-g';
		if($_POST[$ing]=='forms'){
			$newclassdef['generate']=$_POST[$ing];
			$newclassdef['many']='0'; 
			}
		else{
			$newclassdef['generate']='sets';
			$newclassdef['many']=$_POST[$ing]; 
			}
		$oldclassdef=get_subjectclassdef($crid,$bid,$stagename);
		if($newclassdef['many']!=$oldclassdef['many'] 
					   or $oldclassdef['generate']!=$newclassdef['generate']){
			$d_c=mysql_query("SELECT COUNT(class.id) FROM class JOIN cohort ON class.cohort_id=cohort.id WHERE
				cohort.course_id='$crid' AND cohort.stage='$stagename' AND cohort.year='$curryear' AND class.subject_id='$bid';");
			$currentno=mysql_result($d_c,0);
			if($currentno==0 or ($newclassdef['many']>$currentno 
								 and $oldclassdef['generate']==$newclassdef['generate'])){
				/* This is equivalent to just adding new classes and is no major change*/
				if(isset($oldclassdef['naming'])){$newclassdef['naming']=$oldclassdef['naming'];}
				update_subjectclassdef($newclassdef);
				populate_subjectclassdef($newclassdef);
				$result[]=$stagename.' class/es added.';
				}
			elseif($overwrite=='yes' and $_SESSION['role']=='admin'){
				mysql_query("DELETE FROM tidcid WHERE class_id=ANY(SELECT id FROM class JOIN cohort ON class.cohort_id=cohort.id 
					WHERE class.subject_id='$bid' AND cohort.course_id='$crid' AND cohort.stage='$stagename' AND cohort.year='$curryear');");
				mysql_query("DELETE FROM midcid WHERE class_id=ANY(SELECT id FROM class JOIN cohort ON class.cohort_id=cohort.id 
					WHERE class.subject_id='$bid' AND cohort.course_id='$crid' AND cohort.stage='$stagename' AND cohort.year='$curryear');");
				mysql_query("DELETE FROM cidsid WHERE class_id=ANY(SELECT id FROM class JOIN cohort ON class.cohort_id=cohort.id 
					WHERE class.subject_id='$bid' AND cohort.course_id='$crid' AND cohort.stage='$stagename' AND cohort.year='$curryear');");
				mysql_query("DELETE FROM class WHERE class.subject_id='$bid' AND class.cohort_id=ANY(SELECT id FROM cohort 
					WHERE cohort.course_id='$crid' AND cohort.stage='$stagename' AND cohort.year='$curryear');");
				if(isset($oldclassdef['naming'])){$newclassdef['naming']=$oldclassdef['naming'];}
				update_subjectclassdef($newclassdef);
				populate_subjectclassdef($newclassdef);
				$result[]=$stagename.' class/es changed.';
				}
			else{
				/* Anything else could need midcid, tidcid and cidsid to
				be updated first and so is not going to be done from
				here!!! The changes will only take effect at the start of the next
				curriculum year. */
				$error[]='This change may lose existing MarkBooks - use overwrite option to force changes.';
				}
			}
		}
	}
else{
	if($newbid!=''){
		mysql_query("INSERT INTO component SET course_id='$crid',subject_id='$newbid',status='N';");
		$bid=$newbid;
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>