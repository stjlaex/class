<?php
/**									med_view_action.php
 */

$action='med_view.php';

include('scripts/sub_action.php');

	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getMedicalPerm($yid);
	$neededperm='w';
	include('scripts/perm_action.php');
	if(isset($_POST['eids'])){$eids=$_POST['eids'];}else{$eids=array();}

if($sub=='medstatus'){
	if($Student['MedicalFlag']['value']=='Y'){
		mysql_query("UPDATE info SET medical='N' WHERE student_id='$sid'");
		$action='med_student_list.php';
		}
	elseif($Student['MedicalFlag']['value']=='N'){
		mysql_query("UPDATE info SET medical='Y' WHERE student_id='$sid'");
		}
	}
elseif($sub=='Submit'){
	$event='';
	$Notes=$Medical['Notes'];
	foreach($Notes['Note'] as $index => $Note){
		$todate=date('Y-m-d');
		$cattype=$Note['MedicalCategory']['value_db'];
		$inname='detail'.$index;
		$inval=clean_text($_POST[$inname]);
		if($Note['Detail']['value']!=$inval){
			$noteid=$Note['id_db'];
			/*Medical category:::old value:::new value*/
			$event.=$cattype.":::".$Note['Detail']['value'].":::".$inval.";";
			if($noteid==-1){
				mysql_query("INSERT INTO background
						(student_id,detail,type,entrydate) 
						VALUES ('$sid','$inval','$cattype','$todate');");
				}
			else{
				mysql_query("UPDATE background SET detail='$inval', entrydate='$todate'
									WHERE id='$noteid';");
				}
			}
		}
	/*Select the message for medical update and add a new event to student_event table*/
	$d_c=mysql_query("SELECT id FROM categorydef WHERE type='mes' AND name='medical' LIMIT 1;");
	$message_id=mysql_fetch_row($d_c);
	$catid=$message_id[0];
	mysql_query("INSERT INTO student_event SET student_id='$sid', event='$event',
				 type='medical',catid='$catid',file='$current',status='0',ip='".$_SERVER['REMOTE_ADDR']."',user_id='$tid';");

	/*Medical Assesments*/
	$todate=date('Y-m-d');
	foreach($eids as $eid){
		if(isset($_POST['extra'.$eid]) and $_POST['extra'.$eid]!='' and $_POST[$eid]=='1'){$extra=$_POST['extra'.$eid];}else{$extra='0';}
		$AssDef=fetchAssessmentDefinition($eid);
		$grading_grades=$AssDef['GradingScheme']['grades'];
		/* $$eid are the names of score values posted by the form
		 * if the value is empty then score will be unset and no entry made
		 */
		$scorevalue=clean_text($_POST[$eid]);
		if($scorevalue==''){$result='';}
		elseif($grading_grades!='' and $grading_grades!=' '){
			$result=scoreToGrade($scorevalue,$grading_grades);
			}
		else{
			$result=$scorevalue;
			}
		$label=$AssDef['PrintLabel']['value'];
		if($label=='extra'){$score=array('result'=>$result,'value'=>$scorevalue,'date'=>$todate,'comment'=>$extra);}
		else{$score=array('result'=>$result,'value'=>$scorevalue,'date'=>$todate);}
		update_assessment_score($eid,$sid,'G','',$score);
		}
	}
include('scripts/redirect.php');
?>
