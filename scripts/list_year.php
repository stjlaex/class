<?php 
/**						list_year.php
 *
 * TODO: raname as list_yeargroup
 */

if(!isset($listname)){$listname='newyid';}
if(!isset($listlabel)){$listlabel='yeargroup';}
include('scripts/set_list_vars.php');
$d_yeargroup=mysql_query("SELECT id, name FROM yeargroup ORDER BY sequence");
list_select_db($d_yeargroup,$listoptions,$book);
mysql_free_result($d_yeargroup);
unset($listoptions);
?>
