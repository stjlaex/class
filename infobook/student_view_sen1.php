<?php
/**									student_view_sen1.php
 */

$action='student_view.php';
include('scripts/sub_action.php');

if($sub=="SENStatus"){
	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);
	$neededperm='w';
	include('scripts/perm_action.php');

	if($Student['SENFlag']['value']=='Y'){
		mysql_query("UPDATE info SET sen='N' WHERE student_id='$sid'");
		$result[]="Updated student SEN status.";
		}
	elseif($Student['SENFlag']['value']=='N'){
		mysql_query("UPDATE info SET sen='Y' WHERE student_id='$sid'");
		$result[]="Updated student SEN status.";
/*		Set up first blank record for the profile*/
		$todate = date('Y')."-".date('n')."-".date('j');
		if(mysql_query("INSERT INTO senhistory SET startdate='$todate', student_id='$sid'"))
		{}else{$error[]=mysql_error();}
		$senhid=mysql_insert_id();
		if(mysql_query("INSERT INTO sentypes SET student_id='$sid'"))
		{}else{$error[]=mysql_error();}
/*		creates a blank entry for general comments applicable to all subjects*/
		if(mysql_query("INSERT INTO sencurriculum SET
		senhistory_id='$senhid', subject_id='General'"))
		{}else{$error[]=mysql_error();}
		}
	$_SESSION{'Student'}=fetchStudent($_SESSION{'sid'});
	}

elseif($sub=="Submit"){
/********Check user has permission to edit*************/
	$yid=$Student['NCyearActual']['id_db'];
	$perm=getYearPerm($yid,$respons);
	$needperm='w';
	include('scripts/perm_action.php');

	$SEN=$Student['SEN'];
	$action="student_view_sen.php";
	$senhid=$SEN['SENhistory']['id_db'];
	$SENhistory=$SEN['SENhistory'];
	$inval=$_POST['year']."-".$_POST['month']."-".$_POST['day'];
	$table=$SENhistory['NextReviewDate']['table_db'];
	$field=$SENhistory['NextReviewDate']['field_db'];
	if($SENhistory['NextReviewDate']['value']!=$inval){
		$result[]=$SENhistory['NextReviewDate']['label']." : ".$inval;
		if(mysql_query("UPDATE $table SET $field='$inval' WHERE id='$senhid'")){
			$SEN['SENhistory']['NextReviewDate']['value']=$inval;	
			}
			else{$error[]=mysql_error();}
		}
	while(list($key,$SENtypes)=each($SEN['SENtypes'])){
			$table=$SENtypes['SENtypeRank']['table_db'];
			$field=$SENtypes['SENtypeRank']['field_db'];
			$inname=$field.$key;
			$inval=clean_text($_POST{"$inname"});
			if($SENtypes['SENtypeRank']['value']!=$inval){
				$result[]=$SENtypes['SENtypeRank']['label']." : ".$inval;
					if(mysql_query("UPDATE $table SET $field='$inval'
									WHERE student_id='$sid'")){
					$SEN['SENtypes'][$key]['SENtypeRank']['value']=$inval;	
					}
					else{$error[]=mysql_error();}
				}	
			$table=$SENtypes['SENtype']['table_db'];
			$field=$SENtypes['SENtype']['field_db'];
			$inname=$field.$key;
			$inval=clean_text($_POST{"$inname"});
			if($SENtypes['SENtype']['value']!=$inval){
				$result[]=$SENtypes['SENtype'][0]['label']." : ".$inval;
					if(mysql_query("UPDATE $table SET $field='$inval'
									WHERE student_id='$senhid'")){
					$SEN['SENtypes'][$key]['SENtype']['value']=$inval;	
					}
					else{$error[]=mysql_error();}					
				}	
		}

	while(list($key,$Subject)=each($SEN['NationalCurriculum'])){
		if(is_array($Subject)){
			$table=$Subject['Strengths']['table_db'];
			$field=$Subject['Strengths']['field_db'];
			$inname=$field.$key;
			$inval=clean_text($_POST{"$inname"});
			if($Subject['Strengths']['value']!=$inval){
				$result[]=$Subject['Strengths']['label']." : ".$inval;
					if(mysql_query("UPDATE $table SET $field='$inval'
									WHERE senhistory_id='$senhid'")){
					$SEN['NationalCurriculum'][$key]['Strengths']['value']=$inval;	
					}
					else{$error[]=mysql_error();}					
				}	

			$table=$Subject['Strategies']['table_db'];
			$field=$Subject['Strategies']['field_db'];
			$inname=$field.$key;
			$inval=clean_text($_POST{"$inname"});
			if($Subject['Strategies']['value']!=$inval){
				print $Subject['Strategies']['label']." : ".$inval;
					if(mysql_query("UPDATE $table SET $field='$inval'
									WHERE senhistory_id='$senhid'")){
					$SEN['NationalCurriculum'][$key]['Strategies']['value']=$inval;	
					}
					else{$error[]=mysql_error();}
				}

			$table=$Subject['Weaknesses']['table_db'];
			$field=$Subject['Weaknesses']['field_db'];
			$inname=$field.$key;
			$inval=clean_text($_POST{"$inname"});
			if($Subject['Weaknesses']['value']!=$inval){
				$result[]=$Subject['Weaknesses']['label']." : ".$inval;
					if(mysql_query("UPDATE $table SET $field='$inval'
									WHERE senhistory_id='$senhid'")){
					$SEN['NationalCurriculum'][$key]['Weaknesses']['value']=$inval;	
					}
					else{$error[]=mysql_error();}
				}	
			}
		}

	$Student['SEN']=$SEN;
	$_SESSION{'Student'}=$Student;
	}
	include("scripts/results.php");
	include("scripts/redirect.php");
?>