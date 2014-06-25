<?php
/**							    import_assessment_scores_action2.php
 */

$action='new_assessment.php';

include('scripts/sub_action.php');

/*Check user has permission to configure*/
$perm=getCoursePerm($rcrid,$respons);
$neededperm='x';
include('scripts/perm_action.php');

$curryear=$_POST['curryear'];
$scores=$_POST['scores'];

if($sub=='Submit'){
	foreach($scores as $score){
		$eid='';
		$scoreparts=explode(":::",$score);
		$inscore=$scoreparts[0];
		$sid=$scoreparts[1];
		$colno=$scoreparts[2];
		$rowno=$scoreparts[3];
		if($sid=='' and isset($_POST['selectedstudent-'.$rowno])){$sid=$_POST['selectedstudent-'.$rowno];}
		

		$eid=$_POST['assess-'.$colno];
		if(!isset($_POST['subject'])){$subject=$_POST['subject-'.$colno];}else{$subject=$_POST['subject'];}

		$AssDef=fetchAssessmentDefinition($eid);
		$grading_grades=$AssDef['GradingScheme']['grades'];
		if($grading_grades!=''){
			$res=trim($inscore);
			$value=gradeToScore($res,$grading_grades);
			}
		else{
			$value=$inscore;
			$res=$value;
			}
		$inscore=array('result'=>$res,'value'=>$value);
		//$inscore=array('result'=>$inscore,'value'=>$inscore);

		if($subject==''){$subject=$AssDef['Subject']['value'];}

		$bid='';
		$pid='';
		$subject=trim($subject);
		$d_sub=mysql_query("SELECT subject_id FROM 
				component WHERE course_id='$rcrid' AND subject_id='$subject' AND id='';");
		if(mysql_num_rows($d_sub)==0){
			$d_com=mysql_query("SELECT subject_id FROM 
					component WHERE course_id='$rcrid' AND id='$subject';");
			if(mysql_num_rows($d_com)>0){
				/* If the subject is a component. */
				$bid=mysql_result($d_com,0);
				$d_com=mysql_query("SELECT subject_id FROM 
					component WHERE course_id='$rcrid' AND id='$bid';");
				if(mysql_num_rows($d_com)>0){
					/* Or it could be a strand. */
					$bid=mysql_result($d_com,0);
					}
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

		if($eid!='' and $sid!=''){
			$result[]="Assessment_id=".$eid.": sid=".$sid." Subject/Component:".$bid.":::".$pid." score=".$inscore['result']."-".$inscore['value']."<br>";
			update_assessment_score($eid,$sid,$bid,$pid,$inscore);
			$AssCount=fetchAssessmentCount($eid);
			if($AssCount['MarkCount']['value']>0){
				delete_assessment_columns($eid);
				generate_assessment_columns($eid);
				}
			}
		}
	}

#include('scripts/results.php');
#include('scripts/redirect.php');
?>
