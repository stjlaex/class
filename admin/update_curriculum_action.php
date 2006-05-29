<?php
/*								update_curriculum_action.php

Update the database tables to match with entries from the curriculum
files. It does not (as yet) remove any data fro mthe database even if 
it has been removed from the curriculum files.
*/

$action='';

if($_POST{'answer'}=='no'){
	$current='';
	$result[]='NO action taken.';
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}

/*reads the array $curriculum listing those required for this school-site*/
require('../curriculum/curriculums.php');

/*function to open a curriculum file*/
function fileOpen($filename,$curriculum){
   	$path='../curriculum/'.$curriculum.'/'.$filename;
   	$file = fopen ($path, 'r');
   	if (!$file){
		$error[]='Unable to open remote file '.$path.'!'; 
		include('scripts/results.php');
		exit;
		}
	return $file;
	}

/*function to read content of file into array flines*/
function fileRead($file){
	$flines=array();
   	while($in=fgetcsv($file,1000,',')){
		if($in[0]!=''){
			if($in[0]{0}!='#' & $in[0]{0}!='/'){$flines[]=$in;}
			}
		}
   	fclose($file);
	return $flines;
}

	$d_uid=mysql_query("SELECT uid FROM users WHERE username='office'");	
	$officeuid=mysql_result($d_uid,0);
	$d_uid=mysql_query("SELECT uid FROM users WHERE username='administrator'");	
	$adminuid=mysql_result($d_uid,0);

