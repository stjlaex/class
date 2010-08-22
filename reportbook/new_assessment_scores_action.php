<?php
/**							    new_assessment_scores_action.php
 *
 *	This will import a csv file of assessment scores for a whole
 *  bunch of students. The student identified either by their ClaSS
 *  db id (sid) or by their enrolment no. The scores must start from
 *	the second column and the first line has column headers which must be
 *	either the bid or pid for scores in that column. Use a hash as the
 *	first character in a column header or row to ignore it. Scores
 *  which are grades should be in their grade value (not numerical equivalent),
 *
 */

$action='edit_scores.php';
$action_post_vars=array('eid','curryear');

include('scripts/sub_action.php');

	/*Check user has permission to configure*/
	$perm=getCoursePerm($rcrid,$respons);
	$neededperm='x';
	include('scripts/perm_action.php');

$eid=$_POST['eid'];
$curryear=$_POST['curryear'];
$firstcol=$_POST['firstcol'];

if($sub=='Submit'){

	$AssDef=fetchAssessmentDefinition($eid);
	if($AssDef['GradingScheme']['value']!=''){
		$grading_grades=$AssDef['GradingScheme']['grades'];
		}
	else{
		$grading_grades='';
		}

	$fname=$_FILES['importfile']['tmp_name'];
	if($fname!=''){
   	   	$result[]='Loading file '.$fname;
   		include('scripts/file_import_csv.php');
		if(sizeof($inrows>0)){
			/* The first row is column headers containing the subject
			 * ids of scores being imported. 
			 */
	  		$subjectrow=array_shift($inrows);/*grabs the first row*/
			$subjects=array_slice($subjectrow,2);/*offset to ignore uid and student name in first two columns*/
  			if(sizeof($subjects)>0){
				while(list($index,$subject)=each($subjects)){
					//trigger_error($index.' : '.$subject,E_USER_WARNING);
					$bid='';
					$pid='';
					/* Check if this subject code exists for this course or if its in fact a component. */
					$d_sub=mysql_query("SELECT subject_id FROM 
							component WHERE course_id='$rcrid' AND subject_id='$subject' AND id='';");
					if(mysql_num_rows($d_sub)==0){
						$d_com=mysql_query("SELECT subject_id FROM 
								component WHERE course_id='$rcrid' AND id='$subject';");
						if(mysql_num_rows($d_com)!=0){
							$bid=mysql_result($d_com,0);
							$pid=$subject;
							}
						elseif($subject=='G'){
							$bid='G';
							$pid='';
							}
						elseif($subject[0]=='#'){
							$bid=$subject;
							$pid='';
							}
						}
					else{
						$bid=$subject;
						$pid='';
						}

					if($bid==''){
						$errorcol=$index+2;
						$error[]='Unrecognised subject code '. 
							$subject. ' in column ' . $errorcol .'!';
						}
					else{
						$bids[]=$bid;
						$pids[]=$pid;
						}
					}
				}
			}
		else{
			$error[]='The file was empty!';
			}
		}
	else{
		$error[]='No file specified!';
		}


	if(!isset($error)){
		/* Now read each student row.*/
		while(list($index,$row)=each($inrows)){
			$sid='';
			if($firstcol=='enrolno' and $row[0]!=''){
				$d_student=mysql_query("SELECT student_id FROM info WHERE formerupn='$row[0]';");
				$sid=mysql_result($d_student,0);
				}
			elseif($firstcol=='sid'){
				$sid=$row[0];
				}
			if($sid!=''){
				$insid++;
				foreach($bids as $col => $bid){
					if(isset($bid[0]) and $bid[0]!='#'){
						$invalue=$row[$col+2];/*offset to ignore first two columns*/
						if($grading_grades!=''){
							$res=$invalue;
							$value=gradeToScore($res,$grading_grades);
							}
						else{
							$value=$invalue;
							$res=sigfigs($value,3);
							}
						$score=array('result'=>$res,'value'=>$value);
						update_assessment_score($eid,$sid,$bid,$pids[$col],$score);
						$inscore++;
						}
					}
				}
			}
		$result[]='Entered '.$inscore.' assessment scores for '. $insid.' students.';
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
