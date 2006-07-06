<?php
/**								update_curriculum_action.php
 *
 *Update the database tables to match with entries from the curriculum
 *packs. It will leave old courses and subjects (and markdefs and
 *gradeschemes) in the database after removal from the curriculum
 *file. But will remove all yeargroup, form, and component data from
 *the database if it has been removed from the curriculum files. CAUTION!
 */

$action='';
$choice='';

if($_POST{'answer'}=='no'){
	$current='';
	$result[]=get_string('noactiontaken',$book);
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}

/*reads the array $curriculum listing those required for this school-site*/
require('../curriculum/curriculums.php');

function read_curriculum_file($filename,$curriculum){
	$path='../curriculum/'.$curriculum.'/'.$filename;
	$xmlArray=xmlfilereader($path);
	return $xmlArray;
	}

mysql_query("DELETE FROM cridbid");
mysql_query("DELETE FROM classes");
mysql_query("DELETE FROM component");
mysql_query("DELETE FROM form");
mysql_query("DELETE FROM yeargroup");


$d_uid=mysql_query("SELECT uid FROM users WHERE username='administrator'");	
$adminuid=mysql_result($d_uid,0);

while(list($index,$curriculum)=each($curriculums)){

	$Courses=read_curriculum_file('courses.xml',$curriculum);
	while(list($index,$Course)=each($Courses['course'])){
		/*****************Courses************************/
   		$crid=$Course['id'];
   		$name=$Course['name'];
   		$stage=$Course['stage'];
   		$naming='';
		$course_generate=$Course['setting'];
   		$course_many=$Course['classes'];
		if(mysql_query("INSERT INTO course (id, name, stage,
				generate, naming, many)
				VALUES ('$crid','$name','$stage', 
				'$course_generate','$naming','$course_many')")){
					mysql_query("INSERT INTO groups (subject_id,
					course_id, name) VALUES ('%', '$crid', '$crid')");
					$gid=mysql_insert_id();
					mysql_query("INSERT INTO perms (uid, gid, r, w, x) 
					VALUES('$adminuid','$gid', '1', '1', '1')");
					}
		else{
				mysql_query("UPDATE course SET name='$name',
					stage='$stage', generate='$course_generate', naming='$naming',
					many='$course_many' WHERE id='$crid'");
				}

		while(list($index,$Subject)=each($Course['subjects']['subject'])){
			/*****************Subjects************************/
			$bid=$Subject['id'];
			$name=$Subject['name'];
			if(isset($Subject['setting'])){$generate=$Subject['setting'];}
			else{$generate=$course_generate;}
			if(isset($Subject['classes'])){$many=$Subject['classes'];}
			else{$many=$course_many;}
			$components=$Subject['components'];
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

			mysql_query("INSERT INTO cridbid SET course_id='$crid', subject_id='$bid'");


			$Yeargroups=$Course['yeargroups'];
			while(list($index,$yid)=each($Yeargroups['yeargroup'])){
				$result[]=$yid;
				if(mysql_query("INSERT INTO classes (many,
								generate, naming, yeargroup_id, subject_id, course_id) VALUES
								('$many', '$generate', '$naming', '$yid', '$bid',
								'$crid')")){$result[]="Updated courses.";}
				else{$error[]='Failed to insert new classes!';	
								$error[]=mysql_error();}
				}

			while(list($index,$Component)=each($Subject['components']['component'])){
				/*****************Components************************/
				$pid=$Component['id'];
				$status=$Component['status'];
				$name=$Component['name'];
				mysql_query("INSERT INTO subject (id, name)
						VALUES('$pid','$name')");
				mysql_query("INSERT INTO component (id, course_id, subject_id, status)
							VALUES('$pid','$crid','$bid','$status')");
				}
			}
		}

	$Groups=read_curriculum_file('yeargroups.xml',$curriculum);
	while(list($index,$Group)=each($Groups['yeargroup'])){
		/*****************Yeargroups************************/
 		$yid=$Group['id'];
   		$name=$Group['name'];
   		$ncyear=$Group['ncyear'];
   		$section=$Group['section'];
		$d_section=mysql_query("SELECT id FROM section WHERE name='$section'");	
		if(mysql_num_rows($d_section)>0){$secid=mysql_result($d_section,0);}
		else{$secid=0;}

   		if(mysql_query("INSERT INTO yeargroup (id, name, ncyear, section_id)
   			VALUES('$yid','$name','$ncyear','$secid')")){
				mysql_query("INSERT INTO groups (yeargroup_id,name) VALUES ('$yid', '$name')");
				$gid=mysql_insert_id();
				mysql_query("INSERT INTO perms (uid, gid, r, w, x) VALUES('$adminuid','$gid', '1', '1', '1')");
				}
		
		while(list($index,$fid)=each($Group['formgroups']['form'])){
			/*****************Forms************************/
			mysql_query("INSERT INTO form (id, yeargroup_id)
						VALUES('$fid','$yid')");
			}
		}

	$MarkDefinitions=read_curriculum_file('markdefinitions.xml',$curriculum);
	while(list($index,$Mark)=each($MarkDefinitions['markdefinitions']['markdefinition'])){
		/*****************Mark Definitions***************/
   		$name=$Mark['name'];
   		$scoretype=$Mark['scoretype'];
   		$tier=$Mark['tier'];
   		$outoftotal=$Mark['outoftotal'];
   		$grading_name=$Mark['gradingname'];
   		$comment=$Mark['comment'];
   		$crid=$Mark['courseid'];
   		$bid=$Mark['subjectid'];
   		$author='ClaSS';
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

	$GradeSchemes=read_curriculum_file('gradeschemes.xml',$curriculum);
	while(list($index,$Grade)=each($GradeSchemes)){
		/*****************Grade Schemes***************/
   		$name=$Grade['name'];
   		$grades=$Grade['grades'];
   		$comment=$Grade['comment'];
   		$crid=$Grade['courseid'];
   		$bid=$Grade['subjectid'];
   		$author='ClaSS';
   		if(mysql_query("INSERT INTO grading (name, grades,  
				comment, course_id, subject_id, author)
				VALUES ('$name', '$grades', 
					'$comment', '$crid', '$bid', '$author')")){}
		else{mysql_query("UPDATE grading SET 
				grades='$grades', comment='$comment',
				subject_id='$bid', author='$author' 
				WHERE name='$name' AND course_id='$crid'");}
		}

	$result[]=get_string('updatedcurriculum',$book).$curriculum;
	}

$result[]=get_string('logoutandrestart',$book);
include('scripts/results.php');
include('scripts/redirect.php');
?>
