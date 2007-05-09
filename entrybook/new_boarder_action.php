<?php
/**									new_boarder_action.php
 */

$action_post_vars=array('sid');

if(isset($_POST['sid'])){
	/*this means part of a new_student sequence*/
	$action='new_contact.php';
	$sid=$_POST['sid'];
	$Student=fetchStudent_short($sid);
	$Boarder=fetchStudent_singlefield($sid,'Boarder');
	}
else{
	$action='new_booking.php';
	}

$Stay=fetchStay();

include('scripts/sub_action.php');

if($sub=='Submit'){

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

	if(isset($sid)){
		/*joins an accomodation community named to match gender/roomcat/boardercode*/
		/*eg. MGLH (male group leader host family) or MB (male boarder)*/
		$comname=$Student['Gender']['value']. $_POST['roomcategory']. 
						$Boarder['value'];
		$community=array('id'=>'','type'=>'accomodation','name'=>$comname);
		set_community_stay($sid,$community,$_POST['arrivaldate'],$_POST['departuredate']);
		}
	}

include('scripts/redirect.php');
?>