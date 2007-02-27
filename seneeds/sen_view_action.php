<?php
/**									sen_view_action.php
 */

$action='sen_view.php';

include('scripts/sub_action.php');
if(isset($_POST['ncmod'])){$ncmodkey=$_POST['ncmod'];}else{$ncmodkey='';}
$SEN=$Student['SEN'];
$senhid=$SEN['SENhistory']['id_db'];

	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getSENPerm($yid, $respons);
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
		mysql_query("INSERT INTO sentypes SET student_id='$sid'");
		/*creates a blank entry for general comments applicable to all subjects*/
		mysql_query("INSERT INTO sencurriculum SET
					senhistory_id='$senhid', subject_id='General'");
		}
	$action='sen_student_list.php';
	}
elseif($ncmodkey=='-1'){
	if(isset($_POST['bid'])){
		$bid=$_POST['bid'];
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
	while(list($key,$SENtypes)=each($SEN['SENtypes'])){
			$table=$SENtypes['SENtypeRank']['table_db'];
			$field=$SENtypes['SENtypeRank']['field_db'];
			$inname=$field.$key;
			$inval=clean_text($_POST[$inname]);
			if($SENtypes['SENtypeRank']['value']!=$inval){
				mysql_query("UPDATE $table SET $field='$inval'
									WHERE student_id='$sid'");
				}	
			$table=$SENtypes['SENtype']['table_db'];
			$field=$SENtypes['SENtype']['field_db'];
			$inname=$field.$key;
			$inval=clean_text($_POST[$inname]);
			if($SENtypes['SENtype']['value']!=$inval){
				mysql_query("UPDATE $table SET $field='$inval'
									WHERE student_id='$sid'");
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
		$inname=$field.$key;
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
		$inname=$field.$key;
		$inval=clean_text($_POST[$inname]);
		if($Subject['Weaknesses']['value']!=$inval){
			mysql_query("UPDATE $table SET $field='$inval'
									WHERE senhistory_id='$senhid' AND subject_id='$bid'");
			}
		}
	}
	include('scripts/redirect.php');
?>