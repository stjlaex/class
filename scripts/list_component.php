<?php
/**					scripts/list_component.php
 */

if(!isset($listname)){$listname='pid';}
if(!isset($listlabel)){$listlabel='subjectcomponent';}
include('scripts/set_list_variables.php');
$d_subject=mysql_query("SELECT component.id, subject.name FROM
			  subject JOIN component ON component.id=subject.id WHERE 
			component.subject_id='$bid' AND 
			component.course_id='$crid' ORDER BY subject.name");
list_select_db($d_subject,$listoptions,$book);
mysql_free_result($d_subject);
unset($listoptions);
?>
