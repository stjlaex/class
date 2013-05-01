<?php
/**							class_view_table.php
 *
 * Generates the array $viewtable and stores as a session variable.
 *
 *
 */

$orderby=get_studentlist_order();
$d_students=mysql_query("SELECT * FROM students ORDER BY $orderby"); 
$rowno=0;
$viewtable=array();

while($student=mysql_fetch_array($d_students, MYSQL_ASSOC)){
	$sid=$student['student_id'];
	$SEN_field=fetchStudent_singlefield($sid,'SENFlag');
	$Medical_field=fetchStudent_singlefield($sid,'MedicalFlag');
	$Boarder_field=fetchStudent_singlefield($sid,'Boarder');
	$Attendance=fetchcurrentAttendance($sid);

	$studentrow=array('row'=>$rowno,
					  'sen'=>$SEN_field['SENFlag']['value'],
					  'medical'=>$Medical_field['MedicalFlag']['value'],
					  'boarder'=>$Boarder_field['Boarder']['value'],
					  'attstatus'=>$Attendance['Status']['value'], 
					  'attcode'=>$Attendance['Code']['value'], 
					  'attcomment'=>$Attendance['Comment']['value'], 
					  'atttime'=>$Attendance['Logtime']['value'], 
					  'sid'=>$sid,
					  'surname'=>$student['surname'],
					  'forename'=>$student['forename'],
					  'preferredforename'=>$student['preferredforename'],
					  'form_id'=>$student['form_id'],
					  'class_id'=>$student['class_id']
					  );

	/* Compile in reverse order to allow referring back to older columns. */
	for($c=$c_marks-1;$c>-1;$c--) {
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
			$d_score=mysql_query("SELECT * FROM score WHERE mark_id='$col_mid' AND student_id='$sid';");
			$score=mysql_fetch_array($d_score,MYSQL_ASSOC);			
			/*score can be one of four types: grade, value, percentage, comment*/
			if($scoretype=='grade'){
				$out=scoreToGrade($score['grade'],$scoregrades[$scoregrading[$c]]);
				$outrank=$score['grade'];

				/* For traffic light grades only... */
				if(strtolower($out)=='green'){$scoreclass.=' golite';$out='';}
				elseif(strtolower($out)=='red'){$scoreclass.=' hilite';$out='';}
				elseif(strtolower($out)=='yellow'){$scoreclass.=' pauselite';$out='';}

				}
			elseif($scoretype=='value'){
				$out=$score['value'];
				$outrank=$score['value'];    
				}
			elseif($scoretype=='percentage'){
				list($out,$percent,$outrank)=scoreToPercent($score['value'],$score['outoftotal']);
				}
			elseif($scoretype=='comment'){
				$out=substr($score['comment'],0,12).'<br />'.substr($score['comment'],12,12);
				$outrank=-100;
				}
			}
		/*********************************************************/
		elseif($marktype=='average'){
			/*Mark is average of several score values*/
			$scoreclass.=' derived';
			$mids=explode(' ',$midlist[$c]);
			/* Test if this average uses weightings... */
			$pos=strpos($midlist[$c],':::');
			if(!$pos===false){
				$tempmids=$mids;
				$mids=array();
				$weights=array();
				$weightsum=0;
				foreach($tempmids as $mid){
					list($mids[],$weight)=explode(':::',$mid);
					$weights[]=$weight;
					$weightsum+=$weight;
					}
				unset($tempmids);
				}
			else{$weightsum=sizeof($mids);}


			/* Average is to be a grade */
			if($umns[$c]['scoretype']=='grade'){

				if($scoregrades[$umns[$c]['lena']]){$grading_grades=$scoregrades[$umns[$c]['lena']];}
				else{$grading_grades=$scoregrades[$scoregrading[$c]];}

				$gradesum=0;
				$gradecount=0;
				foreach($mids as $mid){
					if(isset($studentrow["score$mid"])){$iscore=$studentrow["score$mid"];}
					else{$iscore=array();}
					/* Careful to exclude NULL values but not 0s */
					if(isset($iscore['grade']) and $iscore['grade']!=''){
						$gradesum=$gradesum+$iscore['grade'];
						$gradecount++;
						}
					}
				if($gradecount>0){
					$score_grade=round($gradesum/$gradecount);
					$grade=scoreToGrade($score_grade,$grading_grades);
					$out=$grade;
					$outrank=$score_grade;
					$score['grade']=$score_grade;
					}
				else{$outrank=-100;$out='';unset($score_grade);}
				}
			/* Average is to be a percentage */
			elseif($umns[$c]['scoretype']=='percentage'){
				$scoresum=0;
				$scorecount=0;
				$iweightsum=0;
				$ivals=array();
				foreach($mids as $avc => $mid){
					$iscore=$studentrow["score$mid"];
					if(isset($iscore['value']) and $iscore['value']!=='' and $iscore['outoftotal']>0){
						//$ival=list($display,$percent,$cent)=scoreToPercent($iscore['value'],$iscore['outoftotal']);
						$ivals[$avc]=$iscore['value']/$iscore['outoftotal']*100;
						$scorecount++;
						if(!isset($weights[$avc])){
							$iweightsum++;
							}
						else{
							$iweightsum+=$weights[$avc];
							}
						}
					//elseif(isset($iscore['grade']) and $iscore['grade']!==''){$ival=$iscore['grade'];}
					//elseif(isset($iscore['value']) and $iscore['value']!==''){$ival=$iscore['value'];}
					}

				if($scorecount>0){
					foreach($mids as $avc => $mid){
						if(isset($ivals[$avc])){
							if(!isset($weights[$avc])){
								$scoresum+=$ivals[$avc] / $iweightsum;
								}
							else{
								$scoresum+=$ivals[$avc] * $weights[$avc] / $iweightsum;
								}
							}
						}
					$score['value']=$scoresum;
					list($display,$out,$outrank)=scoreToPercent($scoresum,100);
					}
				else{$out='';$outrank=-100;}
				}
			/* Average is to be a raw score */
			else{
				$scoresum=0;
				$scorecount=0;
				foreach($mids as $avc => $mid){
					if(isset($studentrow["score$mid"])){
						$iscore=$studentrow["score$mid"];
						if($iscore['value']!='' and $iscore['outoftotal']>0){
							$ival=$iscore['value']/$iscore['outoftotal']*100;
							}
						elseif($iscore['grade']!='' and $iscore['value']===''){$ival=$iscore['grade'];}
						elseif($iscore['value']!=''){$ival=$iscore['value'];}
						if(isset($ival)){
							if(!isset($weights[$avc])){
								$scoresum+=$ival / $weightsum;
								}
							else{
								$scoresum+=$ival * $weights[$avc] / $weightsum;
								}
							$scorecount++;
							unset($ival);
							}
						}
					}
				if($scorecount>0){
					$out=round($scoresum,1);
					$outrank=$scoresum;
					$score['value']=$scoresum;
					}
				else{
					//$out=$c.''.$midlist[$c];
					$outrank=-100;
					}
				}
			unset($weights);
			unset($weightsum);
			}
		
		/*********************************************************/
	   	elseif($marktype=='sum'){
			/*Mark is the sum of several score values*/
			$scoreclass.=' derived';
			$mids=explode(' ',$midlist[$c]);
			$score_value=0;
			$score_total=0;
			foreach($mids as $mid){
				$iscore=$studentrow["score$mid"];
				if($iscore['value']){$score_value+=$iscore['value']; $yesval=1;}
				if($iscore['outoftotal']){
					$score_total+=$iscore['outoftotal']; 
					$yestotal=1;
					}
				}
			if(isset($yestotal)){
				/*mark's were percentage scores*/
				list($dislpay,$out,$outrank)=scoreToPercent($score_value,$score_total);
				$score['value']=$out;
				}
			else{
				/*otherwise mark's were raw scores*/
				if(isset($yesval)){$out=$score_value; $outrank=$score_value; $score['value']=$score_value;}
				else{$out='';$outrank=-100;}
				}
			unset($yesval);
			unset($yestotal);
			}
		
		/*********************************************************/
	   	elseif($marktype=='dif'){
			/* Mark is the difference of two scores  */
			$scoreclass.=' derived';
			$mids=(array)explode(' ',$midlist[$c]);
			$score_value=0;
			$score_total=0;
			/* Is the score a grade or a raw value. */
			if($scoregrading[$c]!=''){
				$scorekey='grade';
				}
			else{
				$scorekey='value';
				}
			foreach($mids as $mid){
				unset($previousscore);
				if(isset($studentrow["score$mid"])){			
					$iscore=$studentrow["score$mid"];
					if(isset($lastscore) and $iscore[$scorekey]!==''){$previousscore=$lastscore;}
					if(!isset($firstscore) and $iscore[$scorekey]!=''){$firstscore=$iscore;}
					if(isset($iscore[$scorekey]) and $iscore[$scorekey]!==''){$lastscore=$iscore;}
					if(isset($previousscore) and isset($lastscore)){
						$lastdif=$lastscore[$scorekey]-$previousscore[$scorekey];
						if($lastdif>0){$studentrow["score$mid"]['scoreclass'].=' golite';}
						elseif($lastdif<0){$studentrow["score$mid"]['scoreclass'].=' hilite';}
						else{$studentrow["score$mid"]['scoreclass'].=' pauselite';}
						}
					}
				}
			if(isset($firstscore) and isset($lastscore)){
				$score_value=$lastscore[$scorekey]-$firstscore[$scorekey];
				$yesval=1;
				}
			if(isset($yesval)){
				$out=$score_value;
				$outrank=$score_value;
				$score['value']=$score_value;
				}
			else{
				$out='';
				$outrank=-100;
				}
			unset($yesval);
			unset($yestotal);
			unset($firstscore);
			unset($lastscore);
			unset($previousscore);
			unset($scorekey);
			}

		/*********************************************************/
	   	elseif($marktype=='tally'){
			/* Mark tallies (counts) the number of grades obtained */
			$scoreclass.=' derived';
			$mids=explode(' ',$midlist[$c]);
			$score_value=0;
			foreach($mids as $mid){
				$iscore=$studentrow["score$mid"];
				if($iscore['value']){$score_value++; $yesval=1;}
				}
			if(isset($yesval)){$out=$score_value; $outrank=$score_value; $score['value']=$score_value;}
			else{$out='';$outrank=-100;}
			unset($yesval);
			}

		/*********************************************************/
	   	elseif($marktype=='applevel'){
			/* Mark tallies (counts) the number of grades obtained */
			$scoreclass.=' derived';
			$mids=explode(' ',$midlist[$c]);
			$score_value=0;
			$score_display='';
			foreach($mids as $no => $mid){
				$iscore=$studentrow["score$mid"];
				if($iscore['value']){
					$lev=calculateProfileLevel($iscore['rid'],$sid,$profilebid,$iscore['pid']);
					$score_value+=$lev['value1'];
					$yesval=1;
					if($lev['value1']>80 and $score_display==''){$score_display=$lev['result'].'a';}
					elseif($lev['value1']>60 and $score_display==''){$score_display=$lev['result'].'b';}
					elseif($lev['value1']>30 and $score_display==''){$score_display=$lev['result'].'c';}
					}
				//$score_display.=$lev['value1'].' '.$lev['value2'].' '.$lev['value3'].' '.$lev['value4'].' '.$lev['value5'].' '.$lev['outoftotal'].'<br />';
				}

			if(isset($yesval)){
				$out=$score_display.' '; 
				$outrank=$score_value; 
				$score['value']=$score_value;
				}
			else{$out='';$outrank=-100;}
			unset($yesval);
			}

		/*********************************************************/
	   	elseif($marktype=='level'){
			$scoreclass.=' derived';
			$mid=$midlist[$c];
			$iscore=$studentrow["score$mid"];
			/*then mark is the levelled grade of a score*/
			list($out,$outrank)=scoreToLevel($iscore['value'],$iscore['outoftotal'],$levels[$c]);
			}

		/*********************************************************/
	   	elseif($marktype=='oldcompound'){
			/*Mark is a compound column*/
			$mids=explode(' ',$midlist[$c]);
			$yesval=0;
			foreach($mids as $mid){
				$iscore=$studentrow["score$mid"];
				if($iscore['value']){$yesval++;}
				}
			if($yesval<sizeof($mids)){$out='c';}else{$out='C';}
			$outrank=-100;
			}

		/*********************************************************/
	   	elseif($marktype=='compound'){
			/*Mark is a compound column*/
			$scoreclass='derived';

			/* TODO: figure out a simpler way of defining this for profile_bid.... */ 
			if(empty($umns[$c]['profile_bid']) or $umns[$c]['profile_bid']=='%'){$profilebid=$bid[0];}
			else{$profilebid=$umns[$c]['profile_bid'];}

trigger_error($umns[$c]['profile_bid'].' ::: '.$profilebid.' ::: '.$bid[0],E_USER_WARNING);

			/* Have to explicity pass the bid and pid for the profile here NOT for the class. */
			$rep=checkReportEntryCat($umns[$c]['midlist'],$sid,$profilebid,$umns[$c]['component']);
			/* Option to decide what is displayed in the table cell: either blank or the result value. */
			if($umns[$c]['profile_celldisplay']==='' or $rep['value']==='' or $rep['value']<=0){
				$outspace='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				}
			else{
				$outspace=$rep['result'];
				}
			/* Only use the result value if it is a percentage (greater than 0) */
			if($rep['result']>0){
				$outof=round($rep['value']*100/$rep['result']);
				$outtitle=display_date($rep['date']).': '.$rep['value'].' /' .$outof.' ('.$rep['result'].'%)';
				}
			else{
				$outof=0;
				$outtitle='';
				$rep['result']=0;
				}

			$out='<div class="'.$rep['class'].'" title="'.$outtitle.'"><a href="markbook.php?current=new_edit_reports.php&cancel=class_view.php&midlist='.$umns[$c]['midlist'].'&pid='.$umns[$c]['component'].'&sid='.$sid.'&bid='.$profilebid.'&nextrow='.$rowno.'">'.$outspace.'</a></div>';

			$score['grade']=$rep['result'];
			$score['value']=$rep['result'];
			$score['outoftotal']=100;
			$score['rid']=$umns[$c]['midlist'];//need this for the overall applevel column
			$score['pid']=$umns[$c]['component'];//need this for the overall applevel column
			$outrank=$rep['value'];
			}

		/*********************************************************/
	   	elseif($marktype=='report'){
			/*Mark is a compound report column*/
			$reportentryn=checkReportEntry($umns[$c]['midlist'],$sid,$bid[0],$umns[$c]['component']);
			$out='<a href="markbook.php?current=new_edit_reports.php&cancel=class_view.php&midlist='.$umns[$c]['midlist'].'&pid='.$umns[$c]['component'].'&sid='.$sid.'&bid='.$bid[0].'&nextrow='.$rowno.'">R'.$reportentryn.'</a>';
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

		if($score['grade']!=''){
			$totals[$col_mid]['grade']+=$score['grade'];
			$totals[$col_mid]['value']+=$score['value'];
			$totals[$col_mid]['outoftotal']+=$score['outoftotal'];
			$totals[$col_mid]['no']++;
			}

		$score['scoreclass']=$scoreclass;
		$studentrow["score$col_mid"]=$score;
		/*and score values from the database to be used by column_scripts*/
		}

	array_push($viewtable, $studentrow);
	$rowno++;
	}


/**************************************************************
 *		Rank order the table according to $umnrank choice     
 */	

if($umnrank=='surname'){
	/*
	  Already sorted by mysql so not needed
	  and sortx doesn't support utf8!
	*/
	}
else{
	$sort_array[0]['name']="rank$umnrank";
	$sort_array[0]['sort']='DESC';
	$sort_array[0]['case']=TRUE;
	sortx($viewtable, $sort_array);
	}

/*	All finished.*/
$_SESSION['viewtable']=$viewtable;
$_SESSION['umns']=$umns;
?>
