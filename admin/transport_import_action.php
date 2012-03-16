<?php
/**							    transport_import_action.php
 *
 *	This will import a csv file of bus journeys for a whole
 *  bunch of students. The student identified either by their ClaSS
 *  db id (sid) or by their enrolment no.
 *
 *  It will contruct new routes from the stops but needs the buses
 *  already configured to match that of the file.
 *
 *  WARNING: All existing route and journey information is overwritten!!!!
 */

$action='transport.php';

include('scripts/sub_action.php');

$firstcol=$_POST['firstcol'];
$colstart=$_POST['colstart'];
if($_POST['separator']=='semicolon'){$separator=';';}else{$separator=',';}

if($sub=='Submit'){

	$fname=$_FILES['importfile']['tmp_name'];
	if($fname!=''){
   	   	$result[]='Loading file '.$fname;
   		include('scripts/file_import_csv.php');
		if(sizeof($inrows>0)){
			}
		else{
			$error[]='The file was empty!';
			}
		}
	else{
		$error[]='No file specified!';
		}


	if(!isset($error)){
		mysql_query("TRUNCATE TABLE transport_stop;");
		mysql_query("TRUNCATE TABLE transport_rtidstid;");
		mysql_query("TRUNCATE TABLE transport_journey;");
		mysql_query("TRUNCATE TABLE transport_booking;");
		$routes=array();

		/* First contruct the routes. */
		foreach($inrows as $row){
			$busname=trim($row[$colstart-1]);
			$stopname=clean_text($row[$colstart]);
			$direction=trim($row[$colstart+1]);
			$stoptime=$row[$colstart+2];
			/* This is very particular format eg. 2-01 (bus 2 AM stop 1) or 2-51 (bus 2 PM stop 1)*/
			list($busno,$stopcode)=explode('-',$row[$colstart+3]);
			$d_s=mysql_query("SELECT id FROM transport_stop WHERE name='$stopname';");
			if(mysql_num_rows($d_s)>0){
				$stid=mysql_result($d_s,0);
				}
			else{
				$d_s=mysql_query("INSERT INTO transport_stop SET name='$stopname', detail='$stopname';");
				$stid=mysql_insert_id();
				//trigger_error($busname.' : '.$direction.' : '.$stopname.' : '.$stoptime.' : '.$stopcode);
				}

			if($direction=='AM'){
				$direction='I';
				$seqno=$stopcode;
				}
			elseif($direction=='PM'){
				$direction='O';
				$seqno=$stopcode-50;
				}

			$bus=(array)get_bus(-1,$busname,$direction,'%');
			if(isset($bus['id'])){
				if(!array_key_exists($rtid,$routes)){$routes[$rtid]=array();}
				$rtid=$bus['route_id'];
				$routes[$rtid][$seqno]=array('id'=>$stid,'time'=>$stoptime);
				mysql_query("INSERT INTO transport_rtidstid SET route_id='$rtid', 
							stop_id='$stid', sequence='$seqno';");
				}
			}


		/* Get the times right. */
		foreach($routes as $rtid => $stops){
			krsort($stops,SORT_NUMERIC);
			$seqnos=array_keys($stops);
			$i=0;
			foreach($seqnos as $i => $seqno){
				$stid=$stops[$seqno]['id'];
				$stoptime=$stops[$seqno]['time'];
				if($seqno>1){
					$prev_seqno=$seqnos[$i+1];
					$prev_stoptime=$stops[$prev_seqno]['time'];
					list($dephour,$depmin,$junk)=explode(':',$prev_stoptime);
					list($hour,$min,$junk)=explode(':',$stoptime);
					$traveltime=round((mktime($hour,$min)-mktime($dephour,$depmin))/60);
					mysql_query("UPDATE transport_rtidstid SET traveltime='$traveltime'
								WHERE route_id='$rtid' AND stop_id='$stid';");
					}
				}
			}



		/* Now read each student row.*/
		$inscore=0;
		foreach($inrows as $row){
			$sid='';
			if($firstcol=='enrolno' and $row[0]!=''){
				$d_student=mysql_query("SELECT student_id FROM info WHERE formerupn='$row[0]';");
				if(mysql_num_rows($d_student)>0){$sid=mysql_result($d_student,0);}
				}
			elseif($firstcol=='sid'){
				$sid=$row[0];
				}
			if($sid!=''){

				$busname=trim($row[$colstart-1]);
				$direction=trim($row[$colstart+1]);
				$stopname=clean_text($row[$colstart]);
				if($direction=='AM'){$direction='I';}
				elseif($direction=='PM'){$direction='O';}
				$bus=(array)get_bus(-1,$busname,$direction,'%');
				if(isset($bus['id'])){
					$rtid=$bus['route_id'];
					$d_s=mysql_query("SELECT transport_stop.id, name FROM transport_stop 
									JOIN transport_rtidstid ON transport_stop.id=transport_rtidstid.stop_id  
									WHERE transport_rtidstid.route_id='$rtid' AND name='$stopname';");
					if(mysql_num_rows($d_s)>0){
						$stopid=mysql_result($d_s,0);
						add_journey_booking($sid,$bus['id'],$stopid,$date='2012-03-01','every');
						//trigger_error(mysql_num_rows($d_s).' : '.$rtid.' : '.$stopid.' : '.$stopname,E_USER_WARNING);
						$inscore++;
						}
					}
				}


			}
		$result[]='Entered '.$inscore.' journeys students.';
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
