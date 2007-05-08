<?php 
/* 			   					define_levels_action2.php
*/

$action='column_level.php';

include('scripts/sub_action.php');

if(isset($_POST['mid'])){$mid=$_POST['mid'];}

$sub=$_POST['sub'];

$gena=$_POST['gena'];
$lena=$_POST['lena'];
$comment=$_POST['comment'];
$levels=$_POST['levels'];
$grades=$_POST['grades'];
$crid=$_POST['crid'];
$bid=$_POST['bid'];

		$pairs=explode (';', $grades);	
		list($level_grade, $level)=split(':',$pairs[0]);
		/*the first boundary must be equal to zero!*/
		$levellist=$level_grade.':0';
		$previous=0;

		for($c3=1; $c3<sizeof($levels); $c3++){
		    if ($levels[$c3]>$previous){
				list($level_grade, $level)=split(':',$pairs[$c3]);
				$levellist=$levellist.';'.$level_grade.':'.$levels[$c3];
				$previous=$levels[$c3];
				}
			else{
				$error[]='The level boundaries must be in ascending order!';	
				$current='define_levels_action1.php';
				$action_post_vars=array('gena','lena','comment');
				include('scripts/results.php');
				include('scripts/redirect.php');
				exit;
				}
			}
		
		if(mysql_query("INSERT INTO levelling SET
			name='$lena', grading_name='$gena', levels='$levellist',
			comment='$comment', author='$tid', 
			course_id='$crid', subject_id='$bid' ")){
				$result[]='Levels defined.';
				}
	     else{
				$error[]='Failed on levelling insert!';	
				}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>















































