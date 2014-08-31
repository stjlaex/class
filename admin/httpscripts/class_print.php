<?php
/**									group_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

/*NB. The comids are pulled by checksidsAction hence they really are called sids!*/
if(isset($_GET['sids'])){$tids=(array)$_GET['sids'];}else{$tids=array();}
if(isset($_POST['sids'])){$tids=(array)$_POST['sids'];}

$respons=$_SESSION['respons'];
$r=$_SESSION['r'];
list($crid,$bid,$error)=checkCurrentRespon($r,$respons);
$curryear=get_curriculumyear();

//$tids=array('admin3');

if(sizeof($tids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';

	}
else{

	$Students=array();
	$Students['Group']=array();

	foreach($tids as $tid){
		if($tid!=''){
			trigger_error($tid,E_USER_WARNING);
			$classes=list_teacher_classes($tid,$crid,'%',$curryear);
			foreach($classes as $no => $class){
				$cid=$class['id'];
				$class=get_this_class($cid);
				$students=(array)listin_class($cid,true);

				$Group=array();
				$Group['Name']=array('value'=>$class['name']);
				$Group['Student']=array();
				$Group['Tutor']=array('value'=>$tid);

				/* list any repsonsible teachers 
				$tutors=(array)list_community_users($com,array('r'=>1,'w'=>1,'x'=>1));
				foreach($tutors as $tutor){
					if($CFG->teachername=='formal'){
						$teachername=$tutor['forename'][0].' '.$tutor['surname'];
						}
					elseif($CFG->teachername=='informal'){
						$teachername=$tutor['forename'];
						}
					else{
						$teachername=$tutor['forename'].' '.$tutor['surname'];
						}
					$Group['Tutor'][]=array('label' => 'formtutor', 
											'email' => ''.$tutor['email'],
											'value' => ''.$teachername
											);
					}
				*/

				foreach($students as $student){
					$sid=$student['id'];
					$Student=(array)fetchStudent_short($sid);
					//$Student['Attendances']=(array)fetchAttendances($sid,$attday,1);
					//$Student['Journey']=array();
					//$field=fetchStudent_singlefield($sid,'FirstContactPhone');
					//$Student=array_merge($Student,$field);
					//$field=fetchStudent_singlefield($sid,'SecondContactPhone');
					//$Student=array_merge($Student,$field);
					$Group['Student'][]=$Student;
					}
				$Students['Group'][]=$Group;
				}
			}
		}

	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
