<?php
/**                    new_assessment_action.php
 */

$action='new_assessment.php';

$rcrid=$respons[$r]['course_id'];

$curryear=$_POST['curryear'];
if(isset($_POST['profid'])){
	$profid=$_POST['profid'];
	$profile=get_assessment_profile($profid);
	$profile_name=$profile['name'];
	}
$action_post_vars=array('curryear','profid');

include('scripts/sub_action.php');

if($sub=='Submit' and isset($_FILES['importfile']) and $_FILES['importfile']['tmp_name']!=''){
	/*Check user has permission to configure*/
	$perm=getCoursePerm($rcrid,$respons);
	$neededperm='x';
	include('scripts/perm_action.php');

	$importfile=$_POST['importfile'];
	$fname=$_FILES['importfile']['tmp_name'];
	if($fname!=''){
	   	$result[]='Loading file '.$importfile;
		include('scripts/file_import_csv.php');
		if(sizeof($inrows>0)){
			foreach($inrows as $d){
				$description=$d[0];
				$element=$d[1];
				$label=$d[2];
				$resultstatus=$d[3];
				$create=$d[4];
				$deadline=$d[5];
				$stage=$d[6];
				$subject=$d[7];
				if($subject=='All' or $subject=='all'){$subject='%';}
				$componentstatus=$d[8];
				$strandstatus=$d[9];
				$gena=$d[10];
				if(isset($d[11])){$profile_name=$d[11];}else{$profile_name='';}
				if(isset($d[12])){$derivation=$d[12];}else{$derivation='';}

				mysql_query("INSERT INTO assessment (stage, year, subject_id, element,
					description, label, resultstatus, component_status, strand_status,
					course_id, grading_name, creation, deadline, profile_name) VALUES
					('$stage', '$curryear', '$subject', '$element', '$description', '$label',
						'$resultstatus', '$componentstatus', '$strandstatus', 
						'$rcrid', '$gena', '$create', '$deadline', '$profile_name');");
				if($derivation!=''){
					$eid=mysql_insert_id();
					update_derivation($eid,$derivation);
					}
				}
			}
		}
	}

elseif($sub=='Submit'){
	/*Check user has permission to configure*/
	$perm=getCoursePerm($rcrid,$respons);
	$neededperm='x';
	include('scripts/perm_action.php');

		$eid=$_POST['id'];
		$year=$curryear;
		$stage=$_POST['stage'];
		$subject=$_POST['bid'];
		if(isset($_POST['course'])){$course=$_POST['course'];}else{$course=$rcrid;};
		//$method=$_POST['method'];
		$pid='';
		$description=$_POST['description'];
		//$resultq=$_POST['resultq'];
		$element=$_POST['element'];
		$printlabel=$_POST['printlabel'];
		$resultstatus=$_POST['resultstatus'];
		//$outoftotal=$_POST['outoftotal'];
		//$season=$_POST['season'];
		$derivation=$_POST['derivation'];
		$componentstatus=$_POST['componentstatus'];
		$strandstatus=$_POST['strandstatus'];
		if(isset($_POST['gena'])){$gena=$_POST['gena'];}else{$gena='';};
		$deadline=$_POST['deadline'];
		$creation=$_POST['creation'];

		/* Check if the assessment is being assigned to a different profile.*/
		if(isset($_POST['newprofid'])){$newprofid=$_POST['newprofid'];}else{$newprofid=$profid;}
		if($newprofid!=$profid){
			$newprofile=get_assessment_profile($newprofid);
			$profile_name=$newprofile['name'];
			}

		if($eid==''){
			/* A brand new assessment entered. */
			mysql_query("INSERT INTO assessment (stage, year, subject_id, method,  
				element, component_id, description, resultqualifier, resultstatus, course_id,
				component_status, strand_status, label, grading_name, creation, deadline, profile_name) 
				VALUES ('$stage', '$year', '$subject', '$method', 
				'$element', '$pid', '$description', '$resultq', '$resultstatus', '$course','$componentstatus',
				'$strandstatus', '$printlabel', '$gena','$creation','$deadline','$profile_name');");	
			if($derivation!=''){
				$eid=mysql_insert_id();
				update_derivation($eid,$derivation);
				}
			}
		elseif($eid!=''){
			/* Editing an existing assessment. */
			mysql_query("UPDATE assessment SET year='$year',
				stage='$stage', subject_id='$subject', method='$method',
				component_id='$pid', description='$description', 
				resultqualifier='$resultq', resultstatus='$resultstatus', course_id='$course', 
				element='$element', component_status='$componentstatus', 
				strand_status='$strandstatus',label='$printlabel', grading_name='$gena',
				deadline='$deadline', creation='$creation', profile_name='$profile_name' WHERE id='$eid';");
			update_derivation($eid,$derivation);
			}
		}

include('scripts/redirect.php');
?>
