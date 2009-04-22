<?php
/**						list_rating_name.php
 *
 * This lists the names of the differnet rating schemes available for use
 * when configuring categories.
 *
 */

if(!isset($listname)){$listname='ratingname';}
if(!isset($listid)){$listid='ratingname';}
if(!isset($listlabel)){$listlabel='ratingname';}
include('scripts/set_list_vars.php');
$d_ratname=mysql_query("SELECT DISTINCT name AS id, name  FROM rating ORDER BY name;");
if(mysql_num_rows($d_ratname)>0){
?>
<div class="center">
	<?php list_select_db($d_ratname,$listoptions,$book);?>
</div>
<?php
	mysql_free_result($d_ratname);
	unset($listoptions);
	}
?>
