<?php 
/** 									edit_single_report_action.php
 */

require_once('../../scripts/http_head_options.php');

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
$columnid=$_POST['colid'];


	for($c=0;$c<sizeof($viewtable);$c++){
		$sid=$viewtable[$c]['sid'];
		/* Go through the assessments, these must come first. */
		for($c2=0;$c2<sizeof($inasses);$c2++){
			unset($inass);
			unset($res);
			$inass=$inasses[$c2];
			$eid=$inass['eid'];
			$asspid=$inass['pid'];/*this could be different to inpid!!!*/
			if($inass['table']=='score' and isset($_POST["sidc$sid:$c2"])){
				$in=$_POST["sidc$sid:$c2"];
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
			}

		/*Now do individual subject teacher entries.*/
		$c2=0;
		while(isset($_POST["sid$sid:$c2"])){
			$incategory='';
			$inmust=$_POST["inmust$sid:$c2"];
	   		if($inorders['category']=='yes'){
				/* Read previous entry for the categories and check which have changed. */
				foreach($catdefs as $catdef){
					$test=$c2;
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
						$d_sk=mysql_query("SELECT rating FROM report_skill_log WHERE report_id='$rid' AND student_id='$sid' 
										AND skill_id='$catid' AND teacher_id='$tid' 
										ORDER BY timestamp DESC LIMIT 1;");
						if(mysql_num_rows($d_sk)==0 or mysql_result($d_sk,0)!=$in){
							mysql_query("INSERT INTO report_skill_log (report_id,student_id, skill_id, rating, comment, teacher_id) 
								VALUES ('$rid','$sid', '$catid', '$in', '$incom', '$tid');");
							}
						}
					$c2++;
					}
				update_profile_score($rid,$sid,$inbid,$inpid,$incategory,$catdefs,$rating_name);
				}
				$c2++;
			}


		if(isset($_POST['sid']) and $_POST['sid']==$sid){
			$c2++;
			$reportentryn=checkReportEntry($rid,$sid,$inbid,$inpid);
			$out='<div onclick="parent.openModalWindow(\'markbook.php?current=edit_single_report.php&cancel=class_view.php&midlist='.$rid.'&pid='.$inpid.'&sid='.$sid.'&bid='.$inbid.'&colid='.$columnid.'\',\'\',\'\');" style="cursor:pointer;">R';
			if($reportentryn>0){$out.=$reportentryn;$scoreclass='report vspecial';}else{$scoreclass='report';}
			$out.='</div>';
			$Student['sid']=$sid;
			$Student['rid']=$rid;
			$Student['colid']=$columnid;
			$Student['newval']=$out;
			$Student['cellclass']=$scoreclass;
			$Students['Student'][]=$Student;
			}
		}

$returnXML=$Students;
$rootName='Students';
$xmlechoer=true;
require_once('../../scripts/http_end_options.php');
exit;
?>
