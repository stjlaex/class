<?php 
/**													fix8.php
 * temporary quick-fix to populate yeargroup and formgroup communities
 */

$action='';

$d_community=mysql_query("SELECT id, name FROM community WHERE type='year'");
while($community=mysql_fetch_array($d_community,MYSQL_ASSOC)){
	$comid=$community['id'];
	$yid=$community['name'];
	$d_student=mysql_query("SELECT id FROM student WHERE form_id='$fid'");
	while($student=mysql_fetch_array($d_student,MYSQL_ASSOC)){
		$sid=$student['id'];
		mysql_query("INSERT INTO comidsid (community_id, student_id) VALUES
				('$comid','$sid')");
		}
	}
?>