<?php
/**									transport_print.php
 *
 */

require_once('../../scripts/http_head_options.php');
require_once('../../lib/fetch_transport.php');

/*NB. The busnames are pulled by checksidsAction hence they honestly really are called sids!*/
if(isset($_GET['sids'])){$busnames=(array)$_GET['sids'];}else{$busnames=array();}
if(isset($_POST['sids'])){$busnames=(array)$_POST['sids'];}
if((isset($_POST['date0']) and $_POST['date0']!='')){$printdate=$_POST['date0'];}else{$printdate=date('Y-m-d');}
if((isset($_GET['date0']) and $_GET['date0']!='')){$printdate=$_GET['date0'];}
if((isset($_POST['length']) and $_POST['length']!='')){$length=$_POST['length'];}else{$length='full';}
if((isset($_GET['length']) and $_GET['length']!='')){$length=$_GET['length'];}

$day=date('N',strtotime($printdate));
/* calculate difference in days from now for past attendance */
$d=explode('-',$printdate);
$diff=mktime(0,0,0,date('m'),date('d'),date('Y'))-mktime(0,0,0,$d[1],$d[2],$d[0]);
$attday=-round($diff/(60*60*24));

$buses=list_buses();

if(sizeof($busnames)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{

	$Students=array();
	$Students['Transport']=array();

	foreach($busnames as $typebusname){
		list($type,$busname)=explode('-',$typebusname);
		if($busname!='' and $type=='b'){
			$busin=(array)get_bus('',$busname,'I',$day);
			$buses[$busin['id']]['stops']=list_bus_stops($busin['id']);
			$busout=(array)get_bus('',$busname,'O',$day);
			$buses[$busout['id']]['stops']=list_bus_stops($busout['id']);
			$students=(array)list_bus_journey_students($busname,$printdate,0);
			}
		elseif($busname!='' and $type=='f'){
			$com=array('id'=>'','type'=>'form','name'=>$busname);
			$students=(array)listin_community($com);
			$busin='';$busout='';
			}

		if(sizeof($students)>0){
			$Transport=array();
			$Transport['Name']=array('value'=>$busname);
			$Transport['Type']=array('value'=>$type);
			$Transport['Day']=array('value'=>get_string(displayEnum($day,'dayofweek'),'admin'));
			$Transport['Date']=array('value'=>display_date($printdate));


			$Transport['Student']=array();


			$clubcommunity=array('id'=>'','name'=>'','type'=>'tutor');

			foreach($students as $student){
				$sid=$student['id'];
				$Student=(array)fetchStudent_short($sid);

				/* TODO: After school clubs */
				$communities=(array)list_member_communities($sid,$clubcommunity);
				foreach($communities as $club){
					$pos=strpos($club['sessions'],"A$day");
					if($pos!==false){
						$Student['Club']['value']=$club['name'];
						}
					}

				$Student['Attendances']=(array)fetchAttendances($sid,$attday,1);
				$Student['Journey']=array();
				$field=fetchStudent_singlefield($sid,'FirstContactPhone');
				$Student=array_merge($Student,$field);
				$field=fetchStudent_singlefield($sid,'SecondContactPhone');
				$Student=array_merge($Student,$field);
				$bookings=(array)list_student_journey_bookings($sid,$printdate,$day);
				$jout=false;$jin=false;
				$onbus=false;
				$busid='';
				foreach($bookings as $booking){
					if($booking['direction']=='I'){$jname='jin';if($busin!=''){$busid=$busin['id'];}}
					elseif($booking['direction']=='O'){$jname='jout';if($busout!=''){$busid=$busout['id'];}}
					/* 
					 * The first booking for a direction takes precedence.
					 */
					if($busid==$booking['bus_id']){$onbus=true;}
					if(!$$jname){
						$$jname=true;
						$Journey=array();
						$Journey['Direction']=$buses[$booking['bus_id']]['direction'];
						$Journey['Day']=$booking['day'];
						$Journey['Comment']['value']=$booking['comment'];
						$Journey['Bus']=array('id_db'=>$booking['bus_id'],
											  'value'=>$buses[$booking['bus_id']]['name']
											  );

						if(!isset($buses[$booking['bus_id']]['stops'])){
							$buses[$booking['bus_id']]['stops']=(array)list_bus_stops($booking['bus_id']);
							}

						$Journey['Stop']=array('id_db'=>$booking['stop_id'],
											   'sequence'=>$buses[$booking['bus_id']]['stops'][$booking['stop_id']]['sequence'],
											   'value'=>$buses[$booking['bus_id']]['stops'][$booking['stop_id']]['name']
											   );
						$Student['Journey'][]=$Journey;
						}
					else{
						/* 
						 * More than one booking would indicate this is not their regular booking.
						 */
						//trigger_error($sid. ' : ' .$buses[$booking['bus_id']]['name'],E_USER_WARNING);
						$Journey=array();
						$Journey['Direction']=$buses[$booking['bus_id']]['direction'];
						$Journey['Bus']=array('id_db'=>$booking['bus_id'],
											  'value'=>$buses[$booking['bus_id']]['name']
											  );
						$Journey['Stop']=array('id_db'=>$booking['stop_id'],
											   'sequence'=>$buses[$booking['bus_id']]['stops'][$booking['stop_id']]['sequence'],
											   'value'=>$buses[$booking['bus_id']]['stops'][$booking['stop_id']]['name']
											   );
						$Student['OtherJourney'][]=$Journey;
						}
					}
				/* Only include the student in the list if they have a journey of some sort for this day. */
				/* If the list is for a form then include all */
				if(($onbus or $type=='f') and ($jin or $jout)){
					$Transport['Student'][]=$Student;
					}
				}
			$Students['Transport'][]=$Transport;
			}
		}


	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
