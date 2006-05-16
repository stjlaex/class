<?php 
/* 													subject_edit_action.php

*/
	$current="class_matrix.php";
	$choice="class_matrix.php";
	$action="";
	$host="admin.php";


$submit=$_POST{'submit'};
$yid=$_POST{'yid'};
$bid=$_POST{'bid'};
$crid=$_POST{'crid'};
$many=$_POST{'many'};
$generate=$_POST{'generate'};


if($many==0 OR $generate=='none'){$many=''; $generate='';}
	
	include("scripts/redirect.php");

	
if ($submit=="Enter"):

	$d_classes=mysql_query("SELECT * FROM classes WHERE
						subject_id='$bid' AND yeargroup_id='$yid' AND course_id='$crid'");
  	if(mysql_fetch_array($d_classes,MYSQL_ASSOC)){
		if(mysql_query("UPDATE classes SET many='$many',
		generate='$generate' WHERE yeargroup_id='$yid' AND
		subject_id='$bid' AND course_id='$crid'"))
   		{print "Successfully updated.";	}
			else{print "Failed to enter new values!<br>";	
					$error=mysql_error(); print $error."<br>";}
		}
	else {
		if(mysql_query("INSERT INTO classes (many, generate,
				yeargroup_id, course_id, subject_id) VALUES ('$many',
				'$generate', '$yid', '$crid', '$bid')"))
   		{print "Successfully updated.";	}
			else{print "Failed to enter new values!<br>";	
					$error=mysql_error(); print $error."<br>";}
		}

endif;

		
?>































