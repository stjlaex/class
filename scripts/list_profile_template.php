<?php
/**					scripts/list_profile_template.php
 */

if(!isset($listname)){$listname='template';}
if(!isset($listlabel)){$listlabel='template';}
$d_c=mysql_query("SELECT DISTINCT comment AS id, CONCAT(name,': ',comment) AS name FROM categorydef WHERE
								  type='pro' AND comment!='' ORDER BY course_id;");
include('scripts/set_list_vars.php');
list_select_db($d_c,$listoptions,$book);
mysql_free_result($d_c);
unset($listoptions);
?>
