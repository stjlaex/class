<?php 
/** 									year_end_action2.php
 */

$action='year_end.php';

if($_POST{'sub'}!='Submit'){
	$current='';
 	$result[]='NO action taken.';
	include('scripts/results.php');
	include('scripts/redirect.php');
	}

$years=array();
$d_yeargroup=mysql_query("SELECT id, ncyear,section,name FROM
							yeargroup ORDER BY section, ncyear");
while($years[]=mysql_fetch_array($d_yeargroup,MYSQL_ASSOC)){}


for($c=(sizeof($years)-2);$c>-1;$c--){
	$yid=$years[$c]['id'];
    $nextyid=$_POST[$yid];
	if (mysql_query("UPDATE student SET yeargroup_id='$nextyid', 
				form_id='' WHERE yeargroup_id='$yid';"))
		{$result[]='Promoted year '.$yid.' to '.$nextyid;}
		else {$error[]=mysql_error();}
	}

if(mysql_query("DELETE FROM cidsid")){$result[]='Emptied table cidsid';}
else{$error[]='Failed to empty to table ';};
if(mysql_query("DELETE FROM score")){$result[]='Emptied table score';}
else{$error[]='Failed to empty to table ';};
if(mysql_query("DELETE FROM mark")){$result[]='Emptied table mark';}
else{$error[]='Failed to empty to table ';};
if(mysql_query("DELETE FROM midcid")){$result[]='Emptied table midcid';}
else{$error[]='Failed to empty to table ';};
if(mysql_query("DELETE FROM eidmid")){$result[]='Emptied table eidmid';}
else{$error[]='Failed to empty to table ';};

include('scripts/results.php');
?>