while(list($index,$curriculum)=each($curriculums)){


/*****************Subjects************************/
	$file=fileOpen('subject_codes.csv',$curriculum);
	$flines=fileRead($file);
	for($c=0;$c<sizeof($flines);$c++){
 		$bid=$flines[$c][0];
   		$name=$flines[$c][1];
   		if(mysql_query("INSERT INTO subject (id, name)
						VALUES('$bid','$name')")){
   			mysql_query("INSERT INTO groups (subject_id,
					course_id, name) VALUES ('$bid', '%', '$bid')");
			$gid=mysql_insert_id();
			mysql_query("INSERT INTO perms (uid, gid, r, w, x) 
					VALUES('$adminuid','$gid', '1', '1', '1')");
			}
		else{
				mysql_query("UPDATE subject SET name='$name'
					WHERE id='$bid'");
				}
		}

/*****************Courses************************/
	$file=fileOpen('course_codes.csv',$curriculum);
	$flines=fileRead($file);
	for($c=0;$c<sizeof($flines);$c++){
   		$crid=$flines[$c][0];
   		$name=$flines[$c][1];
   		$stage=$flines[$c][2];
   		$generate=$flines[$c][3];
   		$naming=$flines[$c][4];
   		$many=$flines[$c][5];
   		if(mysql_query("INSERT INTO course (id, name, stage,
				generate, naming, many)
				VALUES ('$crid','$name','$stage','$generate','$naming','$many')")){
				mysql_query("INSERT INTO groups (subject_id,
				course_id, name) VALUES ('%', '$crid', '$crid')");
				$gid=mysql_insert_id();
				mysql_query("INSERT INTO perms (uid, gid, r, w, x) 
					VALUES('$adminuid','$gid', '1', '1', '1')");
				}
		else{
				mysql_query("UPDATE course SET name='$name',
					stage='$stage', generate='$generate', naming='$naming',
					many='$many' WHERE id='$crid'");
				}
		}

/*****************Components************************/
	$file=fileOpen('component_codes.csv',$curriculum);
	$flines=fileRead($file);
	for($c=0;$c<sizeof($flines);$c++){
 		$pid=$flines[$c][0];
   		$crid=$flines[$c][1];
   		$bid=$flines[$c][2];
   		$status=$flines[$c][3];
   		if(mysql_query("INSERT INTO component (id, course_id, subject_id, status)
   			VALUES('$pid','$crid','$bid','$status')")){
			}
		else{
				mysql_query("UPDATE component SET status='$status'
					WHERE id='$pid' AND course_id='$crid' AND subject_id='$bid'");
				}
		}

/*****************Mark Definitions***************/
	$file=fileOpen('mark_definitions.csv',$curriculum);
	$flines=fileRead($file);
	for($c=0;$c<sizeof($flines);$c++){
   		$name=$flines[$c][0];
   		$scoretype=$flines[$c][1];
   		$tier=$flines[$c][2];
   		$outoftotal=$flines[$c][3];
   		$grading_name=$flines[$c][4];
   		$comment=$flines[$c][5];
   		$crid=$flines[$c][6];
   		$bid=$flines[$c][7];
   		$author=$flines[$c][8];
   		if(mysql_query("INSERT INTO markdef (name, scoretype, tier, 
				outoftotal, grading_name, comment, course_id, subject_id, author)
				VALUES ('$name', '$scoretype', '$tier', '$outoftotal',
					'$grading_name', '$comment', '$crid', '$bid', '$author')")){}
		else{mysql_query("UPDATE markdef SET 
				scoretype='$scoretype', tier='$tier', outoftotal='$outoftotal',
				grading_name='$grading_name', comment='$comment',
				subject_id='$bid', author='$author' 
				WHERE name='$name' AND course_id='$crid'");}
		}

/*****************Grade Schemes***************/
	$file=fileOpen('grade_schemes.csv',$curriculum);
	$flines=fileRead($file);
	for($c=0;$c<sizeof($flines);$c++){
   		$name=$flines[$c][0];
   		$grades=$flines[$c][1];
   		$comment=$flines[$c][2];
   		$crid=$flines[$c][3];
   		$bid=$flines[$c][4];
   		$author=$flines[$c][5];
   		if(mysql_query("INSERT INTO grading (name, grades,  
				comment, course_id, subject_id, author)
				VALUES ('$name', '$grades', 
					'$comment', '$crid', '$bid', '$author')")){}
		else{mysql_query("UPDATE grading SET 
				grades='$grades', comment='$comment',
				subject_id='$bid', author='$author' 
				WHERE name='$name' AND course_id='$crid'");}
		}

/*****************Yeargroups************************/
	$file=fileOpen('yeargroups.csv',$curriculum);
	$flines=fileRead($file);
	for($c=0;$c<sizeof($flines);$c++){
 		$yid=$flines[$c][0];
   		$name=$flines[$c][1];
   		$ncyear=$flines[$c][2];
   		$section=$flines[$c][3];
		$d_section=mysql_query("SELECT id FROM section WHERE name='$section'");	
		if(mysql_num_rows($d_Section)>0){$secid=mysql_result($d_section,0);}
		else{$secid=0;}

   		if(mysql_query("INSERT INTO yeargroup (id, name, ncyear, section_id)
   			VALUES('$yid','$name','$ncyear','$secid')")){
				mysql_query("INSERT INTO groups (yeargroup_id,name) VALUES ('$yid', '$name')");
				$gid=mysql_insert_id();
				mysql_query("INSERT INTO perms (uid, gid, r, w, x) VALUES('$officeuid','$gid', '1', '1', '1')");
				mysql_query("INSERT INTO perms (uid, gid, r, w, x) VALUES('$adminuid','$gid', '1', '1', '1')");
				}
		else{mysql_query("UPDATE yeargroup SET
   			name='$name', ncyear='$ncyear', section_id='$secid' WHERE id='$yid'");}
		}


/*****************Forms************************/
	$file=fileOpen('formgroups.csv',$curriculum);
	$flines=fileRead($file);
	for($c=0;$c<sizeof($flines);$c++){
 		$fid=$flines[$c][0];
   		$yid=$flines[$c][1];
   		$tid=$flines[$c][2];
   		if(mysql_query("INSERT INTO form (id, yeargroup_id, teacher_id)
   			VALUES('$fid','$yid','$tid')")){}
		else{mysql_query("UPDATE form SET yeargroup_id='$yid', 
				teacher_id='$tid' WHERE id='$fid'");}
		}


/*finished this curriculum*/	
	$result[]='Updated the '.$curriculum.' curriculum.';
	}

$result[]='You will need to logout and close your browser to see the changes.';
include('scripts/results.php');
include('scripts/redirect.php');
?>
