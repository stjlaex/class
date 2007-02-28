<?php 
/**						list_year.php
 */
 
if(!isset($listname)){$listname='newyid';}
if(!isset($listlabel)){$listlabel='yeargroup';}
include('scripts/set_list_variables.php');
$d_yeargroup=mysql_query("SELECT id, name  
				FROM yeargroup ORDER BY sequence");
list_select_db($d_yeargroup,$listoptions,$book);
mysql_free_result($d_yeargroup);
unset($listoptions);
?>
