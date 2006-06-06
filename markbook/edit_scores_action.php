<?php 
/** 		   						edit_scores_action.php
 */

$action='class_view.php';

$viewtable=$_SESSION{'viewtable'};
$umns=$_SESSION{'umns'};
$mid=$_POST{'mid'};
$col=$_POST{'col'};
$scoretype=$_POST{'scoretype'};
$grading_grades=$_POST{'grading_grades'};
$total=clean_text($_POST{'total'});

include('scripts/sub_action.php');

if($sub=='Submit'){
	if($umns["$col"]['assessment']=='yes'){
		$todate=date('Y')."-".date('n')."-".date('j');
		/*if associated with an assessment then find which*/
		$d_assessment=mysql_query("SELECT id, subject_id, component_id FROM assessment JOIN
				eidmid ON assessment.id=eidmid.assessment_id WHERE eidmid.mark_id='$mid'");
		$ass=mysql_fetch_array($d_assessment,MYSQL_ASSOC);
		$eid=$ass['id'];
		$bid=$ass['subject_id'];
		$pid=$ass['component_id'];
		if($bid=='%'){
			/*any value other than % means this eid is for a single bid and
				is already explicity defined, probably as G for
				general. Note G for general cannot be found from midcid anyway!
				And the mid must only be linked to classes for a single bid -
				which is always the case if columns have been auto-generated*/
				$d_bid=mysql_query("SELECT DISTINCT subject_id FROM class JOIN midcid ON
					midcid.class_id=class.id WHERE midcid.mark_id='$mid'");
				$bid=mysql_result($d_bid,0);
				}
		if($pid==''){
				$d_pid=mysql_query("SELECT component_id FROM mark WHERE id='$mid'");
				$pid=mysql_result($d_pid,0);
				}
		}

	for($c=0;$c<sizeof($viewtable);$c++){
		unset($res);
		$sid=$viewtable[$c]['sid'];
		if(isset($_POST{"total$sid"})){$intotal=clean_text($_POST{"total$sid"});}
		$inscore=clean_text($_POST{"$sid"});
		$incomm=clean_text($_POST{"comm$sid"});
		/*$$sid are the names of score values posted by the form*/
		/*if the value is empty then score will be unset and no entry made*/
		if($scoretype=='grade'){
			if($inscore==''){unset($inscore);}
			else{
				if(mysql_query("INSERT INTO score (grade,
					comment, mark_id, student_id) VALUES
					('$inscore', '$incomm', '$mid', '$sid')")){}
				elseif(mysql_query("UPDATE score SET
							grade='$inscore', comment='$incomm' 
						WHERE mark_id='$mid' AND student_id='$sid'")){}
				else{$error[]='Failed!'.mysql_error();}
				$res=scoreToGrade($inscore,$grading_grades);
			    }
			}
		elseif($scoretype=='value'){
			if($inscore==''){unset($inscore);}
			else{
				if(mysql_query("INSERT INTO score (value,
						comment, mark_id, student_id) VALUES
						('$inscore', '$incomm', '$mid', '$sid')")){}
				elseif(mysql_query("UPDATE score SET
									value='$inscore', comment='$incomm' 
									WHERE mark_id='$mid' AND student_id='$sid'")){}
				else{$error[]=mysql_error();}		   
				$res=$inscore;
				}
			}
		elseif($scoretype=='percentage'){
			if($inscore==''){unset($inscore);}
			else{
				if($intotal==''){$intotal=$total;}
				if(mysql_query("INSERT INTO score (value, outoftotal, comment,
						   	mark_id, student_id) VALUES
							('$inscore', '$intotal', '$incomm', '$mid', '$sid')")){}
				elseif(mysql_query("UPDATE score SET value='$inscore',
							outoftotal='$intotal', comment='$incomm' 
							WHERE mark_id='$mid' AND student_id='$sid'")){}
				else {$error[]=mysql_error();}		   
				include('markbook/percent_score.php');
				if(isset($percent)){
					$res=$percent.' ('.number_format($score_value,1,'.','').')';
					}
				else{$res='';}
				}
			}
		elseif($scoretype=='comment'){
			if($incomm==''){unset($inscore);}
			else{
				set($inscore);
				if(mysql_query("INSERT INTO score (comment, 
					   	mark_id, student_id) VALUES ('$incomm', '$mid', '$sid')")){}
				elseif(mysql_query("UPDATE score SET comment='$incomm',
						WHERE mark_id='$mid' AND student_id='$sid'")){}
				else {$error[]=mysql_error();}
				}
			}
		elseif($scoretype=='tier'){
			if($inscore==''){unset($inscore);}
			else{
				if (mysql_query("INSERT INTO score (tier, 
					   	mark_id, student_id) VALUES ('$inscore', '$mid', '$sid')")){}
				elseif (mysql_query("UPDATE score SET tier='$inscore',
						WHERE mark_id='$mid' AND student_id='$sid'")){}
				else {$error[]=mysql_error();}		   
				}
			}

		if(isset($eid) and isset($res)){
			$d_eidsid=mysql_query("SELECT id FROM eidsid
				WHERE subject_id='$bid' AND component_id='$pid' 
				AND assessment_id='$eid' AND student_id='$sid'");
			if(mysql_num_rows($d_eidsid)==0){
				mysql_query("INSERT INTO eidsid (assessment_id,
					student_id, subject_id, component_id, result, value, date) 
					VALUES ('$eid','$sid','$bid','$pid','$res','$inscore','$todate');");
				}
			else{
				$eidsidid=mysql_result($d_eidsid,0);
				mysql_query("UPDATE eidsid SET result='$res',
				 value='$inscore', date='$todate' WHERE id='$eidsidid'");
				}
			}

/*		Tidy up any empty value entries by deleting their score 
 *		entry - one of the above must have unset $inscore to indicate this
 */		   	
		if(!isset($inscore)){
			if (mysql_query("DELETE FROM score WHERE
					mark_id='$mid' AND student_id='$sid' LIMIT 1")){}
			else {$error[]=mysql_error();}

			if(isset($eid)){
				$d_eidsid=mysql_query("SELECT id FROM eidsid
					WHERE subject_id='$bid' AND component_id='$pid' 
					AND assessment_id='$eid' AND student_id='$sid'");
				if(mysql_num_rows($d_eidsid)!=0){
					$eidsidid=mysql_result($d_eidsid,0);
					mysql_query("DELETE FROM eidsid WHERE id='$eidsidid'");
					}
				}
			}
		}
	}

include('scripts/redirect.php');
?>
