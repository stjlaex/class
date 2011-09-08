<?php 
/**				   	   				   new_class_action.php
 */

$action='new_class.php';
$action_post_vars=array('bid');

if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='';}
if(isset($_POST['newbid'])){$newbid=$_POST['newbid'];}else{$newbid='';}
if(isset($_POST['crid'])){$crid=$_POST['crid'];}else{$crid='';}
if(isset($_POST['overwrite0'])){$overwrite=$_POST['overwrite0'];}

include('scripts/sub_action.php');

if($sub=='Submit'){
	
	$stages=list_course_stages($crid);
	foreach($stages as $stage){
		$stagename=$stage['name'];
		$ing=$stagename. '-g';
		$inm=$stagename. '-m';
		$newclassdef=array('crid'=>$crid,'bid'=>$bid,'stage'=>$stagename);
		$oldclassdef=get_subjectclassdef($crid,$bid,$stagename);
		$newclassdef['many']=$_POST[$inm]; 
		$newclassdef['generate']=$_POST[$ing];
		if($newclassdef['many']!=$oldclassdef['many'] 
					   or $oldclassdef['generate']!=$newclassdef['generate']){
			$d_c=mysql_query("SELECT COUNT(id) FROM class WHERE
				course_id='$crid' AND subject_id='$bid' AND stage='$stagename';");
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
				mysql_query("DELETE FROM tidcid JOIN class ON tidcid.class_id=class.id 
					WHERE class.subject_id='$bid' AND class.course_id='$crid' AND class.stage='$stagename';");
				mysql_query("DELETE FROM midcid JOIN class ON midcid.class_id=class.id 
					WHERE class.subject_id='$bid' AND class.course_id='$crid' AND class.stage='$stagename';");
				mysql_query("DELETE FROM cidsid JOIN class ON cidsid.class_id=class.id 
					WHERE class.subject_id='$bid' AND class.course_id='$crid' AND class.stage='$stagename';");
				mysql_query("DELETE FROM class  
					WHERE class.subject_id='$bid' AND class.course_id='$crid' AND class.stage='$stagename';");
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