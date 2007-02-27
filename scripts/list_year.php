<?php 
/**						list_year.php
 */
 
if(!isset($listname)){$listname='newyid';}
if(!isset($listlabel)){$listlabel='yeargroup';}
include('scripts/set_list_variables.php');
$d_yeargroup=mysql_query("SELECT id AS value, name AS description 
				FROM yeargroup ORDER BY sequence");
list_select($listoptions,$d_yeargroup,$book);
mysql_free_result($d_yeargroup);
unset($listoptions);
?>
