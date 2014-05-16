<?php 
/** 		  							edit_single_report_action.php
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
		}
	}

include('scripts/redirect.php');
?>
