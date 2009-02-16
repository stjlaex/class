<?php
/**                    new_assessment_action.php
 */

$action='new_assessment.php';

$rcrid=$respons[$r]['course_id'];

$curryear=$_POST['curryear'];
$profid=$_POST['profid'];
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
			while(list($index,$d)=each($inrows)){
				/* This is based on the UK CBDS spreadsheets originaly
						but has developed to be generally useful. */
				$stage=$d[0];
				$year=$d[1];
				$subject=$d[2];
				$method=$d[3];
				$element=$d[4];
				$description=$d[5];
				$label=$d[6];
				$resultq=$d[7];
				$outoftotal=$d[8];
				$derivation=$d[9];
				$resultstatus=$d[10];
				$componentstatus=$d[11];
				$strandstatus=$d[12];
				$gena=$d[13];
				$create=$d[14];
				$deadline=$d[15];
				mysql_query("INSERT INTO assessment (stage, year, subject_id, method, element,
					description, label, resultqualifier, outoftotal,
					resultstatus, component_status, strand_status,
					course_id, grading_name, creation, deadline) VALUES
					('$stage', '$year', '$subject', '$method',
					'$element', '$description', '$label', '$resultq',
					'$outoftotal', '$resultstatus', '$componentstatus', 
					'$strandstatus', '$rcrid', '$gena', '$create', '$deadline');");
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
		if($eid==''){
			mysql_query("INSERT INTO assessment (stage, year, subject_id, method,  
				element, component_id, description, resultqualifier, resultstatus, course_id,
				component_status, strand_status, label, grading_name, creation, deadline) 
				VALUES ('$stage', '$year', '$subject', '$method', 
				'$element', '$pid', '$description', '$resultq', '$resultstatus', '$course','$componentstatus',
				'$strandstatus', '$printlabel', '$gena','$creation','$deadline');");	
			if($derivation!=''){
				$eid=mysql_insert_id();
				update_derivation($eid,$derivation);
				}
			}
		elseif($eid!=''){
			mysql_query("UPDATE assessment SET year='$year',
				stage='$stage', subject_id='$subject', method='$method',
				component_id='$pid', description='$description', 
				resultqualifier='$resultq', resultstatus='$resultstatus', course_id='$course', 
				element='$element', component_status='$componentstatus', 
				strand_status='$strandstatus',label='$printlabel', grading_name='$gena',
				deadline='$deadline', creation='$creation' WHERE id='$eid';");
			update_derivation($eid,$derivation);
			}
		}

include('scripts/redirect.php');
?>
