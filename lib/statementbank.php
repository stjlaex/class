<?php
/**							statementbank.php
 *
 */

function connect_statementbank(){
	global $CFG;
	$dbstat='';
	if($CFG->statementbank_db!=''){
		$dbstat=db_connect($CFG->statementbank_db);
		}
	return $dbstat;
	}

function fetchStatementBank($crid,$bid,$pid,$stage,$dbstat=''){
	$StatementBank=array();
	if($dbstat==''){$dbstat=connect_statementbank();}
	if($dbstat!=''){
		if($pid==''){$pid='%';}
		if($stage==''){$stage='%';}
		$d_area=mysql_query("SELECT DISTINCT area.id, area.name FROM area
				JOIN grouping ON area.id=grouping.area_id WHERE
	   			grouping.course_id='$crid' AND grouping.subject_id='$bid' AND 
	   			grouping.component_id LIKE '$pid' AND grouping.stage LIKE '$stage';");
		while($area=mysql_fetch_array($d_area,MYSQL_ASSOC)){
			$areaid=$area['id'];
			$StatementBank['Area']["$areaid"]['Name']=$area['name'];

			$d_grouping=mysql_query("SELECT id, rating_name FROM grouping WHERE
						course_id='$crid' AND subject_id='$bid' AND area_id='$areaid'
						AND component_id LIKE '$pid' AND stage LIKE '$stage';");
			$grid=mysql_result($d_grouping,0);
			$ratingname=mysql_result($d_grouping,1);

			$d_stat=mysql_query("SELECT * FROM statement JOIN gridstid 
				ON statement.id=gridstid.statement_id WHERE gridstid.grouping_id='$grid';");
			$Statement=array();
			while($statement=mysql_fetch_array($d_stat,MYSQL_ASSOC)){
				reset($Statement);
				$Statement['Value']=$statement['statement_text'];
				$Statement['Counter']=$statement['counter'];
				$Statement['Author']=$statement['author'];
				$StatementBank['Area']["$areaid"]['Statement'][]=$Statement;
				}
			}
		}
	return $StatementBank;
	}
?>