<?php 
/* 			   					define_levels_action2.php
*/

$host="markbook.php";
$current="class_view.php";
$action="";
$choice="class_view.php";
if(isset($_POST{'mid'})){$mid=$_POST{'mid'};}

$sub=$_POST{'sub'};
if($sub=='Cancel'){
		$current="class_view.php";
		include("scripts/redirect.php");
		exit;
		}

$gena=$_POST['gena'];
$lena=$_POST['lena'];
$comment=$_POST['comment'];
$levels=$_POST['levels'];
$grades=$_POST['grades'];
$crid=$_POST['crid'];
$bid=$_POST['bid'];

		$pairs=explode (";", $grades);	
		list($level_grade, $level)=split(":",$pairs[0]);
		/*the first boundary must be equal to zero!*/
		$levellist=$level_grade.":0";
		$previous=0;

		for($c3=1; $c3<sizeof($levels); $c3++){
		    if ($levels[$c3]>$previous){
				list($level_grade, $level)=split(":",$pairs[$c3]);
				$levellist=$levellist.";".$level_grade.":".$levels[$c3];
				$previous=$levels[$c3];
				}
			else{
				$result[]="The level boundaries must be in ascending order!";	
				$current="define_levels_action1.php";
				include("scripts/results.php");
				include("scripts/redirect.php");
				exit;
				}
			}
		
		if(mysql_query("INSERT INTO levelling SET
			name='$lena', grading_name='$gena', levels='$levellist',
			comment='$comment', author='$tid', 
			course_id='$crid', subject_id='$bid' ")){
				$result[]="Levels defined.";
				}
	     else{
				$result[]="Failed on levelling insert!";	
				$error[]=mysql_error();
				}

	$current="column_level.php";
	include("scripts/results.php");
	include("scripts/redirect.php");

?>















































