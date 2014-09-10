<?php 
/**										new_search_action.php
 *
 *
 */

if(isset($_POST['comids'])){$comids=(array)$_POST['comids'];}

$action_post_vars=array('selsavedview');

$sids=array();

foreach($comids as $comid){
	$d_s=mysql_query("SELECT comidsid.student_id,community.name,info.enrolstatus FROM comidsid JOIN community ON community.id=comidsid.community_id JOIN info ON info.student_id=comidsid.student_id WHERE community_id='$comid';");
	while($student=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		$commyear=$student['name'];
		if($commyear==get_curriculumyear()){
			if($student['enrolstatus']=='C'){
				$sids[]=$student['student_id'];
				}
			}
		else{
			$sids[]=$student['student_id'];
			}
		}
	}


$_SESSION['infosids']=$sids;
$_SESSION['infosearchgids']=array();
if(!isset($nolist)){
	$action='student_list.php';
	include('scripts/redirect.php');
	}
?>
