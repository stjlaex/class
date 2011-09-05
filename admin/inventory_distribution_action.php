<?php
/**									inventory_distribution_action.php
 */

$action='inventory_distribution.php';

$budgetyear=$_POST['budgetyear'];
if(isset($_POST['budid'])){$budid=$_POST['budid'];}else{$budid=-1;}
if(isset($_POST['newfid'])){$newfid=$_POST['newfid'];}else{$newfid='';}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid=-1;}

$action_post_vars=array('budgetyear','budid','newfid','catid');

include('scripts/sub_action.php');


if($sub=='Submit'){

	$com=get_community($newfid);
	$students=(array)listin_community($com);

	foreach($students as $student){
		$sid=$student['id'];

		if(isset($_POST["remove$sid"])){
			$rem=clean_text($_POST["remove$sid"]);
			if($rem==$sid){
				mysql_query("DELETE FROM fees_charge WHERE student_id='$sid' AND budget_id='$budid' AND catalogue_id='$catid' AND payment='0';");
				}
			}

		if(isset($_POST["add$sid"]) and $_POST["add$sid"]>0){
			$concept='books';
			$add=clean_text($_POST["add$sid"]);
			mysql_query("INSERT INTO fees_charge SET student_id='$sid', budget_id='$budid',
					concept='$concept', quantity='$add', catalogue_id='$catid',
					payment='0';");
			}
		}
	}


include('scripts/redirect.php');
?>
