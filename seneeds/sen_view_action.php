<?php
/**									sen_view_action.php
 */

$action='sen_view.php';

include('scripts/sub_action.php');

if($sub=='SENStatus'){
	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getSENPerm($yid, $respons);
	$neededperm='w';
	include('scripts/perm_action.php');

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
	}

elseif($sub=='Submit'){
	/********Check user has permission to edit*************/
	$yid=$Student['NCyearActual']['id_db'];
	$perm=getSENPerm($yid,$respons);
	$neededperm='w';
	include('scripts/perm_action.php');

	$SEN=$Student['SEN'];
	$senhid=$SEN['SENhistory']['id_db'];
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

	while(list($key,$Subject)=each($SEN['NationalCurriculum'])){
		if(is_array($Subject)){
			$table=$Subject['Strengths']['table_db'];
			$field=$Subject['Strengths']['field_db'];
			$inname=$field.$key;
			$inval=clean_text($_POST[$inname]);
			if($Subject['Strengths']['value']!=$inval){
				mysql_query("UPDATE $table SET $field='$inval'
									WHERE senhistory_id='$senhid'");
				}

			$table=$Subject['Strategies']['table_db'];
			$field=$Subject['Strategies']['field_db'];
			$inname=$field . $key;
			$inval=clean_text($_POST[$inname]);
			if($Subject['Strategies']['value']!=$inval){
				print $Subject['Strategies']['label']." : ".$inval;
				mysql_query("UPDATE $table SET $field='$inval'
									WHERE senhistory_id='$senhid'");
				}

			$table=$Subject['Weaknesses']['table_db'];
			$field=$Subject['Weaknesses']['field_db'];
			$inname=$field.$key;
			$inval=clean_text($_POST[$inname]);
			if($Subject['Weaknesses']['value']!=$inval){
				mysql_query("UPDATE $table SET $field='$inval'
									WHERE senhistory_id='$senhid'");
				}	
			}
		}
	}
	include('scripts/redirect.php');
?>