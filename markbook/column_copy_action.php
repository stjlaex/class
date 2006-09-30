<?php 
/** 									column_copy_action.php
 */

$action='class_view.php';

$mid=$_POST['mid'];
$marktype=$_POST['marktype'];
$lena=$_POST['lena'];
if(isset($_POST['total'])){$total=$_POST['total'];}
if(isset($_POST['scale'])){$scale=$_POST['scale'];}else{$scale='no';}
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
	if(mysql_query("INSERT INTO mark (entrydate, marktype, topic, 
		total, comment, author, def_name) 
		VALUES ('$entrydate', 'score', '$topic', '$total', 
		     '$comment',  '$tid', '$def_name')")){}
    else{
	    $error[]='Failed mark may already exist!';	
		$error[]=mysql_error(); 
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}
	$newmid=mysql_insert_id();
	$displaymid=$newmid;

/*	Do the copy for each class that is assigned that mark not just */
/*						those in the view table.*/

		$d_midcid=mysql_query("SELECT class_id FROM midcid WHERE mark_id='$mid'");	
		while($midcid=mysql_fetch_array($d_midcid,MYSQL_ASSOC)){
		$cid=$midcid['class_id'];
		if(mysql_query("INSERT INTO midcid 
		     (mark_id, class_id) VALUES ('$newmid', '$cid')")){}
		else{$error[]=mysql_error();}

		/* 		Copy the score rows into new scores with the new mark_id*/	
		$d_student = mysql_query("SELECT a.student_id, b.surname,
				b.forename FROM cidsid a, student b WHERE
				a.class_id='$cid' AND b.id=a.student_id ORDER BY b.surname");
		$c=0;
		$d_markdef=mysql_query("SELECT scoretype FROM 
								markdef WHERE name='$def_name'");
		$newscoretype=mysql_result($d_markdef,0);

		if($marktype=='score'){
			while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
				$sid = $student['student_id'];
				$d_old=mysql_query("SELECT * FROM score WHERE
						mark_id='$mid' AND student_id='$sid'");
				$old=mysql_fetch_array($d_old,MYSQL_BOTH);
				if($old[6]!='' and isset($total) and $scale=='yes'){
					$newtotal=$total*$old[6]/$oldtotal;
					$old[3]=$old[3]*$total/$oldtotal;
					$old[6]=$newtotal;
					}
				elseif(isset($total)){
					$old[6]=$total*$old[6]/$oldtotal;
					}

				if($old[0]==$mid){
		   			mysql_query("INSERT INTO score () VALUES
						('$newmid', '$sid', '$old[2]', '$old[3]', '$old[4]',
						'$old[5]', '$old[6]')");
				    }
				}
			}
			
		/*****************************************/
	   elseif($marktype=='sum'){
		   /*		Mark is the sum of several score values*/
				$mids=explode(' ',$midlist);
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
					if (mysql_query("INSERT INTO score (mark_id, 
						student_id, value, outoftotal) VALUES 
						('$newmid', '$sid','$score_value', '$score_total')")){}
					else{$result[]='Failed to insert new score!';	
							$error[]=mysql_error();}
					}

//					if($mark_total<$score_total){$mark_total=$score_total;}	
//					}
//			if(mysql_query("UPDATE mark SET total='$mark_total' WHERE id='$newmid'")){}
//				else{$result[]='Failed to update total!';	
//							$error[]=mysql_error();}
			}
			
	   /********************************************/
	   	elseif($marktype=='average' and $newscoretype=='grade'){
			/*		Mark is average of several score values*/
				$mids=explode(' ',$midlist);
				while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
					$sid=$student{'student_id'};
					$gradesum=0;
					$gradecount=0;
					for($c2=0; $c2<sizeof($mids); $c2++){
						$d_score=mysql_query("SELECT grade FROM score 
							WHERE mark_id='$mids[$c2]' AND student_id='$sid'");
						$grade=mysql_fetch_array($d_score,MYSQL_ASSOC);
						if(isset($grade{'grade'})){$gradesum=$gradesum+$grade{'grade'};
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
				while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
					$sid=$student['student_id'];
					unset($score_value);
					$scoresum=0;
					$scorecount=0;
					$score_total=0;
					for($c2=0; $c2<sizeof($mids); $c2++){
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
		
//					if($mark_total<$score_total){$mark_total=$score_total;}	
//					}
//			if(mysql_query("UPDATE mark SET total='$mark_total' WHERE id='$newmid'")){}
//				else{$result[]='Failed to update total!';	
//							$error[]=mysql_error();}
			   	}

		/*******************************/
	   	elseif($marktype=='level'){
			/*		Mark is the levelled grade of a score*/
				$mid=$midlist;
				$d_levelling=mysql_query("SELECT * FROM levelling WHERE name='$lena'");
				$levelling=mysql_fetch_array($d_levelling,MYSQL_ASSOC);
				$levelling_levels=$levelling{'levels'};
				$gena=$levelling{'grading_name'};
				$d_grading=mysql_query("SELECT * FROM grading WHERE name='$gena'");
				$grading=mysql_fetch_array($d_grading,MYSQL_ASSOC);			
				$grading_grades=$grading{'grades'};
						
				while ($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
					$sid = $student{'student_id'};
					$score_value=0;
					$score_total=0;
					$d_score=mysql_query("SELECT value, 
						outoftotal FROM score WHERE mark_id='$mid' 
						AND student_id='$sid'");
					$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
			      	$score_value=$score{'value'};
			      	$score_total=$score{'outoftotal'};
		   			$result[]=$mid.$score_value;		      	
			      	include('percent_score.php');
			      	if(isset($cent)){
			      		include('level_score.php');
						$score_grade=$grade;
						include('score_grade.php');
						$result[]=$score_grade;			      	
			      		mysql_query("INSERT INTO score (mark_id,
							student_id, grade) VALUES ('$newmid', '$sid', '$score_grade')");
						}
	  				}
				}
			}
		}

include('scripts/redirect.php');
?>



















































