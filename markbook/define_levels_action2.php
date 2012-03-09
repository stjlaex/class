<?php 
/**
 *			   			   					define_levels_action2.php
 */

$action='column_level.php';
$action_post_vars=array('checkmid');

include('scripts/sub_action.php');

if(isset($_POST['checkmid'])){$checkmid=$_POST['checkmid'];}

$gena=$_POST['gena'];
$lena=$_POST['lena'];
$comment=$_POST['comment'];
$grades=$_POST['grades'];
$crid=$_POST['crid'];
$bid=$_POST['bid'];
$levels=$_POST['levels'];

	$pairs=explode(';', $grades);	
	list($level_grade, $level)=explode(':',$pairs[0]);
	/*the first boundary must be equal to zero!*/
	$levellist=$level_grade.':0';
	$previous=0;


   	for($c=1;$c<sizeof($levels);$c++){
		if($levels[$c]>$previous){
			list($level_grade, $level)=explode(':',$pairs[$c]);
			$levellist=$levellist.';'.$level_grade.':'.$levels[$c];
			$previous=$levels[$c];
			}
		else{
			$error[]='The level boundaries must be in ascending order!';	
			$current='define_levels_action1.php';
			$action_post_vars=array('gena','lena','comment');
			}
		}

	if(!isset($error)){
			mysql_query("INSERT INTO levelling SET
			name='$lena', grading_name='$gena', levels='$levellist',
			comment='$comment', author='$tid', course_id='$crid', subject_id='$bid' ");
			}

include('scripts/results.php');
include('scripts/redirect.php');
?>
