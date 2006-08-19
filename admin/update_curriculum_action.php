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

include('scripts/sub_action.php');

include('scripts/answer_action.php');

/*reads the array $curriculum listing those required for this school-site*/
require('../curriculum/include.php');

function read_curriculum_file($filename,$curriculum){
	$path='../curriculum/'.$curriculum.'/'.$filename;
	if(file_exists($path)){
		$xmlArray=xmlfilereader($path);
		}
	else{$xmlArray=array();}
	return $xmlArray;
	}

mysql_query("DELETE FROM cridbid");
mysql_query("DELETE FROM classes");
mysql_query("DELETE FROM component");
mysql_query("DELETE FROM form");
mysql_query("DELETE FROM yeargroup");


$d_uid=mysql_query("SELECT uid FROM users WHERE username='administrator'");	
$adminuid=mysql_result($d_uid,0);

//$curriculums[]='common';

while(list($index,$curriculum)=each($curriculums)){

	$Courses=read_curriculum_file('courses.xml',$curriculum);

	$Courses=xmlarray_indexed_check($Courses,'course');
	while(list($index,$Course)=each($Courses['course']) and $Course!=''){
		/*****************Courses************************/
   		$crid=$Course['id'];
		if($crid!='%'){
   		$name=$Course['name'];
   		$sequence=$Course['sequence'];
   		$endmonth=$Course['endmonth'];
		$course_generate=$Course['setting'];
   		$course_many=$Course['classes'];
		if(is_array($Course['naming'])){
			$course_naming=$Course['naming']['root'] 
					.';'.$Course['naming']['stem'] 
					.';'.$Course['naming']['branch'].';'.$Course['naming']['counter'];
			if(sizeof($course_naming)>39){$course_naming='';}
			}
   		else{$course_naming='';}

		$d_course=mysql_query("SELECT name FROM course WHERE id='$crid'");
		if(mysql_num_rows($d_course)==0){
			mysql_query("INSERT INTO course (id, name, sequence,
				generate, naming, many, endmonth)
				VALUES ('$crid','$name','$sequence', 
				'$course_generate','$course_naming','$course_many','$endmonth')");
			mysql_query("INSERT INTO groups (subject_id,
					course_id, name) VALUES ('%', '$crid', '$crid')");
			$gid=mysql_insert_id();
			mysql_query("INSERT INTO perms (uid, gid, r, w, x) 
					VALUES('$adminuid','$gid', '1', '1', '1')");
			}
		else{
			mysql_query("UPDATE course SET name='$name',
					sequence='$sequence', generate='$course_generate', naming='$course_naming',
					many='$course_many', endmonth='$endmonth' WHERE id='$crid'");
			}
	
		$Subjects=xmlarray_indexed_check($Course['subjects'],'subject');
		while(list($index,$Subject)=each($Subjects['subject']) and $Subject!=''){
			/*****************Subjects************************/
			$bid=$Subject['id'];
			$name=$Subject['name'];
			if(isset($Subject['setting'])){$generate=$Subject['setting'];}
			else{$generate=$course_generate;}
			if(isset($Subject['classes'])){$many=$Subject['classes'];}
			else{$many=$course_many;}
			if(is_array($Subject['naming'])){
				$naming=$Subject['naming']['root'] 
						.';'.$Subject['naming']['stem'] 
						.';'.$Subject['naming']['branch'].';'.$Subject['naming']['counter'];
				if(sizeof($naming)>39){$naming='';}
				}
	  		else{$naming=$course_naming;}
			$d_subject=mysql_query("SELECT name FROM subject WHERE id='$bid'");
			if(mysql_num_rows($d_subject)==0){
			   mysql_query("INSERT INTO subject (id, name)
						VALUES('$bid','$name')");
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


			$Stages=xmlarray_indexed_check($Course['stages'],'stage');
			while(list($index,$stage)=each($Stages['stage']) and $stage!=''){
				if(mysql_query("INSERT INTO classes (many,
								generate, naming, stage, subject_id, course_id) VALUES
								('$many', '$generate', '$naming', '$stage', '$bid',
								'$crid')")){}
				else{$error[]='Failed to insert new classes!';
								$error[]=mysql_error();}
				updateCohort(array('course_id'=>$crid,'stage'=>$stage));
				}

			$Components=xmlarray_indexed_check($Subject['components'],'component');
			while(list($index,$Component)=each($Components['component'])
																and $Component!=''){
				/*****************Components************************/
				$pid=$Component['id'];
				$status=$Component['status'];
				$name=$Component['name'];
				$d_subject=mysql_query("SELECT name FROM subject WHERE id='$bid'");
				if(mysql_num_rows($d_subject)==0){
					mysql_query("INSERT INTO subject (id, name)
						VALUES('$pid','$name')");
					}
				$d_component=mysql_query("SELECT status FROM component
					WHERE id='$pid' AND course_id='$crid' AND subject_id='$bid'");
				if(mysql_num_rows($d_component)==0){
					mysql_query("INSERT INTO component (id, course_id, subject_id, status)
							VALUES('$pid','$crid','$bid','$status')");
					}
				else{
					mysql_query("UPDATE component SET status='$status'
					WHERE id='$pid' AND course_id='$crid' AND subject_id='$bid'");
					}
				}
			}
			}

		$AssessmentMethods=$Course['assessmentmethods'];

		$GradeSchemes=xmlarray_indexed_check($AssessmentMethods['gradeschemes'],'gradescheme');
		while(list($index,$GradeScheme)=each($GradeSchemes['gradescheme']) and $GradeScheme!=''){
			/*****************Grade Schemes***************/
			$name=$GradeScheme['name'];
			$Grades=$GradeScheme['grades'];
			$listgrades='';
		    while(list($index,$Grade)=each($Grades['grade'])){
   				$listgrades=$listgrades . $Grade['value'].':'.$Grade['score'].';';
   				}
			$comment=$GradeScheme['comment'];
			if(isset($GradeScheme['subjectid'])){$bid=$GradeScheme['subjectid'];}
			else{$bid='%';}
			$author='ClaSS';
			$d_grading=mysql_query("SELECT subject_id FROM grading WHERE
			name='$name' AND course_id='$crid'");
			if(mysql_num_rows($d_grading)==0){
				mysql_query("INSERT INTO grading (name, grades,  
				comment, course_id, subject_id, author)
				VALUES ('$name', '$listgrades', 
					'$comment', '$crid', '$bid', '$author')");
				}
			else{mysql_query("UPDATE grading SET 
				grades='$listgrades', comment='$comment',
				subject_id='$bid', author='$author' 
				WHERE name='$name' AND course_id='$crid'");
				}
			}

		$Categories=xmlarray_indexed_check($AssessmentMethods['categories'],'category');	
		while(list($index,$Category)=each($Categories['category']) and
																   $Category!=''){
			/*****************Categories***************/
			$name=$Category['name'];
			$type=$Category['typeofuse'];
			$rating=$Category['order'];
			$ratingname=$Category['ratingname'];
			if(isset($Category['subjectid'])){$bid=$Category['subjectid'];}
			else{$bid='%';}
			$sectionname=$Category['sectionname'];
			$d_categorydef=mysql_query("SELECT id FROM categorydef WHERE
			name='$name' AND course_id='$crid' AND subject_id='$bid'");
			if(mysql_num_rows($d_categorydef)==0){
				mysql_query("INSERT INTO categorydef (name, type,  
				rating, rating_name, course_id, subject_id, section_id)
				VALUES ('$name', '$type', '$rating', 
					'$ratingname', '$crid', '$bid', '$sectioname')");
				}
			else{
				mysql_query("UPDATE categorydef SET 
				type='$type', rating='$rating',
				rating_name='$ratingname', subject_id='$bid', section_id='$sectionname'  
				WHERE name='$name' AND course_id='$crid'");
				}
			}

		$Ratings=xmlarray_indexed_check($AssessmentMethods['ratings'],'rating');
		while(list($index,$Rating)=each($Ratings['rating'])){
			/*****************Ratings***************/
			$name=$Rating['name'];
			$Values=xmlarray_indexed_check($Rating['values'],'value');
			while(list($index,$Value)=each($Values['value'])){
				$order=$Value['order'];
				$descriptor=$Value['descriptor'];
				$description=$Value['description'];
				$d_rating=mysql_query("SELECT descripor FROM rating WHERE
						name='$name' AND value='$order'");
				if(mysql_num_rows($d_rating)==0){
					mysql_query("INSERT INTO rating (name, descriptor,  
							longdescriptor, value)
				VALUES ('$name', '$descriptor', '$description', '$order')");
					}
				else{
					mysql_query("UPDATE rating SET 
						descriptor='$descriptor', longdescriptor='$description' 
							WHERE name='$name' AND value='$order'");
					}
				}
			}

		$MarkDefinitions=xmlarray_indexed_check($AssessmentMethods['markdefinitions'],'mark');
		while(list($index,$Mark)=each($MarkDefinitions['mark']) and $Mark!=''){
			/*****************Mark Definitions***************/
			$name=$Mark['name'];
			$scoretype=$Mark['scoretype'];
			$outoftotal=$Mark['outoftotal'];
			$grading_name=$Mark['gradingscheme'];
			$comment=$Mark['comment'];
			if(isset($Mark['subjectid'])){$bid=$Mark['subjectid'];}
			else{$bid='%';}
			$author='ClaSS';
			$d_markdef=mysql_query("SELECT scoretype FROM markdef WHERE
			name='$name' AND course_id='$crid'");
			if(mysql_num_rows($d_markdef)==0){
				mysql_query("INSERT INTO markdef (name, scoretype,  
				outoftotal, grading_name, comment, course_id, subject_id, author)
				VALUES ('$name', '$scoretype', '$outoftotal',
					'$grading_name', '$comment', '$crid', '$bid', '$author')");
				}
			else{mysql_query("UPDATE markdef SET 
				scoretype='$scoretype', outoftotal='$outoftotal',
				grading_name='$grading_name', comment='$comment',
				subject_id='$bid', author='$author' 
				WHERE name='$name' AND course_id='$crid'");
				}
			}

		$Methods=xmlarray_indexed_check($AssessmentMethods['methods'],'method');	
		while(list($index,$Method)=each($Methods['method']) and $Method!=''){
			/*****************Methods***************/
			$subtype=$Method['value'];
			$name=$Method['description'];
			$ratingname=$Method['gradescheme'];
			$d_categorydef=mysql_query("SELECT id FROM categorydef WHERE
			subtype='$subtype' AND course_id='$crid' AND type='met'");
			if(mysql_num_rows($d_categorydef)==0){
				mysql_query("INSERT INTO categorydef (name, type, subtype, 
				rating_name, course_id)
				VALUES ('$name', 'met', '$subtype', 
					'$ratingname', '$crid')");
				}
			else{
				mysql_query("UPDATE categorydef SET 
					rating_name='$ratingname',   
					name='$name' WHERE course_id='$crid' AND type='met'
					AND subtype='$subtype'");
				}
			}

		$ResultQualifiers=xmlarray_indexed_check($AssessmentMethods['resultqualifiers'],'resultqualifier');	
		while(list($index,$ResultQualifier)=each($ResultQualifiers['resultqualifier'])
																and $ResultQualifier!=''){
			/*****************ResultQualifierss***************/
			$subtype=$ResultQualifier['value'];
			$name=$ResultQualifier['description'];
			$d_categorydef=mysql_query("SELECT id FROM categorydef WHERE
			subtype='$subtype' AND course_id='$crid' AND type='rsq'");
			if(mysql_num_rows($d_categorydef)==0){
				mysql_query("INSERT INTO categorydef (name, type, subtype, course_id)
				VALUES ('$name', 'rsq', '$subtype', '$crid')");
				}
			else{
				mysql_query("UPDATE categorydef SET name='$name' 
				WHERE subtype='$subtype' AND course_id='$crid' AND type='rsq'");
				}
			}
		}

	$Groups=read_curriculum_file('groups.xml',$curriculum);

	$YearGroups=xmlarray_indexed_check($Groups['yeargroups'],'yeargroup');
	while(list($index,$Group)=each($YearGroups['yeargroup']) and $Group!=''){
		/*****************Yeargroups************************/
 		$yid=$Group['id'];
   		$name=$Group['name'];
   		$ncyear=$Group['ncyear'];
   		$section=$Group['section'];
		$d_section=mysql_query("SELECT id FROM section WHERE name='$section'");	
		if(mysql_num_rows($d_section)>0){$secid=mysql_result($d_section,0);}
		else{$secid=0;}
		updateCommunity(array('name'=>$yid,'type'=>'year'));
   		if(mysql_query("INSERT INTO yeargroup (id, name, ncyear, section_id)
   			VALUES('$yid','$name','$ncyear','$secid')")){
				mysql_query("INSERT INTO groups (yeargroup_id,name) VALUES ('$yid', '$name')");
				$gid=mysql_insert_id();
				mysql_query("INSERT INTO perms (uid, gid, r, w, x) VALUES('$adminuid','$gid', '1', '1', '1')");
				}

		$Formgroups=xmlarray_indexed_check($Group['formgroups'],'form');
		while(list($index,$fid)=each($Formgroups['form']) and $fid!=''){
			/*****************Forms************************/
			mysql_query("INSERT INTO form (id, yeargroup_id)
						VALUES('$fid','$yid')");
			updateCommunity(array('name'=>$fid,'type'=>'form'));
			}
		}

	$result[]=get_string('updatedcurriculum',$book).$curriculum;
	}

$result[]=get_string('logoutandrestart',$book);
include('scripts/results.php');
include('scripts/redirect.php');
?>
