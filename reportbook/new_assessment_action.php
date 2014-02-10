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
		$outoftotal=$_POST['outoftotal'];
		//$season=$_POST['season'];
		//$resultq=$_POST['resultq'];
		$pid='';
		$description=$_POST['description'];
		$element=$_POST['element'];
		$printlabel=$_POST['printlabel'];
		$resultstatus=$_POST['resultstatus'];
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
			/* Editing an existing assessment if the are no marks. */
			$count=$_POST['Markcount'];
			if($count==0){
				mysql_query("UPDATE assessment SET stage='$stage', 
					subject_id='$subject', course_id='$course', 
					component_status='$componentstatus', 
					strand_status='$strandstatus' WHERE id='$eid';");
				}

			/* Find the appropriate markdef_name */
			if($gena!='' and $gena!=' '){
				$d_m=mysql_query("SELECT name FROM markdef WHERE
								grading_name='$gena' AND scoretype='grade' 
								AND (course_id='%' OR course_id='$course');");
				if(mysql_num_rows($d_m)==0){
					$markdef_name=$crid.' '.$gena;
					/*mysql_query("UPDATE markdef SET
								name='$markdef_name', scoretype='grade', grading_name='$gena',
								comment='$description', outoftotal='$outoftotal', 
								course_id='$course', subject_id='$subject' WHERE id='$markdef_id';");*/
					}
				else{
					$markdef_name=mysql_result($d_m,0);
					}
				$markdef_scoretype='grade';
				}
			else{
				$d_m=mysql_query("SELECT name FROM markdef WHERE
								scoretype='value' AND (course_id='%' OR course_id='$course');");
				$markdef_name=mysql_result($d_m,0);
				$markdef_scoretype='value';
				}
			mysql_free_result($d_m);

			/* Updating existing marks for this assessment */
			$d_e=mysql_query("SELECT * FROM eidmid WHERE assessment_id='$eid';");
			while($e=mysql_fetch_array($d_e,MYSQL_ASSOC)){
				$mid=$e['mark_id'];
				mysql_query("UPDATE mark SET topic='$description', entrydate='$creation', def_name='$markdef_name' WHERE id='$mid';");
				}

			/* Editing an existing assessment. */
			mysql_query("UPDATE assessment SET year='$year',
				method='$method', component_id='$pid', description='$description', 
				resultqualifier='$resultq', resultstatus='$resultstatus', 
				element='$element', label='$printlabel', grading_name='$gena',
				deadline='$deadline', creation='$creation', profile_name='$profile_name' WHERE id='$eid';");
			update_derivation($eid,$derivation);
			}
		}

include('scripts/redirect.php');
?>
