<?php 
/**									 report_assessments_action.php
 *
 */

$action='report_assessments.php';
$action_post_vars=array('year','stage','selfid','selyid','profid','gender');

if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['selfid'])){$selfid=$_POST['selfid'];}
if(isset($_POST['selyid'])){$selyid=$_POST['selyid'];}
if(isset($_POST['profid'])){$profid=$_POST['profid'];}
if(isset($_POST['gender'])){$gender=$_POST['gender'];}
if(isset($_POST['newfid']) and $_POST['newfid']!=$selfid){$selfid=$_POST['newfid'];$selyid='';}
elseif(isset($_POST['newyid']) and $_POST['newyid']!=$selyid){$selyid=$_POST['newyid'];$selfid='';}


include('scripts/sub_action.php');

if($sub=='Submit'){
	$action='report_assessments_view.php';
	}

include('scripts/redirect.php');
?>