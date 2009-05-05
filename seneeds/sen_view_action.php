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

$senhid=$SEN['SENhistory']['id_db'];

	/* Check user has permission to edit */
	$yid=$Student['YearGroup']['value'];
	$fid=$Student['RegistrationGroup']['value'];
	$bperm=getSubjectPerm($bid, $respons);
	$sperm=getSENPerm($yid, $respons);
	$fperm=getFormPerm($fid, $respons);
	$perm=$fperm;
	if($sperm['w']==1 or $fperm['w']==1 or $bperm['r']==1){
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
	$SENhistory=$SEN['SENhistory'];
	$inval=$_POST['date1'];
	$table=$SENhistory['NextReviewDate']['table_db'];
	$field=$SENhistory['NextReviewDate']['field_db'];
	if($SENhistory['NextReviewDate']['value']!=$inval){
		mysql_query("UPDATE $table SET $field='$inval' WHERE id='$senhid'");
		}
	/* Allow up to 2 records with blanks for new entries*/
	while(sizeof($SEN['SENtypes']['SENtype'])<2){$SEN['SENtypes']['SENtype'][]=fetchSENtype();}
	while(list($index,$SENtypes)=each($SEN['SENtypes']['SENtype'])){
		$entryn=$index+1;
		$table=$SENtypes['SENtypeRank']['table_db'];
		$field=$SENtypes['SENtypeRank']['field_db'];
		$inname=$field. $entryn;
		$inval=clean_text($_POST[$inname]);
		if($SENtypes['SENtypeRank']['value']!=$inval){
				if(mysql_query("INSERT INTO $table SET
						student_id='$sid', entryn='$entryn',$field='$inval';")){}
				else{mysql_query("UPDATE $table SET $field='$inval'
								WHERE student_id='$sid' 
								AND entryn='$entryn';");}
				}
		$table=$SENtypes['SENtype']['table_db'];
		$field=$SENtypes['SENtype']['field_db'];
		$inname=$field. $entryn;
		$inval=clean_text($_POST[$inname]);
		if($SENtypes['SENtype']['value']!=$inval){
				if(mysql_query("INSERT INTO sentype SET
						student_id='$sid', entryn='$entryn',$field='$inval';")){}
				else{mysql_query("UPDATE $table SET $field='$inval'
								WHERE student_id='$sid' AND entryn='$entryn';");}
				}
		}

	while(list($key,$Subject)=each($SEN['NCmodifications'])){
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
		}

	}

include('scripts/redirect.php');
?>
