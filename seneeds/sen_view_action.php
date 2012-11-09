<?php
/**									sen_view_action.php
 */

/** 
 * Have to be careful to check current as this can be called from the
 * InfoBook too.
 */
if($current=='sen_view_action.php'){$action='sen_view.php';}

include('scripts/sub_action.php');
if(isset($_POST['ncmod'])){$ncmodkey=$_POST['ncmod'];}else{$ncmodkey='';}
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='G';}

$senhid=$SEN['id_db'];

	/* Check user has permission to edit */
	$yid=$Student['YearGroup']['value'];
	$fid=$Student['RegistrationGroup']['value'];
	$bperm=getSubjectPerm($bid, $respons);
	$sperm=getSENPerm($yid, $respons);
	$fperm=getFormPerm($fid, $respons);
	$perm=$fperm;
	if($sperm['w']==1 or $fperm['w']==1 or $bperm['w']==1){
		$perm['w']=1;
		}
	$neededperm='w';
	include('scripts/perm_action.php');

if($sub=='senstatus'){
	if($Student['SENFlag']['value']=='Y'){
		mysql_query("UPDATE info SET sen='N' WHERE student_id='$sid'");
		}
	elseif($Student['SENFlag']['value']=='N'){
		mysql_query("UPDATE info SET sen='Y' WHERE student_id='$sid'");
		/*set up first blank record for the profile*/
		$todate=date('Y')."-".date('n')."-".date('j');
		mysql_query("INSERT INTO senhistory SET startdate='$todate', student_id='$sid'");
		$senhid=mysql_insert_id();
		/*creates a blank entry for general comments applicable to all subjects*/
		mysql_query("INSERT INTO sencurriculum SET
					senhistory_id='$senhid', subject_id='General'");
		}
	if($current=='sen_view_action.php'){$action='sen_student_list.php';}
	}
elseif($ncmodkey=='-1'){
	if($bid!='G' and $bid!=''){
		mysql_query("INSERT INTO sencurriculum SET
			senhistory_id='$senhid', subject_id='$bid'");
		}

	}
elseif($sub=='Submit'){
	$inval=$_POST['date0'];
	$table=$SEN['StartDate']['table_db'];
	$field=$SEN['StartDate']['field_db'];
	if($SEN['NextReviewDate']['value']!=$inval){
		mysql_query("UPDATE $table SET $field='$inval' WHERE id='$senhid'");
		}
	$inval=$_POST['date1'];
	$table=$SEN['NextReviewDate']['table_db'];
	$field=$SEN['NextReviewDate']['field_db'];
	if($SEN['NextReviewDate']['value']!=$inval){
		mysql_query("UPDATE $table SET $field='$inval' WHERE id='$senhid'");
		}
	$inval=$_POST['date2'];
	$table=$SEN['AssessmentDate']['table_db'];
	$field=$SEN['AssessmentDate']['field_db'];
	if($SEN['AssessmentDate']['value']!=$inval){
		mysql_query("UPDATE $table SET $field='$inval' WHERE id='$senhid'");
		}


	$senasstypes=array('I'=>'SENinternaltypes','E'=>'SENtypes');
	foreach($senasstypes as $asscode => $assname){
		mysql_query("DELETE FROM sentype WHERE student_id='$sid' AND senassessment='$asscode';");
		/* Allow up to 3 records for each assessment type  */
		for($entryn=0;$entryn<3;$entryn++){
			$inname=$asscode. 'senranking' . $entryn;
			$senranking=clean_text($_POST[$inname]);
			$inname=$asscode. 'sentype' . $entryn;
			$sentype=clean_text($_POST[$inname]);
			if($sentype!='' or $senranking!=''){
				mysql_query("INSERT INTO sentype SET student_id='$sid', entryn='$entryn', 
								senranking='$senranking', sentype='$sentype', senassessment='$asscode';");
				}
			}
		}



	foreach($SEN['Curriculum'] as $key => $Subject){
		$bid=$Subject['Subject']['value_db'];
		$table='sencurriculum';
		$inname='extrasupport'.$key;
		$inval=clean_text($_POST[$inname]);
		if($Subject['ExtraSupport']['value_db']!=$inval){
			mysql_query("UPDATE $table SET categorydef_id='$inval'
									WHERE senhistory_id='$senhid' AND subject_id='$bid'");
			}

		$field=$Subject['Strengths']['field_db'];
		$inname=$field. $key;
		$inval=clean_text($_POST[$inname]);
		if($Subject['Strengths']['value']!=$inval){
			mysql_query("UPDATE $table SET $field='$inval'
					   			WHERE senhistory_id='$senhid' AND subject_id='$bid'");
				}

		$field=$Subject['Weaknesses']['field_db'];
		$inname=$field . $key;
		$inval=clean_text($_POST[$inname]);
		if($Subject['Strategies']['value']!=$inval){
			mysql_query("UPDATE $table SET $field='$inval'
									WHERE senhistory_id='$senhid' AND subject_id='$bid'");
			}

		$field=$Subject['Strategies']['field_db'];
		$inname=$field. $key;
		$inval=clean_text($_POST[$inname]);
		if($Subject['Weaknesses']['value']!=$inval){
			mysql_query("UPDATE $table SET $field='$inval'
									WHERE senhistory_id='$senhid' AND subject_id='$bid'");
			}

		$field=$Subject['Targets']['field_db'];
		$inname=$field. $key;
		$inval=clean_text($_POST[$inname]);
		if($Subject['Targets']['value']!=$inval){
			mysql_query("UPDATE $table SET $field='$inval'
									WHERE senhistory_id='$senhid' AND subject_id='$bid'");
			}

		}

	}

include('scripts/redirect.php');
?>
