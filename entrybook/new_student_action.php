<?php
/**			  					new_student_action.php
 */

$action='new_contact.php';
include('scripts/sub_action.php');

if($sub=='Submit'){

	$Student=fetchStudent();
	mysql_query("INSERT INTO student SET surname=''");
	$sid=mysql_insert_id();
	mysql_query("INSERT INTO info SET student_id='$sid'");
	reset($Student);
	while(list($key,$val)=each($Student)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
				if($val['table_db']=='student'){
					mysql_query("UPDATE student SET $field='$inval'	WHERE id='$sid'");
					}
				elseif($val['table_db']=='info'){
					mysql_query("UPDATE info SET $field='$inval' WHERE student_id='$sid'");
					}
				}
			}
		}


	if(isset($_POST['boarder']) and $_POST['boarder']!='' and $_POST['boarder']!='N'){
		/*extra fields for residencial students*/
		mysql_query("INSERT INTO accomodation SET student_id='$sid'");
		$accid=mysql_insert_id();
		$Stay=fetchStay();
		reset($Stay);
		while(list($key,$val)=each($Stay)){
			if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
				$field=$val['field_db'];
				$inname=$field;
				$inval=clean_text($_POST[$inname]);
				if($val['table_db']=='accomodation'){
					mysql_query("UPDATE accomodation SET
							$field='$inval' WHERE id='$accid'");
					}
				}
			}
		$comname=$_POST['gender']. $_POST['roomcategory']. $_POST['boarder'];
		$community=array('id'=>'','type'=>'accomodation','name'=>$comname);
		set_community_stay($sid,$community,$_POST['arrivaldate'],$_POST['departuredate']);
		}


	/*Figure out the community they need to join*/
	if($enrolstatus=='C' or $enrolstatus=='G'){
		/*joining the current roll directly is special*/
		/*as the student must join a yeargroup community*/
		$community=array('id'=>'','type'=>'year','name'=>$enrolyid);
		}
	else{
		if($enrolstatus=='P'){$comtype='alumni';}
		elseif($enrolstatus=='EN'){$comtype='enquired';}
		elseif($enrolstatus=='AC'){$comtype='accepted';}
		/*all other enrolstatus values place the student within the*/
		/*applied category - apllication is in progress*/
		else{$comtype='applied';}
		$comname=$enrolstatus.':'.$enrolyid;
		$community=array('id'=>'','type'=>$comtype,'name'=>$comname,'year'=>$enrolyear);
		}

	join_community($sid,$community);
//	$result[]=get_string('newstudentadded',$book);


//include('scripts/results.php');
include('scripts/redirect.php');
?>
