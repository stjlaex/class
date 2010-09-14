<?php
/**									transport_print.php
 *
 */

require_once('../../scripts/http_head_options.php');
require_once('../../lib/fetch_transport.php');

if(isset($_GET['sids'])){$busnames=(array)$_GET['sids'];}else{$busnames=array();}
if(isset($_POST['sids'])){$busnames=(array)$_POST['sids'];}

$todate=date('Y-m-d');
$today=date('N');
$buses=list_buses();

if(sizeof($busnames)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{

	$Students=array();
	$Students['Transport']=array();

	foreach($busnames as $busname){
		//trigger_error($busname,E_USER_WARNING);
		if($busname!=''){

			$busin=(array)get_bus('',$busname,'I',$today);
			$buses[$busin['id']]['stops']=list_bus_stops($busin['id']);
			$busout=(array)get_bus('',$busname,'O',$today);
			$buses[$busout['id']]['stops']=list_bus_stops($busout['id']);

			$com=array('id'=>'','type'=>'transport','name'=>$busname);
			$students=(array)listin_community($com);
			//$Transport=(array)fetchCommunity($comid);
			$Transport=array();
			$Transport['Name']=array('value'=>$busname);
			$Transport['Day']=array('value'=>get_string(displayEnum($today,'dayofweek'),$book));
			$Transport['Date']=array('value'=>display_date($todate));
			$Transport['Student']=array();
			$community=array('id'=>'','name'=>'','type'=>'tutor');

			while(list($index,$student)=each($students)){
				$sid=$student['id'];
				$Student=(array)fetchStudent_short($sid);

				/* TODO: After school clubs */
				$communities=(array)list_member_communities($sid,$community);
				foreach($communities as $club){
					$pos=strpos($club['detail'],"A$today");
					if($pos>0){
						$Student['Club']['value']=$club['name'];
						}
					}

				$Student['Attendance']=fetchcurrentAttendance($sid);
				$Student['Journey']=array();
				$field=fetchStudent_singlefield($sid,'FirstContactPhone');
				$Student=array_merge($Student,$field);
				$field=fetchStudent_singlefield($sid,'SecondContactPhone');
				$Student=array_merge($Student,$field);
				$bookings=array();
				$bookings=(array)list_student_journey_bookings($sid,$todate,$today);
				foreach($bookings as $booking){
					if($booking['bus_id']==$busin['id'] or $booking['bus_id']==$busout['id']){
						$Journey=array();
						$Journey['Direction']=$buses[$booking['bus_id']]['direction'];
						$Journey['Comment']['value']=$booking['comment'];
						$Journey['Bus']=array('id_db'=>$booking['bus_id'],
											  'value'=>$buses[$booking['bus_id']]['name']
											  );
						$Journey['Stop']=array('id_db'=>$booking['stop_id'],
											   'sequence'=>$buses[$booking['bus_id']]['stops'][$booking['stop_id']]['sequence'],
											   'value'=>$buses[$booking['bus_id']]['stops'][$booking['stop_id']]['name']
											   );
						$Student['Journey'][]=$Journey;
						}
					}
				$Transport['Student'][]=$Student;
				}
			$Students['Transport'][]=$Transport;
			}
		}
	$Students['Transform']='transport_list';
	$Students['Paper']='portait';

	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
