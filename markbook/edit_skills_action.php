<?php 
/** 		  							new_edit_reports_action.php
 */

$action='edit_skills.php';

$viewtable=$_SESSION['viewtable'];
$inorders=$_SESSION['inorders'];	
/* inorders contains all info for storing values in the database, in
/* the order in which they were entered. */
$inasses=$inorders['inasses'];
$inbid=$inorders['subject'];
$inpid=$inorders['component'];
$rid=$inorders['rid'];
if(isset($inorders['catdefs'])){$catdefs=$inorders['catdefs'];}
if(isset($inorders['rating_name'])){$rating_name=$inorders['rating_name'];}
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
				foreach($inass['mids'] as $mid){
					update_mark_score($mid,$sid,$score);
					}
				}
			/*Finished assessment scores.*/
			}

		/*Now do individual subject teacher entries.*/
		while(isset($_POST["inmust$sid:$c2"])){
			$incategory='';
			$inmust=$_POST["inmust$sid:$c2"];
			$c2++;
	   		if($inorders['category']=='yes'){
				/* Read previous entry for the categories and check which have changed. */
				foreach($catdefs as $catdef){
					$catid=$catdef['id'];
					if(isset($_POST["sid$sid:$c2"]) and $_POST["sid$sid:$c2"]=='uncheck'){
						mysql_query("DELETE FROM report_skill_log WHERE report_id='$rid' AND student_id='$sid' AND skill_id='$catid';");
						}
					if(isset($_POST["sid$sid:$c2"]) and $_POST["sid$sid:$c2"]!='uncheck'){
						if($inorders['comment']=='yes'){
							$incom=$_POST["sid$sid:$c2"];
							}
						else{$incom='';}
						$in=$_POST["sid$sid:$c2"];
						if(isset($_POST["cat$sid:$catid"]) and $in==$_POST["cat$sid:$catid"]){
							$setdate=$_POST["dat$sid:$catid"];
							}
						else{
							$setdate=$todate;
							}
						mysql_query("INSERT INTO report_skill_log (report_id,student_id, skill_id, rating, comment, teacher_id) 
							VALUES ('$rid','$sid', '$catid', '$in', '$incom', '$tid');");
						}
					$c2++;
					}
				update_profile_score($rid,$sid,$inbid,$inpid,$incategory,$catdefs,$rating_name);
				}
			}
		}
	}

include('scripts/redirect.php');
?>
