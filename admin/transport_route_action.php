<?php
/**			  					transport_route_action.php
 */

$action='transport_route.php';
$cancel='transport_route.php';

if(isset($_POST['busname'])){$busname=$_POST['busname'];}else{$busname='';}
if(isset($_POST['stids'])){$stids=(array)$_POST['stids'];}else{$stids=array();}
if(isset($_POST['names'])){$names=(array)$_POST['names'];}else{$names=array();}
if(isset($_POST['times'])){$times=(array)$_POST['times'];}else{$times=array();}
if(isset($_POST['sequences'])){$sequences=(array)$_POST['sequences'];}else{$sequences=array();}
$action_post_vars=array('busname');

include('scripts/sub_action.php');

if($sub=='Submit' and $busname!=''){

	$directions=array('I','O');
	$no=0;
	foreach($directions as $direction){
		$laststop=$_POST['last'.$direction]+$no;
		$bus=(array)get_bus('',$busname,$direction,'%');
		$stops=(array)list_bus_stops($bus['id']);

		$rtid=$bus['route_id'];
		mysql_query("DELETE FROM transport_rtidstid WHERE route_id='$rtid';");	

		for($no;$no<=$laststop;$no++){
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
