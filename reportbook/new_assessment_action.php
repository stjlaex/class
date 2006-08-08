<?php
/*                    new_assessment_action.php
*/

$rcrid=$respons[$r]{'course_id'};

$action='new_assessment.php';

include('scripts/sub_action.php');

if($sub=='Submit' and $_FILES{'importfile'}{'tmp_name'}!=''){
	$importfile=$_POST{'importfile'};
	$fname=$_FILES{'importfile'}{'tmp_name'};
	if($fname!=''){
	   	$result[]='Loading file '.$importfile;
		include('scripts/file_import_csv.php');
		if(sizeof($inrows>0)){
			while(list($index,$d)=each($inrows)){
				$stage=$d[0];
				$year=$d[1];
				$subject=$d[2];
				$method=$d[3];
				$element=$d[4];
				$description=$d[5];
				$resultq=$d[6];
				$outoftotal=$d[8];
				$derivation=$d[9];
				$resultstatus=$d[10];
				$compstatus=$d[11];
				if(mysql_query("INSERT INTO assessment (stage, year, subject_id, method,
				element, description, resultqualifier, outoftotal,
				derivation, resultstatus, component_status, course_id) 
				VALUES ('$stage', '$year', '$subject', '$method',
				'$element', '$description', '$resultq', '$outoftotal',
					'$derivation', '$resultstatus', '$compstatus', '$rcrid');"))	
				{$result[]='Successfully inserted new assessment.';}
				else{$error[]='Assessment already exists!';}
				}
			}
		}
	}

elseif($sub=='Submit'){
		$id=$_POST{'id'};
		$stage=$_POST{'stage'};
		$year=$_POST{'year'};
		$subject=$_POST{'bid'};
		if(isset($_POST{'course'})){$course=$_POST{'course'};}else{$course=$rcrid;};
		$method=$_POST{'method'};
		$pid='';
		$description=$_POST{'description'};
		$resultq=$_POST{'resultq'};
		/**/
		$element=$_POST{'element'};
		$printlabel=$_POST{'printlabel'};
		$resultstatus=$_POST{'resultstatus'};
		$outoftotal=$_POST{'outoftotal'};
		$season=$_POST{'season'};
		$derivation=$_POST{'derivation'};
		$componentstatus=$_POST{'componentstatus'};
		$gena=$_POST{'gena'};
		if($id==''){
			if(mysql_query("INSERT INTO assessment (id, stage, year, subject_id, method,
				component_id, description, resultqualifier, course_id,
				component_status, label, grading_name) 
				VALUES ('$id', '$stage', '$year', '$subject', '$method',
				'$pid', '$description', '$resultq', '$course',
				'$componentstatus', '$printlabel', '$gena');"))	
				{$result[]='Created new assessment.';}
			else {$error[]='Assessment may already exist!'.mysql_error();}
			}
		elseif($id!=''){
			if(mysql_query("UPDATE assessment SET year='$year',
				stage='$stage', subject_id='$subject', method='$method',
				component_id='$pid', description='$description', 
				resultqualifier='$resultq', course_id='$course', 
				component_status='$componentstatus', 
				label='$printlabel', grading_name='$gena' WHERE id='$id';"))	
				{$result[]='Updated assessment details.';}
			else {$error[]='Assessment may not exist!'.mysql_error();}
			}
		}

include('scripts/results.php');
include('scripts/redirect.php');

?>
