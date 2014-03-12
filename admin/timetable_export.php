<?php
/**								   timetable_export.php
 *
 */

$action='timetable.php';

include('scripts/answer_action.php');


	$filepath=$CFG->eportfolio_dataroot. '/cache/files/';
  	$filepath.='class_export.fet';
  	$file=fopen($filepath, 'w');
	if(!$file){
		$error[]='unabletoopenfileforwriting';
		}
	else{
		$xmllines=array();
		$xmllines['Institution_Name']=$CFG->schoolname;
		$xmllines['Comments']='Testing';
		$xmllines['Hours_List']=array();
		$Names=array();
		//$Names[]='08.00';
		$Names[]='P1';
		$Names[]='P2';
		$Names[]='P3';
		$Names[]='P4';
		$Names[]='P5';
		$Names[]='P6';
		$Names[]='P7';
		$Names[]='P8';
		//$Names[]='17.00';
		//$Names[]='18.00';
		//$Names[]='19.00';
		$xmllines['Hours_List']['Number']=sizeof($Names);
		$xmllines['Hours_List']['Name']=$Names;


		$xmllines['Days_List']=array();
		$Names=array();
		$Names[]='Monday';
		$Names[]='Tuesday';
		$Names[]='Wednesday';
		$Names[]='Thursday';
		$Names[]='Friday';
		//$Names[]='Saturday';
		$xmllines['Days_List']['Number']=sizeof($Names);
		$xmllines['Days_List']['Name']=$Names;
		$xmllines['Students_List']=array();
		$years=list_yeargroups();
		while(list($index,$year)=each($years)){
			$comid=update_community(array('type'=>'year','name'=>$year['id']));
			$com=get_community($comid);
			$nosids=countin_community($com);
			$forms=list_formgroups($year['id']);
			$Groups=array();
			while(list($index,$form)=each($forms)){
				$Group=array();
				$Group['Name']=$form['name'];
				$Group['Number_of_Students']=countin_community($form);
				$Groups[]=$Group;
				}
			$Year=array();
			$Year['Name']=$year['name'];
			$Year['Number_of_Students']=$nosids;
			$Year['Group']=$Groups;
			$Years[]=$Year;
			}
		$xmllines['Students_List']['Year']=$Years;

		$xmllines['Teachers_List']=array();
		$users=list_teacher_users('','','%');
		$Teachers=array();
		while(list($index,$user)=each($users)){
			$Teacher=array();
			$Teacher['Name']=strtoupper($user['username']);
			$Teachers[]=$Teacher;
			}
		$xmllines['Teachers_List']['Teacher']=$Teachers;	
		$xmllines['Subjects_List']=array();
		$courses=list_courses();
		$subjects=list_course_subjects('%');
		while(list($index,$subject)=each($subjects)){
			$Subject=array();
			$Subject['Name']=$subject['id'];
			$Subjects[]=$Subject;
			}


		$xmllines['Activities_List']=array();
		$Activities=array();
		$Time_Constraints=array();
		$Time_Constraints['ConstraintBasicCompulsoryTime']['Weight_Percentage']=100;
		$Space_Constraints=array();
		$Space_Constraints['ConstraintBasicCompulsorySpace']['Weight_Percentage']=100;
		$fetaid=1;
		$fetgid=1;
		$blocks=array();
		$courses=list_courses();
		while(list($indexcourse,$course)=each($courses)){
			$crid=$course['id'];
			$stages=list_course_stages($crid);
			$subjects=list_course_subjects($crid);
			while(list($indexstage,$stage)=each($stages)){
				$stage=$stage['id'];
				reset($subjects);
				while(list($indexsubject,$subject)=each($subjects)){
					$bid=$subject['id'];
					$classdef=get_subjectclassdef($crid,$bid,$stage);
					if($classdef['many']>-1){
//trigger_error($crid.' '.$bid.' '.$stage. ' '.$classdef['many'],E_USER_WARNING);
						$total_duration=$classdef['dp']*2+$classdef['sp'];
						$blockid=$classdef['block'];
						if($blockid!='' and !is_array($blocks[$blockid])){
							$blocks[$blockid]=array();
							}
						list($newcids,$groups)=get_classdef_classes($classdef);
						//$classes=list_course_classes($crid,$bid,$stage);
						while(list($indexclass,$newcid)=each($newcids)){
							$actno=0;
							$actids=array();
							$Activity=array();
							$Time_Constraint=array();
							$Activity['Subject']=''.$bid;
							$Activity['Active']='yes';
							//$Activity['Activity_Tag']='';
							$Activity['Activity_Group_Id']=$fetgid++;
							$Activity['Students']=$groups[$indexclass];
							$Activity['Total_Duration']=$total_duration;
							$teachers=(array)list_class_teachers($newcid);
							if(sizeof($teachers)>0){
								$Activity['Teacher']=array();
								while(list($indexteacher,$teacher)=each($teachers)){
									$Activity['Teacher'][]=strtoupper($teacher['id']);
									}
								}
							for($c=0;$c<$classdef['sp'];$c++){
								$Activity['Duration']='1';
								$actids['Activity_Id'][]=$fetaid;
								if($blockid!=''){$blocks[$blockid][$actno][]=$fetaid;}
								$Activity['Id']=$fetaid++;
								$Activities[]=$Activity;
								$actno++;
								}
							for($c=0;$c<$classdef['dp'];$c++){
								$Activity['Duration']='2';
								$actids['Activity_Id'][]=$fetaid;
								if($blockid!=''){$blocks[$blockid][$actno][]=$fetaid;}
								$Activity['Id']=$fetaid++;
								$Activities[]=$Activity;
								$actno++;
								}
							if($actno>1){
								$Time_Constraint['Weight_Percentage']='95';
								$Time_Constraint['Consecutive_If_Same_Day']='yes';
								$Time_Constraint['Number_of_Activities']=$actno;
								$Time_Constraint=$Time_Constraint+$actids;
								$Time_Constraint['MinDays']='1';
								$Time_Constraints['ConstraintMinNDaysBetweenActivities'][]=$Time_Constraint;
								}
							}
						}
					}
				}
			}

		if(sizeof($blocks)>0){
			while(list($blockid,$block)=each($blocks)){
				while(list($actno,$fetaids)=each($block)){
					$actids['Activity_Id']=(array)$fetaids;
					$Time_Constraint=array();
					$Time_Constraint['Weight_Percentage']='95';
					$Time_Constraint['Number_of_Activities']=sizeof($fetaids);
					$Time_Constraint=$Time_Constraint+$actids;
					$Time_Constraints['ConstraintActivitiesSameStartingTime'][]=$Time_Constraint;
					}
				}
			}

		$xmllines['Subjects_List']['Subject']=$Subjects;

		$xmllines['Activity_Tags_List']=array();

		$xmllines['Activities_List']['Activity']=$Activities;

		$xmllines['Time_Constraints_List']=$Time_Constraints;

		$xmllines['Space_Constraints_List']=$Space_Constraints;

		$xmllines['Rooms_List']=array();

		/**
		 * 
		 */
		$options=array(
					   'addDecl' => true,
					   'encoding' => 'UTF-8',
					   'indent' => '  ',
					   'mode' => 'simplexml',
					   'rootName' => 'FET',
					   'rootAttributes'=> array('version'=>'5.5.3'),
					   'addDoctype' => true,
					   'doctype' => array(
					   				  'uri' => '',
					   				  'id'  => '')
					   );

		$xml=xmlpreparer('FET',$xmllines,$options);
		fwrite($file, $xml);

	   	fclose($file);
		$result[]='exportedtofile';
?>
		<script>openXMLExport('fet');</script>
<?php
		}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
