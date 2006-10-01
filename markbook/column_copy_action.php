<?php 
/** 									column_copy_action.php
 */

$action='class_view.php';

$mid=$_POST['mid'];
$marktype=$_POST['marktype'];
$lena=$_POST['lena'];
$total=$_POST['total'];
$scale=$_POST['scale'];
$oldtotal=$_POST['oldtotal'];
$midlist=$_POST['midlist'];
$bid=$_POST['bid'];
$crid=$_POST['crid'];
$entrydate=$_POST['date0'];
$comment=$_POST['comment'];	
$def_name=$_POST['def_name'];
$topic=$_POST['topic'];

include('scripts/sub_action.php');

if($sub=='Submit'){

	mysql_query("INSERT INTO mark (entrydate, marktype, topic, 
		total, comment, author, def_name) 
		VALUES ('$entrydate', 'score', '$topic', '$total', 
		     '$comment',  '$tid', '$def_name')");
	$newmid=mysql_insert_id();
	$displaymid=$newmid;

	/*Copy for each class that is assigned that mark not just */
	/*						those in the view table.*/
	$d_midcid=mysql_query("SELECT class_id FROM midcid WHERE mark_id='$mid'");	
	while($midcid=mysql_fetch_array($d_midcid,MYSQL_ASSOC)){
		$cid=$midcid['class_id'];
		mysql_query("INSERT INTO midcid 
		     (mark_id, class_id) VALUES ('$newmid', '$cid')");

		$d_markdef=mysql_query("SELECT scoretype FROM 
								markdef WHERE name='$def_name'");
		$newscoretype=mysql_result($d_markdef,0);

		if($marktype=='score'){
			$d_score=mysql_query("SELECT a.student_id, b.value,
				b.outoftotal, b.grade FROM cidsid a, score b WHERE
				a.class_id='$cid' AND b.mark_id='$mid' AND b.student_id=a.student_id");
			while($score=mysql_fetch_array($d_score, MYSQL_ASSOC)){
				$sid=$score['student_id'];
				$scoretotal=$score['outoftotal'];
				$scorevalue=$score['value'];
				if($scoretotal!='' and $total!=0 and $scale=='yes'){
					$newtotal=$total*$scoretotal/$oldtotal;
					$scorevalue=$scorevalue*$total/$oldtotal;
					$scoretotal=$newtotal;
					}
				elseif($total!=0 and $oldtotal!=0){
					$scoretotal=$total*$scoretotal/$oldtotal;
					}
				else{
					$scoretotal=$total;
					}
				mysql_query("INSERT INTO score (mark_id,
						student_id,value,outoftotal) VALUES
						('$newmid', '$sid', '$scorevalue', '$scoretotal')");
				}
			}

		/*****************************************/
		elseif($marktype=='sum'){
		   /*Mark is the sum of several score values*/
		   $mids=explode(' ',$midlist);
		   $d_student=mysql_query("SELECT student_id FROM cidsid WHERE class_id='$cid'");
		   while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
			   $sid=$student['student_id'];
			   unset($score_value);
			   $score_total=0;
			   for($c2=0;$c2<sizeof($mids);$c2++){
				  $d_score=mysql_query("SELECT value, outoftotal 
							FROM score WHERE mark_id='$mids[$c2]' AND student_id='$sid'");
				  $score=mysql_fetch_array($d_score,MYSQL_ASSOC);
				  if($score_value){$score_value=$score['value']+$score_value;}
							else{$score_value=$score['value'];}
				  $score_total=$score['outoftotal']+$score_total;
				  }
			   if($score_total==0){$score_total=$total;}
			   mysql_query("INSERT INTO score (mark_id, 
						student_id, value, outoftotal) VALUES 
						('$newmid', '$sid','$score_value', '$score_total')");
			   }
			}
			
		/********************************************/
	   	elseif($marktype=='average' and $newscoretype=='grade'){
			/*		Mark is average of several score values*/
			$mids=explode(' ',$midlist);
			$d_student=mysql_query("SELECT student_id FROM cidsid WHERE class_id='$cid'");
			while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
				$sid=$student['student_id'];
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
					mysql_query("INSERT INTO score 
							(mark_id, student_id, grade) VALUES 
							('$newmid', '$sid', '$score_grade')");
					}
				}
			}

	   	elseif($marktype=='average' and $newscoretype=='percentage'){
			/*		Mark is average of several score values*/
			$mids=explode(' ',$midlist);
			$d_student=mysql_query("SELECT student_id FROM cidsid WHERE class_id='$cid'");
			while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
				$sid=$student['student_id'];
				unset($score_value);
				$scoresum=0;
				$scorecount=0;
				$score_total=0;
				for($c2=0;$c2<sizeof($mids);$c2++){
					$d_score=mysql_query("SELECT value, outoftotal 
							FROM score WHERE mark_id='$mids[$c2]' AND student_id='$sid'");
					$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
					if($score['value']){
						$score_value=$score['value'];
						$score_total=$score['outoftotal'];
						include('percent_score.php'); 
						$scoresum=$cent+$scoresum;
						$scorecount++;
						}
					}
				if($scorecount>0){
					$score_value=$scoresum/$scorecount;
					$score_total=100;
					}
				mysql_query("INSERT INTO score (mark_id, 
						student_id, value, outoftotal) VALUES 
						('$newmid', '$sid','$score_value', '$score_total')");
				}
			}

		/*******************************/
	   	elseif($marktype=='level'){
			$result[]='Copying of levels is not yet implemented.';
			}
		}
	}

include('scripts/redirect.php');
?>
