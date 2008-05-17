<?php
/**								   timetable_export.php
 *
 */

$action='timetable.php';

include('scripts/answer_action.php');

  	$file=fopen('/tmp/class_export.fet', 'w');
	if(!$file){
		$error[]='unabletoopenfileforwriting';
		}
	else{
		$xmllines=array();
		$xmllines['Institution_Name']=$CFG->schoolname;
		$xmllines['Comments']='Testing';
		$xmllines['Hours_List']=array();
		$xmllines['Hours_List']['Number']='12';
		$Names=array();
		$Names[]='08.00';
		$Names[]='09.00';
		$Names[]='10.00';
		$Names[]='11.00';
		$Names[]='12.00';
		$Names[]='13.00';
		$Names[]='14.00';
		$Names[]='15.00';
		$Names[]='16.00';
		$Names[]='17.00';
		$Names[]='18.00';
		$Names[]='19.00';
		$xmllines['Hours_List']['Name']=$Names;


		$xmllines['Days_List']=array();
		$Names=array();
		$Names[]='Monday';
		$Names[]='Tuesday';
		$Names[]='Wednesday';
		$Names[]='Thursday';
		$Names[]='Friday';
		$Names[]='Saturday';
		$xmllines['Days_List']['Number']='6';
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
				$Group['Number_of_Students']='0';
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
		$users=list_teacher_users();
		while(list($index,$user)=each($users)){
			$Teacher=array();
			$Teacher['Name']=$user['username'];
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
		$fetid=1;
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
						$block=$classdef['block'];

						list($newcids,$groups)=get_classdef_classes($classdef);
						//$classes=list_course_classes($crid,$bid,$stage);
						while(list($indexclass,$newcid)=each($newcids)){
							$Activity=array();
							$Activity['Subject']=''.$bid;
							$Activity['Active']='yes';
							//$Activity['Activity_Tag']='';
							$Activity['Activity_Group_Id']=''.$block;
							$Activity['Students']=$groups[$indexclass];
							$Activity['Teacher']=array();
							$Activity['Total_Duration']=$total_duration;
							$teachers=list_class_teachers($newcid);
							while(list($indexteacher,$teacher)=each($teachers)){
								$Activity['Teacher'][]=$teacher['id'];
								}
							for($c=0;$c<$classdef['sp'];$c++){
								$Activity['Duration']='1';
								$Activity['Id']=$fetid++;
								$Activities[]=$Activity;
								}
							for($c=0;$c<$classdef['dp'];$c++){
								$Activity['Duration']='2';
								$Activity['Id']=$fetid++;
								$Activities[]=$Activity;
								}
							}
						}
					}
				}
			}

		$xmllines['Subjects_List']['Subject']=$Subjects;

		$xmllines['Activity_Tags_List']=array();

		$xmllines['Activities_List']['Activity']=$Activities;

		$xmllines['Rooms_List']=array();

		$xmllines['Time_Constraints_List']=array();

		$xmllines['Space_Constraints_List']=array();

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
		<script>openXMLExport();</script>
<?php
		}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
