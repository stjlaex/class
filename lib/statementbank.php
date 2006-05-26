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
			$Statements=array();
			while($statement=mysql_fetch_array($d_stat,MYSQL_ASSOC)){
				$Statement=array();
				$Statement['Value']=$statement['statement_text'];
				$Statement['Counter']=$statement['counter'];
				$Statement['Author']=$statement['author'];
				$Statements[]=$Statement;
				}
			$StatementBank['Area']["$areaid"]['Statements']=$Statements;
			}
		}
	return $StatementBank;
	}

function addStatement($new,$dbstat=''){
	$todate=date('Y').'-'.date('n').'-'.date('j');
	if($dbstat==''){$dbstat=connect_statementbank();}
	if($dbstat!=''){
		$crid=$new['crid'];
		$bid=$new['bid'];
		$pid=$new['pid'];
		$area=$new['area'];
		$subarea=$new['subarea'];
		$statement=$new['statement'];
		$ability=$new['ability'];

		if($new['pid']==''){$pid='%';}else{$pid=$new['pid'];}
		if($new['stage']==''){$stage='%';}else{$stage=$new['stage'];}

		if(mysql_query("INSERT INTO statement (author,
					   	entrydate, statement_text, rating_fraction
					) VALUES ('ClaSS', '$todate', '$statement','$ability');")){
			$stid=mysql_insert_id();

			$result=='yes';

			$d_area=mysql_query("SELECT id FROM area WHERE name='$area';");
			if(mysql_num_rows($d_area)>0){$areaid=mysql_result($d_area,0);}
			else{
				mysql_query("INSERT INTO area (name) VALUES ('$area');");
				$areaid=mysql_insert_id();
				}

			$d_grouping=mysql_query("SELECT id, rating_name FROM grouping WHERE
						course_id='$crid' AND subject_id='$bid' AND area_id='$areaid'
						AND component_id LIKE '$pid' AND stage LIKE '$stage';");
			if(mysql_num_rows($d_grouping)>0){$grid=mysql_result($d_grouping,0);}
			else{
				/*everyhting currently only gets a default fivegrade rating_name!!!!*/
				mysql_query("INSERT INTO grouping (area_id,
					   	subarea_id, course_id, subject_id,
						component_id, stage, rating_name
					) VALUES ('$areaid', '0', '$crid','$bid','$pid','$stage','fivegrade');");
				$grid=mysql_insert_id();
				}

			mysql_query("INSERT INTO gridstid (grouping_id, statement_id
					) VALUES ('$grid', '$stid');");
			}
		}
	return $result;
	}

function personaliseStatement($Statement,$Student){
	$text=$Statement['Value'];
	if($Student['Gender']['value']=='M'){
		$possessive='his';
		$pronoun='he';
		$objectpronoun='him';
		}
	else{
		$possessive='her';
		$pronoun='she';
		$objectpronoun='her';
		}
	if($Student['PreferredForename']!=''){$forename=$Student['PreferredForename'];}
	else{$forename=$Student['Forename'];}
   	$text=str_replace('~',$possessive,$text);
	$text=str_replace('^',$pronoun,$text);
	$text=str_replace('*',$objectpronoun,$text);
	$text=ucfirst($text);
	$text=str_replace('#',$forename,$text);
	$Statement['Value']=$text;
	return $Statement;
	}
?>