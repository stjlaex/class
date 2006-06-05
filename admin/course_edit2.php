<?php 
/** 											course_edit2.php
 *	Generates the rows in the table:classes for selected yeargroups/courses
 */

$host='admin.php';
$current='curriculum_matrix.php';
$action='';
$choice='curriculum_matrix.php';

$sub=$_POST['sub'];

if($sub=='Submit' or $sub=='Enter'){
	if(mysql_query("DELETE FROM classes")){}
		else{$error[]='Failed to delete old classes!'; $error[]=mysql_error();}

	$d_course = mysql_query("SELECT * FROM course ORDER BY id");
	while($course = mysql_fetch_array($d_course,MYSQL_ASSOC)){
   		$crid=$course{'id'};
		$d_bid = mysql_query("SELECT * FROM subject ORDER BY id");
		$num=mysql_num_rows($d_bid);
		for ($c2=0; $c2<$num; $c2++){
			$bid = mysql_result($d_bid,$c2);
			if (isset($_POST{$crid.$bid})){
				/*each checked course/subject combination will get rows in cridbid*/
				mysql_query("INSERT INTO cridbid SET course_id='$crid', subject_id='$bid'");
				}
			else {
				mysql_query("DELETE FROM cridbid WHERE 
					course_id='$crid' AND subject_id='$bid' LIMIT 1");
				}
			}
		}

	$d_course = mysql_query("SELECT * FROM course ORDER BY id");
	while($course = mysql_fetch_array($d_course,MYSQL_ASSOC)){
   		$crid=$course{'id'};
		$many=$course{'many'};
		$naming=$course{'naming'};	
		$generate=$course{'generate'};
	
		if (isset($_POST{"$crid"})){
			$in=$_POST{"$crid"};
			for ($c=0; $c<sizeof($in); $c++){
				/*each checked course/year combination will get rows in
				 *classes for all subjects which are part of that course*/
				$yid=$in[$c];
				$d_cridbid = mysql_query("SELECT subject_id FROM
								cridbid WHERE course_id='$crid'");
				$num=mysql_num_rows($d_cridbid);
				for ($c2=0; $c2<$num; $c2++){
					$bid = mysql_result($d_cridbid,$c2);
					if(mysql_query("INSERT INTO classes (many,
								generate, naming, yeargroup_id, subject_id, course_id) VALUES
								('$many', '$generate', '$naming', '$yid', '$bid',
								'$crid')")){$result[]="Updated courses.";}
					else{$error[]='Failed to insert new classes!';	
								$error[]=mysql_error();}
					}
				}	
			}
		}
	}
else{
	$current='';
	$result[]=get_string('cancel');
	}
include('scripts/results.php');
include('scripts/redirect.php');
	
?>
















































