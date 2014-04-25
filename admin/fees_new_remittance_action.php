<?php
/**			  					fees_new_remittance_action.php
 */

$action='fees_remittance_list.php';
$cancel='fees_remittance_list.php';
$conids=(array)$_POST['conids'];
$yids=(array)$_POST['newyids'];
$feeyear=$_POST['feeyear'];
$accid=$_POST['accid'];
$remid=$_POST['remid'];
$enrolstatus=$_POST['enrolstatus'];

if(isset($_POST['editsids']) and $_POST['editsids']=="true"){$editsids=true;$action='fees_new_remittance_action_edit.php';}else{$editsids=false;}


$action_post_vars=array('feeyear');
include('scripts/sub_action.php');

if($sub=='Submit'){

	if($remid==-1){
		/* new remittance */
		mysql_query("INSERT INTO fees_remittance SET year='$feeyear';");
		$remid=mysql_insert_id();
		}
	else{
		/* exisitng remittance - delete all charges before re-making because concepts may have changed. */
		mysql_query("DELETE FROM fees_charge WHERE remittance_id='$remid';");
		}

	mysql_query("UPDATE fees_remittance SET account_id='$accid' WHERE id='$remid';");

	$Remittance=fetchRemittance();
	foreach($Remittance as $index => $val){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='fees_remittance'){
				mysql_query("UPDATE fees_remittance SET $field='$inval' WHERE id='$remid';");
				}
			}
		}

	$yid_list='';
	$separator='';
	foreach($yids as $yid){
		$yid_list.=$separator. $yid;
		$separator=':::';
		}
	mysql_query("UPDATE fees_remittance SET yeargroups='$yid_list' WHERE id='$remid';");

	$concept_list='';
	$separator='';
	foreach($conids as $conid){
		$concept=get_concept($conid);
		$concept_list.=$separator. $conid;
		$separator=':::';

		/* TODO: filter for students by payment type and set in fees_charge */


		if($concept['community_type']=='' and $enrolstatus=='C'){
			/*			mysql_query("INSERT INTO fees_charge (student_id, remittance_id, tarif_id, paymenttype, amount) 
							SELECT a.student_id, '$remid', a.tarif_id, a.paymenttype, t.amount FROM fees_applied AS a, 
							fees_tarif AS t WHERE t.concept_id='$conid' AND t.id=a.tarif_id AND a.student_id=ANY(SELECT student_id FROM info WHERE enrolstatus='$enrolstatus');");
			*/

			$communities=list_communities('year');
			foreach($communities as $community){
				if(in_array($community['name'],$yids)){
					$comid=$community['id'];
					$community=(array)get_community($comid);
					$students=(array)listin_community($community);
					foreach($students as $student){
						$sid=$student['id'];
						if(!$editsids){
							mysql_query("INSERT INTO fees_charge (student_id, remittance_id, tarif_id, paymenttype, amount) 
			SELECT a.student_id, '$remid', a.tarif_id, a.paymenttype, t.amount FROM fees_applied AS a, 
			fees_tarif AS t WHERE t.concept_id='$conid' AND t.id=a.tarif_id AND a.student_id='$sid';");
							}
						else{
							$charges=list_student_fees($sid);
							foreach($charges as $chargeconid=>$charge){
								if($chargeconid==$conid){
									$sids[$sid]=$sid;
									}
								}
							$conceptids[$conid]=$conid;
							}
						}
					}
				}

			}
		elseif($enrolstatus!='C'){
			if($enrolstatus=='EN'){$community_type='enquired';}
			elseif($enrolstatus=='AC'){$community_type='accepted';}
			else{$community_type='applied';}
			$enrolyear=get_curriculumyear()+1;
			$students=array();
			foreach($yids as $yid){
				$com=array('id'=>'','type'=>$community_type, 
						   'name'=>$enrolstatus.':'.$yid,'year'=>$enrolyear);
				$yearstudents=(array)listin_community($com);
				$students=array_merge($students,$yearstudents);
				}
			
			foreach($students as $student){
				$sid=$student['id'];
				if(!$editsids){
					mysql_query("INSERT INTO fees_charge (student_id, remittance_id, tarif_id, paymenttype, amount) 
					SELECT a.student_id, '$remid', a.tarif_id, a.paymenttype, t.amount FROM fees_applied AS a, 
					fees_tarif AS t WHERE t.concept_id='$conid' AND t.id=a.tarif_id AND a.student_id='$sid';");
					}
				else{
					$charges=list_student_fees($sid);
					foreach($charges as $chargeconid=>$charge){
						if($chargeconid==$conid){
							$sids[$sid]=$sid;
							}
						}
					$conceptids[$conid]=$conid;
					}
				}
			}
		else{
			$comtype=$concept['community_type'];
			$communities=list_communities($comtype);
			foreach($communities as $community){
				$comid=$community['id'];
				$community=(array)get_community($comid);
				$students=(array)listin_community($community);
				foreach($students as $student){
					$sid=$student['id'];
					if(!$editsids){
						$d_c=mysql_query("SELECT id FROM fees_charge 
								 WHERE student_id='$sid' AND remittance_id='$remid' AND community_id='$comid';");
						if(mysql_num_rows($d_c)>0){
							$charid=mysql_result($d_c);
							}
						else{
							if($student['special']=='' or $student['special']=='10'){$rate=1;}else{$rate=$student['special']/10;};
							$amount=$community['charge']*$rate;
							add_student_community_charge($sid,$comid,$remid,$amount);
							}
						}
					else{
						$sids[$sid]=$sid;
						$conceptids[$conid]=$conid;
						}
					}
				}
			}

		}
	mysql_query("UPDATE fees_remittance SET concepts='$concept_list' WHERE id='$remid';");

	}

	/*Set SESSION variables for editing sids*/
	if($editsids){
		$_SESSION['sidsvars']=array();
		$_SESSION['conidsvars']=array();
		$_SESSION['remidvar']="";
		$_SESSION['enrolstatusvar']="";

		foreach($sids as $sid){
			$_SESSION['sidsvars'][$sid]=$sid;
			}
		foreach($conceptids as $conid){
			$_SESSION['conidsvars'][$conid]=$conid;
			}

		$_SESSION['remidvar']=$remid;
		$_SESSION['enrolstatusvar']=$enrolstatus;
		}
	if(isset($_SESSION['remittancestotals'])){
		$Remittance=fetchRemittance($remid);
		unset($_SESSION['remittancestotals'][$Remittance['Year']['value']]);
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
