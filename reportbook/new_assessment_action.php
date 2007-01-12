<?php
/*                    new_assessment_action.php
*/

$rcrid=$respons[$r]['course_id'];

$action='new_assessment.php';

include('scripts/sub_action.php');

if($sub=='Submit' and $_FILES['importfile']['tmp_name']!=''){
	$importfile=$_POST['importfile'];
	$fname=$_FILES['importfile']['tmp_name'];
	if($fname!=''){
	   	$result[]='Loading file '.$importfile;
		include('scripts/file_import_csv.php');
		if(sizeof($inrows>0)){
			while(list($index,$d)=each($inrows)){
				/*this matches partly the UK CBDS spreadsheets and isn't generally useful*/
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
				$gena=$d[12];
				$create=$d[13];
				$deadline=$d[14];
				mysql_query("INSERT INTO assessment (stage, year, subject_id, method, element,
					description, label, resultqualifier, outoftotal,
					derivation, resultstatus, component_status,
					course_id, grading_name, creation, deadline) VALUES
					('$stage', '$year', '$subject', '$method',
					'$element', '$description', '$label', '$resultq',
					'$outoftotal', '$derivation', '$resultstatus',
					'$componentstatus', '$rcrid', '$gena', '$create',
					'$deadline');");
				}
			}
		}
	}

elseif($sub=='Submit'){
		$id=$_POST['id'];
		$stage=$_POST['stage'];
		$year=$_POST['year'];
		$subject=$_POST['bid'];
		if(isset($_POST['course'])){$course=$_POST['course'];}else{$course=$rcrid;};
		$method=$_POST['method'];
		$pid='';
		$description=$_POST['description'];
		$resultq=$_POST['resultq'];
		$element=$_POST['element'];
		$printlabel=$_POST['printlabel'];
		$resultstatus=$_POST['resultstatus'];
		$outoftotal=$_POST['outoftotal'];
		$season=$_POST['season'];
		$derivation=$_POST['derivation'];
		$componentstatus=$_POST['componentstatus'];
		if(isset($_POST['gena'])){$gena=$_POST['gena'];}else{$gena='';};
		$deadline=$_POST['deadline'];
		$creation=$_POST['creation'];
		if($id==''){
			mysql_query("INSERT INTO assessment (stage, year, subject_id, method, derivation, 
				element, component_id, description, resultqualifier, course_id,
				component_status, label, grading_name, creation, deadline) 
				VALUES ('$stage', '$year', '$subject', '$method', '$derivation',
				'$element', '$pid', '$description', '$resultq', '$course',
				'$componentstatus', '$printlabel', '$gena','$creation','$deadline');");	
			}
		elseif($id!=''){
			mysql_query("UPDATE assessment SET year='$year',
				stage='$stage', subject_id='$subject', method='$method',
				component_id='$pid', description='$description', 
				resultqualifier='$resultq', course_id='$course', 
				element='$element', derivation='$derivation', 
				component_status='$componentstatus', 
				label='$printlabel', grading_name='$gena',
				deadline='$deadline', creation='$creation' WHERE id='$id';");
			}
		}

include('scripts/redirect.php');
?>
