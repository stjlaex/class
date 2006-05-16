<?php
/*								grade_pairs.php
	Expects $grading_name
	Optional
	Returns $grading_name

*/

	$d_grading=mysql_query("SELECT grades FROM grading WHERE name='$grading_name'");
	$grading=mysql_result($d_grading,0);
/*	$grading will contain a list of 'grade:level' pairs, space seperated, the last must be an empty pair ':' to terminate*/	
	$$grading_name =explode (" ", $grading);
	



?>
