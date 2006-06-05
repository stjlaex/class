<?php 
/** 		  							edit_reports_action.php
 */

$action='class_view.php';

$viewtable=$_SESSION['viewtable'];
$umns=$_SESSION['umns'];
$inorders=$_SESSION['inorders'];	
/* inorders contains all info for storing values in the database,
 *	in the order in which they were entered
*/
$inasses=$inorders['inasses'];
$inbid=$inorders['subject'];
$inpid=$inorders['component'];
$rid=$inorders['rid'];
$catdefs=$inorders['catdefs'];
$todate=date('Y')."-".date('n')."-".date('j');

include('scripts/sub_action.php');

if($sub=='Submit'){
	for($c=0;$c<sizeof($inasses);$c++){
		unset($inorder);
		$inorder=$inasses[$c];
		if($inorder['table']=='score'){
			$mid=$inorder['id'];
			$d_assessment=mysql_query("SELECT id, subject_id, component_id FROM assessment JOIN
				eidmid ON assessment.id=eidmid.assessment_id WHERE eidmid.mark_id='$mid'");
			$ass=mysql_fetch_array($d_assessment,MYSQL_ASSOC);
			$inasses[$c]['eid']=$ass['id'];
			$inasses[$c]['bid']=$ass['subject_id'];
			$inasses[$c]['pid']=$ass['component_id'];
			if($inasses[$c]['bid']=='%'){
				/*any value other than % means this eid is for a single bid and
				is already explicity defined, probably as G for
				general. Note G for general cannot be found from midcid anyway!
				And the mid must only be linked to classes for a single bid -
				which is always the case if columns have been auto-generated*/
				$d_bid=mysql_query("SELECT DISTINCT subject_id FROM class JOIN midcid ON
					midcid.class_id=class.id WHERE midcid.mark_id='$mid'");
				$inasses[$c]['bid']=mysql_result($d_bid,0);
				}
			if($inasses[$c]['pid']==''){
				$d_pid=mysql_query("SELECT component_id FROM mark WHERE id='$mid'");
				$inasses[$c]['pid']=mysql_result($d_pid,0);
				}
		   	}
		}

	for($c=0;$c<sizeof($viewtable);$c++){
		$sid=$viewtable[$c]['sid'];
		for($c2=0;$c2<sizeof($inasses);$c2++){
		    unset($inorder);
			unset($res);
			$inorder=$inasses[$c2];
			if(isset($_POST{"sid$sid:$c2"})){
				$in=$_POST{"sid$sid:$c2"};
				if($inorder['table']=='score' and $inorder['field']=='grade' and $in!=''){
					$mid=$inorder['id'];
					if(mysql_query("INSERT INTO score (grade,
						mark_id, student_id) VALUES
						('$in',  '$mid', '$sid')")){}
					elseif(mysql_query("UPDATE score SET
							grade='$in' WHERE mark_id='$mid' AND student_id='$sid'")){}
					else{$error[]=mysql_error();}
					$res=scoreToGrade($in,$inorder['grading_grades']);
					}
				elseif($inorder['table']=='score' and $inorder['field']=='value' and $in!=''){
					$mid=$inorder['id'];
					if(mysql_query("INSERT INTO score (value,
					 mark_id, student_id) VALUES
					('$in',  '$mid', '$sid')")){}
					elseif(mysql_query("UPDATE score SET
					value='$in' WHERE mark_id='$mid' AND student_id='$sid'")){}
					else {$error[]=mysql_error();}
					$res=$in;
					}
   				elseif($inorder['table']=='score' and $in==''){
					$mid=$inorder['id'];
					if(mysql_query("DELETE FROM score WHERE
						mark_id='$mid' AND student_id='$sid' LIMIT 1")){}
					else{$error[]=mysql_error();}
					if(isset($inorder['eid'])){
						$eid=$inorder['eid'];
						$bid=$inorder['bid'];
						$pid=$inorder['pid'];
						$d_eidsid=mysql_query("SELECT id FROM eidsid
							WHERE subject_id='$bid' AND component_id='$pid' 
							AND assessment_id='$eid' AND student_id='$sid'");
						if(mysql_num_rows($d_eidsid)!=0){
							$eidsidid=mysql_result($d_eidsid,0);
							mysql_query("DELETE FROM eidsid WHERE id='$eidsidid'");
							}
						}
					}

				if(isset($inorder['eid']) and isset($res)){
					$eid=$inorder['eid'];
					$bid=$inorder['bid'];
					$pid=$inorder['pid'];
					$d_eidsid=mysql_query("SELECT id FROM eidsid
						WHERE subject_id='$bid' AND component_id='$pid' 
						AND assessment_id='$eid' AND student_id='$sid'");
					if(mysql_num_rows($d_eidsid)==0){
						mysql_query("INSERT INTO eidsid (assessment_id,
							student_id, subject_id, component_id, result, value, date) 
							VALUES ('$eid','$sid','$bid','$pid','$res','$in','$todate');");
						}
					else{
						$eidsidid=mysql_result($d_eidsid,0);
						mysql_query("UPDATE eidsid SET result='$res', 
							value='$in', date='$todate' WHERE id='$eidsidid'");
						}
					}
				}
			}
		/*finished assessment scores*/

		/*now do individual subject teacher entries*/
		while(isset($_POST{"inmust$sid:$c2"})){
			$incategory='';
			$inmust=$_POST{"inmust$sid:$c2"};
			$c2++;
	   		if($inorders['category']=='yes'){
				reset($catdefs);
				while(list($catn,$catdef)=each($catdefs)){
					if(isset($_POST{"sid$sid:$c2"})){
					    $in=$_POST{"sid$sid:$c2"};
						$incategory=$incategory . $catdef['id'].':'.$in.';';
						}
					$c2++;
					}
				}
			/*this assumes that the comment comes after all the category entries!!!*/
			if($inorders['comment']=='yes'){
				if(isset($_POST{"sid$sid:$c2"})){
					$incom=$_POST{"sid$sid:$c2"};
					$c2++;
					}
				else{$incom='';}
				}
			if($inmust=='yes' and $incategory!=''){
						if(mysql_query("INSERT INTO reportentry (
						category, teacher_id, report_id, student_id, 
						subject_id, component_id) VALUES
						('$incategory', '$tid', '$rid', '$sid',
						'$inbid', '$inpid')")){}
						else {$error[]=mysql_error();}
						}
			elseif($inmust!='yes' and $incom!='' and $incategory!=''){
   						$entryn=$inmust;
						if(mysql_query("UPDATE reportentry SET
						category='$incategory' WHERE report_id='$rid' AND
						student_id='$sid' AND subject_id='$inbid' AND
						component_id='$inpid' AND entryn='$entryn'")){
					    }
						else {$error[]=mysql_error();}
						}
			elseif($inmust!='yes' and $incom=='' and $incategory==''){	   
   						$entryn=$inmust;
						if(mysql_query("DELETE FROM reportentry WHERE
						 report_id='$rid' AND
						student_id='$sid' AND subject_id='$inbid' AND
						component_id='$inpid' AND entryn='$entryn' LIMIT 1")){}
						else {$error[]=mysql_error();}
						}
			}
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
