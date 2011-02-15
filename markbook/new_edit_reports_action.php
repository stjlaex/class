<?php 
/** 		  							new_edit_reports_action.php
 */

$action='class_view.php';

$viewtable=$_SESSION['viewtable'];
$inorders=$_SESSION['inorders'];	
/* inorders contains all info for storing values in the database, in
/* the order in which they were entered. */
$inasses=$inorders['inasses'];
$inbid=$inorders['subject'];
$inpid=$inorders['component'];
$rid=$inorders['rid'];
if(isset($inorders['catdefs'])){$catdefs=$inorders['catdefs'];}
$todate=date('Y').'-'.date('n').'-'.date('j');

include('scripts/sub_action.php');

if($sub=='Submit'){

	for($c=0;$c<sizeof($viewtable);$c++){
		$sid=$viewtable[$c]['sid'];
		/* Go through the assessments, these must come first. */
		for($c2=0;$c2<sizeof($inasses);$c2++){
		    unset($inass);
			unset($res);
			$inass=$inasses[$c2];
			$eid=$inass['eid'];
			$asspid=$inass['pid'];/*this could be different to inpid!!!*/
			if($inass['table']=='score' and isset($_POST["sid$sid:$c2"])){
				$in=$_POST["sid$sid:$c2"];
				if($inass['field']=='grade' and $in!=''){
					$res=scoreToGrade($in,$inass['grading_grades']);
					}
				else{
					$res=$in;
					}
				$score=array('result'=>$res,
							 'value'=>$in,
							 'type'=>$inass['field'],
							 'date'=>$todate);
				/* Always update eidsid and if a mark column exists then update score too.*/
				update_assessment_score($eid,$sid,$inbid,$asspid,$score);
				update_mark_score($inass['mid'],$sid,$score);
				}
			/*Finished assessment scores.*/
			}

		/*Now do individual subject teacher entries.*/
		while(isset($_POST["inmust$sid:$c2"])){
			$incategory='';
			$inmust=$_POST["inmust$sid:$c2"];
			$c2++;
	   		if($inorders['category']=='yes'){
				reset($catdefs);
				while(list($catn,$catdef)=each($catdefs)){
					if(isset($_POST["sid$sid:$c2"])){
					    $in=$_POST["sid$sid:$c2"];
						$incategory=$incategory . $catdef['id'].':'.$in.':'.$todate.';';
						}
					$c2++;
					}
				}

			/*this assumes that the comment comes after all the category entries!!!*/
			if($inorders['comment']=='yes'){
				if(isset($_POST["sid$sid:$c2"])){
					$incom=$_POST["sid$sid:$c2"];
					$c2++;
					}
				else{$incom='';}
				}
			if($inmust=='yes' and $incategory!=''){
						mysql_query("INSERT INTO reportentry (category, teacher_id, report_id, student_id, subject_id, component_id) 
							VALUES ('$incategory', '$tid', '$rid', '$sid', '$inbid', '$inpid')");
						}
			elseif($inmust!='yes' and $incategory!=''){
   						$entryn=$inmust;
						mysql_query("UPDATE reportentry SET
						category='$incategory' WHERE report_id='$rid' AND
						student_id='$sid' AND subject_id='$inbid' AND
						component_id='$inpid' AND entryn='$entryn'");
						}
			elseif($inmust!='yes' and $incom=='' and $incategory==''){	   
   						$entryn=$inmust;
						mysql_query("DELETE FROM reportentry WHERE
						 report_id='$rid' AND
						student_id='$sid' AND subject_id='$inbid' AND
						component_id='$inpid' AND entryn='$entryn' LIMIT 1");
						}
			}
		}
	}

include('scripts/redirect.php');
?>
