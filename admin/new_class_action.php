<?php 
/**				   	   				   new_class_action.php
 */

$action='new_class.php';
$action_post_vars=array('bid');

if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='';}
if(isset($_POST['crid'])){$crid=$_POST['crid'];}else{$crid='';}

include('scripts/sub_action.php');

if($sub=='Submit'){
	$stages=list_course_stages($crid);
	while(list($index,$stage)=each($stages)){
		$stagename=$stage['name'];
		$ing=$stagename. '-g';
		$inm=$stagename. '-m';
		$newclassdef=array('crid'=>$crid,'bid'=>$bid,'stage'=>$stagename);
		$oldclassdef=get_subjectclassdef($crid,$bid,$stagename);
		$newclassdef['many']=$_POST[$inm]; 
		$newclassdef['generate']=$_POST[$ing];
		if($newclassdef['many']!=$oldclassdef['many'] 
					   or $oldclassdef['generate']!=$newclassdef['generate']){
			update_subjectclassdef($newclassdef);
			$d_c=mysql_query("SELECT COUNT(id) FROM class WHERE
				course_id='$crid' AND subject_id='$bid' AND stage='$stagename';");
			$currentno=mysql_result($d_c,0);
			if($currentno==0 or ($newclassdef['many']>$currentno 
					   and $oldclassdef['generate']==$newclassdef['generate'])){
				/* This is equivalent to just adding new classes and is no
						major change*/
				$newclassdef['naming']=$oldclassdef['naming'];
				populate_subjectclassdef($newclassdef);
				$result[]='New class/es added.';
				}
			else{
				/* Anything else could need midcid, tidcid and cidsid to
				be updated first and so is not going to be done fomr
				here!!! The changes will only take effect at the start of the next
				curriculum year. */
				$error[]='These changes will only be made effective for the new academic year.';
				}
			}
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>