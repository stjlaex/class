<?php 
/** 		   						edit_scores_action.php
 */

$action='class_view.php';

$viewtable=$_SESSION['viewtable'];
$umns=$_SESSION['umns'];
$mid=$_POST['mid'];
$col=$_POST['col'];
$scoretype=$_POST['scoretype'];
$grading_grades=$_POST['grading_grades'];
$total=clean_text($_POST['total']);

include('scripts/sub_action.php');

if($sub=='Submit'){

	if($umns[$col]['assessment']!='no'){
		$todate=date('Y').'-'.date('n').'-'.date('j');
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
				which is always so when columns have been auto-generated*/
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
		if(isset($_POST["total$sid"])){$intotal=clean_text($_POST["total$sid"]);}else{$intotal='';}
		/* $$sid are the names of score values posted by the form
		 * if the value is empty then score will be unset and no entry made
		 */
		$inscore=clean_text($_POST[$sid]);
		$incomm=clean_text($_POST['comm'.$sid]);
		if(isset($_POST['extra'.$sid])){$inextra='1';}
		else{$inextra='0';}/* Only used by hw scores at the moment. */
		$ingrade='';
		if($inscore=='' and $incomm=='' and $inextra=='0'){unset($inscore);}
		elseif($scoretype=='grade'){
			$ingrade=$inscore;
			$res=scoreToGrade($inscore,$grading_grades);
			}
		elseif($scoretype=='value'){
			$res=$inscore;
			}
		elseif($scoretype=='percentage'){
			if($intotal=='' or $intotal=='0'){$intotal=$total;}
			list($out,$res,$outrank)=scoreToPercent($inscore,$intotal);
			}
		elseif($scoretype=='comment'){
			$inscore='';
			}

		if(isset($eid) and isset($res)){
			$ass_score=array('result'=>$res,'value'=>$inscore,'date'=>$todate,'comment'=>$incomm);
			update_assessment_score($eid,$sid,$bid,$pid,$ass_score);
			}

/*		Tidy up any empty value entries by deleting their score 
 *		entry - one of the above must have unset $inscore to indicate this
 */   	
		if(!isset($inscore)){
			mysql_query("DELETE FROM score WHERE
					mark_id='$mid' AND student_id='$sid' LIMIT 1");
			if(isset($eid)){
				$d_eidsid=mysql_query("SELECT id FROM eidsid
					WHERE subject_id='$bid' AND component_id='$pid' 
					AND assessment_id='$eid' AND student_id='$sid'");
				if(mysql_num_rows($d_eidsid)!=0){
					$eidsidid=mysql_result($d_eidsid,0);
					mysql_query("DELETE FROM eidsid WHERE id='$eidsidid';");
					mysql_query("DELETE FROM comments WHERE eidsid_id='$eidsidid';");
					}
				}
			}
		elseif($inscore=='' and ($incomm!='' or $inextra!='0')){
			if(mysql_query("INSERT INTO score (value, grade, outoftotal, comment,
						   	extra, mark_id, student_id) VALUES
							(NULL, NULL, '$intotal', 
							'$incomm', '$inextra', '$mid', '$sid');")){}
			else{mysql_query("UPDATE score SET grade=NULL, value=NULL, 
							outoftotal='$intotal', comment='$incomm', extra='$inextra'
							WHERE mark_id='$mid' AND
							student_id='$sid';");}
			}
		else{
			if(mysql_query("INSERT INTO score (value, grade, outoftotal, comment,
						   	extra, mark_id, student_id) VALUES
							('$inscore', '$ingrade', '$intotal', 
							'$incomm', '$inextra', '$mid', '$sid');")){}
			else{
				mysql_query("UPDATE score SET value='$inscore', grade='$ingrade',
							outoftotal='$intotal', comment='$incomm', extra='$inextra' 
							WHERE mark_id='$mid' AND
							student_id='$sid';");}
			}
		}
	}

include('scripts/redirect.php');
?>
