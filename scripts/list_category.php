<?php
/**						list_category.php
 *
 */

if(!isset($listname)){$listname='catid';}
if(!isset($cattype)){$cattype='con';}
include('scripts/set_list_vars.php');
$d_catdef=mysql_query("SELECT id, name FROM categorydef WHERE
								  type='$cattype' ORDER BY rating, name");
list_select_db($d_catdef,$listoptions,$book);
mysql_free_result($d_catdef);
$required=$listoptions['required'];
unset($listoptions);


include('scripts/list_rating.php');
?>