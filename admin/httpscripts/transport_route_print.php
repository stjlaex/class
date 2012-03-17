<?php
/**									transport_route_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

/*NB. The busnames are pulled by checksidsAction hence they honestly really are called sids!*/
if(isset($_GET['sids'])){$busnames=(array)$_GET['sids'];}else{$busnames=array();}
if(isset($_POST['sids'])){$busnames=(array)$_POST['sids'];}


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
		if($busname!=''){
			$Transport=array();
			$Transport['Name']=array('value'=>$busname);
			$directions=array('I','O');
			foreach($directions as $direction){
				$bus=(array)get_bus('',$busname,$direction,'%');
				$stops=list_bus_stops($bus['id']);
				$Journey=array();
				$Journey['Direction']=$bus['direction'];
				$Journey['Bus']=array('id_db'=>$bus['id'],
									  'value'=>$busname,
									  'time'=>$bus['departuretime']
									  );
				list($h,$m,$s)=explode(':',$bus['departuretime']);
				$deptime=$h*3600+$m*60+$s;
				$Stops=array();
				$Stops['Stop']=array();
				foreach($stops as $stop){
					$deptime+=$stop['traveltime']*60;
					$Stops['Stop'][]=array('id_db'=>$stop['id'],
										   'sequence'=>$stop['sequence'],
										   'name'=>$stop['name'],
										   'detail'=>$stop['detail'],
										   'time'=>$stop['traveltime'],
										   'departuretime'=>gmdate('H:i',$deptime)
										   );
					}
				$Journey['Stops']=$Stops;
				$Transport['Journey'][]=$Journey;
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
