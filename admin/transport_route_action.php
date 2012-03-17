<?php
/**			  					transport_route_action.php
 */

$action='transport_route.php';
$cancel='transport.php';

if(isset($_POST['busname'])){$busname=$_POST['busname'];}else{$busname='';}
$action_post_vars=array('busname');

include('scripts/sub_action.php');

if($sub=='Submit' and $busname!=''){

	$directions=array('I','O');
	foreach($directions as $direction){
		$stids=(array)$_POST['stids'.$direction];
		$names=(array)$_POST['names'.$direction];
		$times=(array)$_POST['times'.$direction];
		$sequences=(array)$_POST['sequences'.$direction];
		$laststop=$_POST['last'.$direction];
		$bus=(array)get_bus('',$busname,$direction,'%');
		$stops=(array)list_bus_stops($bus['id']);

		/* Starting departure time for bus. */
		if(isset($_POST['deptime'.$direction])){$deptime=checkEntry($_POST['deptime'.$direction],'time');}
		else{$deptime='';}
		if(!empty($deptime)){
			$busid=$bus['id'];
			mysql_query("UPDATE transport_bus SET departuretime='$deptime' WHERE id='$busid';");
			}

		/* Blank the route and then re-enter from scratch. */
		$rtid=$bus['route_id'];
		mysql_query("DELETE FROM transport_rtidstid WHERE route_id='$rtid';");	

		for($no=0;$no<=$laststop;$no++){
			if(isset($stids[$no]) and $stids[$no]!=0 and $sequences[$no]!=''){
				/* Update an existing stop */
				mysql_query("INSERT transport_rtidstid SET route_id='$rtid', stop_id='$stids[$no]', traveltime='$times[$no]', sequence='$sequences[$no]';");
				mysql_query("UPDATE transport_stop SET name='$names[$no]' WHERE id='$stids[$no]';");
				}
			elseif(isset($stids[$no]) and $stids[$no]==0 and $names[$no]!='' and $sequences[$no]!=''){
				/* Add a new stop */
				mysql_query("INSERT transport_stop SET name='$names[$no]';");
				$stid=mysql_insert_id();
				mysql_query("INSERT transport_rtidstid SET route_id='$rtid', stop_id='$stid', traveltime='$times[$no]', sequence='$sequences[$no]';");
				}
			elseif(isset($stids[$no]) and $stids[$no]!=0 and $sequences[$no]==''){
				/*TODO: amend journeys (not delete!) affected by removed stops */

				}
			}
		}
	}

include('scripts/redirect.php');
?>
