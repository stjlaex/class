<?php
/**								   teacher_matrix_unassign.php
 *
 */

$choice='teacher_matrix.php';
$action='teacher_matrix.php';

if(isset($_POST['tids'])){$tids=(array)$_POST['tids'];}else{$tids=array();}
if(isset($_POST['curryear'])){$curryear=$_POST['curryear'];}else{$curryear=get_curriculumyear();}

include('scripts/sub_action.php');

if(sizeof($tids)==0){
		$result[]=get_string('youneedtoselectsomething');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}

if($sub=='Unassign'){

	if($r>-1){
		$rcrid=$respons[$r]['course_id'];
	 	$rbid=$respons[$r]['subject_id'];

		foreach($tids as $tid){
			$classes=(array)list_teacher_classes($tid,$rcrid,$curryear);
			foreach($classes as $class){
				$cid=$class['id'];
				if($class['bid']==$rbid or $rbid=='%'){
					mysql_query("DELETE FROM tidcid WHERE teacher_id='$tid' AND class_id='$cid' LIMIT 1;");
					}
				}
			}
		}

	}

include('scripts/redirect.php');

?>
