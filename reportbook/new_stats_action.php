<?php
/*							    new_stats_action.php
*/

$action='new_stats.php';

$rcrid=$respons[$r]{'course_id'};

$description=$_POST{'description'};
include('scripts/sub_action.php');

if($sub=='Import'){

	$importfile=$_POST{'importfile'};
	$fname=$_FILES{'importfile'}{'tmp_name'};
	$fuser=$_FILES{'importfile'}{'name'};
	$ferror=$_FILES{'importfile'}{'error'};
	$ftype=$_FILES{'importfile'}{'type'};

  if(mysql_query("INSERT INTO stats (description, course_id) 
   			VALUES ('$description', '$rcrid');"))
   			{$result[]="Created new statistics.";}
   	else {$error[]="Assessment may already exist!".mysql_error();}
   	$statid=mysql_insert_id();

	if($fname!=''){
	   	$result[]="Loading file ".$importfile;
		include('scripts/file_import_csv.php');
		if(sizeof($inrows>0)){
			while(list($index,$d)=each($inrows)){
				$bid=$d[0];
				$pid=$d[1];
				$m=$d[2];
				$c=$d[3];
				$error=$d[4];
				$sd=$d[5];
				$weight=$d[6];
				$d_subject=mysql_query("SELECT id FROM subject WHERE id='$bid';");
				if(mysql_num_rows($d_subject)!=0){
					if(mysql_query("INSERT INTO statvalues (stats_id, subject_id, component_id,
					m, c,error, sd, weight) VALUES ('$statid', '$bid', '$pid',
					'$m', '$c', '$error', '$sd', '$weight');"))	
					{$result[]='Inserted statistics for '.$bid;}
					}
				}
			}
		}

	else{
	   	$eid0=$_POST{'eid'};
	   	$eid1=$_POST{'eid1'};
	   	}
	}

include("scripts/results.php");
include("scripts/redirect.php");

?>

















