<?php
/**							class_view_table.php
 *
 * Generates the array $viewtable and stores as a session variable.
 *
 *
 */

$d_students=mysql_query("SELECT * FROM students ORDER BY surname, forename"); 
$row=0;
$viewtable=array();

	while($student=mysql_fetch_array($d_students, MYSQL_ASSOC)){
		$sid=$student['student_id'];
		$SEN_field=fetchStudent_singlefield($sid,'SENFlag');
		$Medical_field=fetchStudent_singlefield($sid,'MedicalFlag');
		$comment=comment_display($sid);
		$Attendance=fetchcurrentAttendance($sid);

		if($student['preferredforename']!=''){
			$displaypfn='&nbsp;('.$student['preferredforename'].')';
			}
		else{$displaypfn='';}
		$studentrow=array('row'=>$row, 
						  'sen'=>$SEN_field['SENFlag']['value'],
						  'medical'=>$Medical_field['MedicalFlag']['value'],
						  'commentclass'=>$comment['class'], 
						  'commentbody'=>$comment['body'], 
						  'attstatus'=>$Attendance['Status']['value'], 
						  'attcode'=>$Attendance['Code']['value'], 
						  'attcomment'=>$Attendance['Comment']['value'], 
						  'atttime'=>$Attendance['Logtime']['value'], 
						  'sid'=>$sid,
						  'surname'=>$student['surname'],
						  'forename'=>$student['forename'],
						  'preferredforename'=>$displaypfn,
						  'form_id'=>$student['form_id'],
						  'class_id'=>$student['class_id']
						  );

		for($c=0;$c<$c_marks;$c++) {
			$col_mid=$umns[$c]['id'];
			$score=array();

			/* The mark can be one of the five kinds - if a score or hw
			 * then one of a further five.
			 */
			$marktype=$umns[$c]['marktype'];
			$scoretype=$umns[$c]['scoretype'];
			$asstype=$umns[$c]['assessment'];
			$scoreclass='grade';/* Will only be overridden by a report. */
			if($marktype=='score' or $marktype=='hw'){
				if($asstype=='other'){$scoreclass.=' other';}
				$d_score=mysql_query("SELECT * FROM score 
					WHERE mark_id='$col_mid' AND student_id='$sid';");
				$score=mysql_fetch_array($d_score,MYSQL_ASSOC);				
				/*score can be one of four types: grade, value, percentage, comment*/
				if($scoretype=='grade'){
					$out=scoreToGrade($score['grade'],$scoregrades[$c]);
					$outrank=$score['grade'];
					}
				elseif($scoretype=='value'){
					$out=$score['value'];
					$outrank=$score['value'];			      
					}
				elseif($scoretype=='percentage'){
					list($out,$percent,$outrank)=scoreToPercent($score['value'],$score['outoftotal']);
					}
				elseif($scoretype=='comment'){
					$out=$score['comment'];
					$outrank=-100;
					}
				}
			/*********************************************************/
			elseif($marktype=='average'){
				/*Mark is average of several score values*/
				$scoreclass.=' derived';
				$avmids=explode(' ',$midlist[$c]);
				if($umns[$c]['scoretype']=='grade'){
					$grading_grades=$scoregrades[$c];
					$gradesum=0;
					$gradecount=0;
					for($c2=0;$c2<sizeof($avmids);$c2++){
						$d_score=mysql_query("SELECT grade FROM score 
		   					WHERE mark_id='$avmids[$c2]' AND student_id='$sid';");
						$grade=mysql_fetch_array($d_score,MYSQL_ASSOC);
						if(isset($grade['grade'])){
							$gradesum=$gradesum+$grade['grade'];
							$gradecount++;
							}
						}
					if($gradecount>0){
						$score_grade=$gradesum/$gradecount;
						$score_grade=round($score_grade);
						$grade=scoreToGrade($score_grade,$grading_grades);
						$out=$grade;
						$outrank=$score_grade;
						}
					else{$outrank=-100;$out='';unset($score_grade);}
					}
				elseif($umns[$c]['scoretype']=='percentage'){
					$scoresum=0;
					$scorecount=0;
					for($c2=0;$c2<sizeof($avmids);$c2++){
						$d_score=mysql_query("SELECT value, outoftotal 
						FROM score WHERE mark_id='$avmids[$c2]' AND student_id='$sid';");
						$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
						if($score['value']){
							list($dislpay,$percent,$cent)=scoreToPercent($score['value'],$score['outoftotal']);
							$scoresum+=$cent;
							$scorecount++;
							}
						}
					if($scorecount>0){$scoresum=$scoresum/$scorecount;}
					list($display,$out,$outrank)=scoreToPercent($scoresum);
					}
				else{
					$scoresum=0;
					$scorecount=0;
					for($c2=0;$c2<sizeof($avmids);$c2++){
						$d_score=mysql_query("SELECT value 
						FROM score WHERE mark_id='$avmids[$c2]' AND student_id='$sid'");
						$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
						if($score['value']){
							$scoresum=$scoresum+$score['value'];
							$scorecount++;
							}
						}
					if($scorecount>0){
						$scoresum=$scoresum/$scorecount;
						$out=$scoresum;$outrank=$scoresum;
						}
					else{$out='';$outrank=-100;}
					}
				}

		/*********************************************************/
	   	elseif($marktype=='sum'){
			/*Mark is the sum of several score values*/
			$scoreclass.=' derived';
			$mids=explode(' ',$midlist[$c]);
			$score_value=0;
			$score_total=0;
			for($c2=0;$c2<sizeof($mids);$c2++){
				$d_score=mysql_query("SELECT value, outoftotal 
   						FROM score WHERE mark_id='$mids[$c2]' AND student_id='$sid';");
				$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
				if($score['value']){$score_value+=$score['value']; $yesval=1;}
				if($score['outoftotal']){
					$score_total+=$score['outoftotal']; 
					$yestotal=1;
					}
				}
			if(isset($yestotal)){
				/*mark's were percentage scores*/
				list($dislpay,$out,$outrank)=scoreToPercent($score_value,$score_total);
				}
			else{
				/*otherwise mark's were raw scores*/
				if(isset($yesval)){$out=$score_value; $outrank=$score_value;}
				else{$out='';$outrank=-100;}
				}
			//$out=$score_value; WHY WAS IT HERE?
			unset($yesval);
			unset($yestotal);
			}

		/*********************************************************/
	   	elseif($marktype=='dif'){
			/*Mark is the difference of two score values*/
			$scoreclass.=' derived';
			$profilemids=(array)explode(' ',$midlist[$c]);
			$score_value=0;
			$score_total=0;
			$d_score=mysql_query("SELECT value, outoftotal 
   						FROM score WHERE mark_id='$profilemids[0]' AND student_id='$sid';");
			$firstscore=mysql_fetch_array($d_score,MYSQL_ASSOC);
			for($lastmid=sizeof($profilemids)-1;$lastmid>0;$lastmid--){
				$d_score=mysql_query("SELECT value, outoftotal 
   						FROM score WHERE mark_id='$profilemids[$lastmid]' AND student_id='$sid';");
				$lastscore=mysql_fetch_array($d_score,MYSQL_ASSOC);
				if(isset($lastscore['value'])){$lastmid=-1;}
				}
			if($firstscore['value'] and $lastscore['value']){$score_value=$lastscore['value']-$firstscore['value']; $yesval=1;}
			if(isset($yesval)){$out=$score_value; $outrank=$score_value;}
			else{$out='';$outrank=-100;}
			unset($yesval);
			unset($yestotal);
			unset($firstscore);
			unset($lastscore);
			}

		/*********************************************************/
	   	elseif($marktype=='tally'){
			/* Mark tallies (counts) the number of grades obtained */
			$scoreclass.=' derived';
			$mids=explode(' ',$midlist[$c]);
			$score_value=0;
			for($c2=0;$c2<sizeof($mids);$c2++){
				$d_score=mysql_query("SELECT value 
   						FROM score WHERE mark_id='$mids[$c2]' AND student_id='$sid';");
				$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
				if($score['value']){$score_value++; $yesval=1;}
				}
			if(isset($yesval)){$out=$score_value; $outrank=$score_value;}
			else{$out='';$outrank=-100;}
			unset($yesval);
			}

		/*********************************************************/
	   	elseif($marktype=='level'){
			$scoreclass.=' derived';
			/*then mark is the levelled grade of a score*/
			$d_score=mysql_query("SELECT value, outoftotal FROM 
					score WHERE mark_id='$midlist[$c]' AND student_id='$sid';");
			$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
			list($out,$outrank)=scoreToLevel($score['value'],$score['outoftotal'],$levels[$c]);
			}

		/*********************************************************/
	   	elseif($marktype=='oldcompound'){
			/*Mark is a compound column*/
			$mids=explode(' ',$midlist[$c]);
			$yesval=0;
			for ($c2=0; $c2<sizeof($mids); $c2++){
				$d_score=mysql_query("SELECT value, outoftotal FROM 
								score WHERE mark_id='$mids[$c2]' AND student_id='$sid'");
				$score=mysql_fetch_array($d_score,MYSQL_ASSOC);
				if($score['value']){$yesval++;}
				}
			if($yesval<sizeof($mids)){$out='c';}else{$out='C';}
			$outrank=-100;
			}

		/*********************************************************/
	   	elseif($marktype=='compound'){
			/*Mark is a compound column*/
			$scoreclass='derived';
			$rep=checkReportEntryCat($umns[$c]['midlist'],$sid,$bid[0],$umns[$c]['component']);
			$out='<div class="'.$rep['class'].'"><a href="markbook.php?current=new_edit_reports.php&cancel=class_view.php&midlist='.$umns[$c]['midlist'].'&title='.$umns[$c]['topic'].'&pid='.$umns[$c]['component'].'&sid='.$sid.'&bid='.$bid[0].'">'.$rep['result'].'</a></div>';
			$outrank=$rep['value'];
			}

		/*********************************************************/
	   	elseif($marktype=='report'){
			/*Mark is a compound report column*/
			$reportentryn=checkReportEntry($umns[$c]['midlist'],$sid,$bid[0],$umns[$c]['component']);
			$out='<a href="markbook.php?current=new_edit_reports.php&cancel=class_view.php&midlist='.$umns[$c]['midlist'].'&title='.$umns[$c]['topic'].'&pid='.$umns[$c]['component'].'&sid='.$sid.'&bid='.$bid[0].'">R'.$reportentryn.'</a>';
			if($reportentryn>0){$scoreclass='report vspecial';}else{$scoreclass='report';};
			$outrank=-100;
			}

		/********finished with this mark*******************/
		/*		 If no $out set then the mark must be faulty......*/
		if(!isset($out)){$out=''; $outrank=-100;}
					/* .....in case!!!!! */

			/*three entries for each score in the student's row in the $viewtable*/
			$studentrow[$col_mid]=$out;
			/*displayed on the screen*/
			$studentrow["rank$col_mid"]=$outrank;
			/*the criteria used to sort by should the column be ranked*/
			if(!isset($score['outoftotal'])){$score['outoftotal']='';}
			if(!isset($score['value'])){$score['value']='';}
			if(!isset($score['grade'])){$score['grade']='';}
			if(!isset($score['comment'])){$score['comment']='';}
			if(!isset($score['extra'])){$score['extra']='';}
			$score['scoreclass']=$scoreclass;
			$studentrow["score$col_mid"]=$score;
			/*and score values form the database to be used by column_scripts*/
			}
	//		}
		array_push($viewtable, $studentrow);
		$row++;
		}

/**************************************************************/
/*		Rank order the table according to $umnrank choice*/	

	if($umnrank=='surname'){
		$sort_array[0]['name']='surname';
		$sort_array[0]['sort']='ASC';
		$sort_array[0]['case']=TRUE;
		$sort_array[1]['name']='forename';
		$sort_array[1]['sort']='ASC';
		$sort_array[1]['case']=TRUE;
		//		Should already be sorted by mysql so not needed
		//		and it doesn't support utf8!
		//		sortx($viewtable, $sort_array);
		}
	else{
		$sort_array[0]['name']="rank$umnrank";
		$sort_array[0]['sort']='DESC';
		$sort_array[0]['case']=TRUE;
		//removed because usort doesn't handle utf8
		//  	$sort_array[1]['name']='surname';
		//   	$sort_array[1]['sort']='ASC';
		// 		$sort_array[1]['case']=TRUE;
	    sortx($viewtable, $sort_array);
		}

/*	All finished.*/
$_SESSION['viewtable']=$viewtable;
$_SESSION['umns']=$umns;
?>
