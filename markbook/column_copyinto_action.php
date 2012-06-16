<?php 
/** 									column_copyinto_action.php
 */

$action='class_view.php';
$action_post_vars=array('displaymid');

$copyfrommid=$_POST['mid0'];
$copytomid=$_POST['mid1'];

include('scripts/sub_action.php');

if($sub=='Submit'){


	$d_mark=mysql_query("SELECT * FROM mark WHERE id='$copyfrommid';");
	$mark=mysql_fetch_array($d_mark,MYSQL_ASSOC);
	$marktype=$mark['marktype'];
	$midlist=$mark['midlist'];
	foreach($cids as $cid){
		$d_s=mysql_query("SELECT student_id FROM cidsid WHERE class_id='$cid'");
		while($s=mysql_fetch_array($d_s,MYSQL_ASSOC)){
			$sids[]=$s['student_id'];
			}
		}


	$d_mark=mysql_query("SELECT * FROM mark WHERE id='$copytomid';");
	$copytomark=mysql_fetch_array($d_mark,MYSQL_ASSOC);
	$markdefname=$mark['def_name'];
	$d_markdef=mysql_query("SELECT DISTINCT scoretype, grading_name, outoftotal FROM markdef WHERE name='$markdefname'");
	$newscoretype=mysql_result($d_markdef,0,0);
	$newgrading_name=mysql_result($d_markdef,0,1);
	if($copytomark['assessment']!='no'){
		$todate=date('Y').'-'.date('n').'-'.date('j');

		if($newscoretype=='grade'){
			$d_grading=mysql_query("SELECT grades FROM grading WHERE name='$newgrading_name'");
			$grading_grades=mysql_result($d_grading,0);
			}
		/*if associated with an assessment then find which*/
		$d_assessment=mysql_query("SELECT id, subject_id, component_id FROM assessment JOIN
				eidmid ON assessment.id=eidmid.assessment_id WHERE eidmid.mark_id='$copytomid'");
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
					midcid.class_id=class.id WHERE midcid.mark_id='$copytomid'");
			$bid=mysql_result($d_bid,0);
			}
		if($pid==''){
			$pid=$copytomark['component_id'];
			}
		}



	if($marktype=='score'){
			$d_score=mysql_query("SELECT a.student_id, b.value,
				b.outoftotal, b.grade FROM cidsid a, score b WHERE
				a.class_id='$cid' AND b.mark_id='$copyfrommid' AND b.student_id=a.student_id");
			while($score=mysql_fetch_array($d_score, MYSQL_ASSOC)){
				$sid=$score['student_id'];
				$scoretotal=$score['outoftotal'];
				$scorevalue=$score['value'];
				$scoregrade=$score['grade'];
				mysql_query("DELETE FROM score WHERE mark_id='$copytomid' AND student_id='$sid' LIMIT 1");
				mysql_query("INSERT INTO score (mark_id, student_id, grade, value, outoftotal) VALUES
						('$copytomid', '$sid', '$scoregrade', '$scorevalue', '$scoretotal')");
				if(isset($eid)){
					if($newscoretype=='grade'){
						$res=scoreToGrade($scoregrade,$grading_grades);
						}
					elseif($newscoretype=='value'){
						$res=$scorevalue;
						}
					elseif($newscoretype=='percentage'){
						if($scoretotal=='' or $scoretotal=='0'){$scoretotal=$copyfrommark['total'];}
						list($out,$res,$outrank)=scoreToPercent($scorevalue,$scoretotal);
						}
					$ass=array('result'=>$res,'value'=>$scorevalue,'date'=>$todate);
					update_assessment_score($eid,$sid,$bid,$pid,$ass);
					}
				}
			}

		/*****************************************/
		elseif($marktype=='sum'){
			/* Mark is the sum of several score values*/
			$mids=explode(' ',$midlist);
			foreach($sids as $sid){
				unset($scorevalue);
				$scoretotal=0;
				for($c2=0;$c2<sizeof($mids);$c2++){
					$d_score=mysql_query("SELECT value, outoftotal 
							FROM score WHERE mark_id='$mids[$c2]' AND student_id='$sid'");
					$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
					if($scorevalue){$scorevalue=$score['value']+$scorevalue;}
					else{$scorevalue=$score['value'];}
					$scoretotal=$score['outoftotal']+$scoretotal;
					}
				if($scoretotal==0){$scoretotal=$total;}
				mysql_query("DELETE FROM score WHERE mark_id='$copytomid' AND student_id='$sid' LIMIT 1");
				mysql_query("INSERT INTO score (mark_id, student_id, value, outoftotal) VALUES 
						('$copytomid', '$sid','$scorevalue', '$scoretotal')");
				if(isset($eid)){
					if($newscoretype=='grade'){
						$res=scoreToGrade($scoregrade,$grading_grades);
						}
					elseif($newscoretype=='value'){
						$res=$scorevalue;
						}
					elseif($newscoretype=='percentage'){
						if($scoretotal=='' or $scoretotal=='0'){$scoretotal=$copyfrommark['total'];}
						list($out,$res,$outrank)=scoreToPercent($scorevalue,$scoretotal);
						}
					$ass=array('result'=>$res,'value'=>$scorevalue,'date'=>$todate);
					update_assessment_score($eid,$sid,$bid,$pid,$ass);
					}
				}
			}
			
		/********************************************/
	   	elseif($marktype=='average' and $newscoretype=='grade'){
			/* Mark is average of several score values*/
			$mids=explode(' ',$midlist);
		   foreach($sids as $sid){
				$gradesum=0;
				$gradecount=0;
				for($c2=0;$c2<sizeof($mids);$c2++){
					$d_score=mysql_query("SELECT grade FROM score 
							WHERE mark_id='$mids[$c2]' AND student_id='$sid'");
					$grade=mysql_fetch_array($d_score,MYSQL_ASSOC);
					if(isset($grade['grade'])){$gradesum=$gradesum+$grade['grade'];
					$gradecount++;}
					}
				if($gradecount>0){
					$score_grade=$gradesum/$gradecount;
					$score_grade=round($score_grade);
					mysql_query("DELETE FROM score WHERE mark_id='$copytomid' AND student_id='$sid' LIMIT 1");
					mysql_query("INSERT INTO score (mark_id, student_id, grade) VALUES 
							('$copytomid', '$sid', '$score_grade')");
					if(isset($eid)){
						$res=scoreToGrade($scoregrade,$grading_grades);
						$ass=array('result'=>$res,'value'=>$scorevalue,'date'=>$todate);
						update_assessment_score($eid,$sid,$bid,$pid,$ass);
						}
					}
				}
			}

		/********************************************/
	   	elseif($marktype=='average' and $newscoretype=='value'){
			/* Mark is average of several score values*/
			$mids=explode(' ',$midlist);
			foreach($sids as $sid){
				unset($scorevalue);
				$scoresum=0;
				$scorecount=0;
				$scoretotal=0;
				foreach($mids as $mid){
					$d_score=mysql_query("SELECT value FROM score WHERE mark_id='$mid' AND student_id='$sid';");
					if(mysql_num_rows($d_score)>0){
						$score=mysql_result($d_score,0);
						$scoresum=+$score;
						$scorecount++;
						}
					}

				if($scorecount>0){
					$scorevalue=$scoresum/$scorecount;
					mysql_query("DELETE FROM score WHERE mark_id='$copytomid' AND student_id='$sid' LIMIT 1");
					mysql_query("INSERT INTO score (mark_id, student_id, value) VALUES 
							('$copytomid', '$sid', '$scorevalue')");
					if(isset($eid)){
						$res=$scorevalue;
						$ass=array('result'=>$res,'value'=>$scorevalue,'date'=>$todate);
						update_assessment_score($eid,$sid,$bid,$pid,$ass);
						}
					}
				}
			}

	   	elseif($marktype=='average' and $newscoretype=='percentage'){
			/*		Mark is average of several score values*/
			$mids=explode(' ',$midlist);
			foreach($sids as $sid){
				unset($scorevalue);
				$scoresum=0;
				$scorecount=0;
				$scoretotal=0;
				for($c2=0;$c2<sizeof($mids);$c2++){
					$d_score=mysql_query("SELECT value, outoftotal 
							FROM score WHERE mark_id='$mids[$c2]' AND student_id='$sid'");
					$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
					if($score['value']){
						list($out,$percent,$outrank)=scoreToPercent($score['value'],$score['outoftotal']);
						$scoresum+=$percent;
						$scorecount++;
						}
					}
				if($scorecount>0){
					$scorevalue=round($scoresum/$scorecount)/10;
					$scoretotal=100;
					}
				mysql_query("DELETE FROM score WHERE mark_id='$copytomid' AND student_id='$sid' LIMIT 1");
				mysql_query("INSERT INTO score (mark_id, student_id, value, outoftotal) VALUES 
						('$copytomid', '$sid', '$scorevalue', '$scoretotal')");
				if(isset($eid)){
					if($scoretotal=='' or $scoretotal=='0'){$scoretotal=100;}
					list($out,$res,$outrank)=scoreToPercent($scorevalue,$scoretotal);
					$ass=array('result'=>$res,'value'=>$scorevalue,'date'=>$todate);
					update_assessment_score($eid,$sid,$bid,$pid,$ass);
					}

				}
			}

		/*******************************/
	   	elseif($marktype=='level'){
			$result[]='Copying of levels is not yet implemented.';
			}
		}

include('scripts/redirect.php');
?>
