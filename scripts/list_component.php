<?php
/**					scripts/list_component.php
 */

if(!isset($listname)){$listname='pid';}
if(!isset($listlabel)){$listlabel='subjectcomponent';}
include('scripts/set_list_variables.php');
$d_subject=mysql_query("SELECT id AS value, name AS description FROM subject
		LEFT JOIN component ON component.subject_id=subject.id WHERE 
		component.subject_id='$bid' AND component.course_id='$crid' ORDER BY name, id");
list_select($listoptions,$d_subject,$book);
mysql_free_result($d_subject);
unset($listoptions);
?>
