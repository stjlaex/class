<?php
/**						list_rating.php
 *
 * This lists the ratings used by a categorydef of whatever type,
 * identified by $cattype. Defaults to cattype=con for general comments.
 *
 */

if(!isset($listname)){$listname='ratvalue';}
if(!isset($listid)){$listid='rating';}
if(!isset($cattype)){$cattype='con';}
if($cattype=='con'){$listlabel='type';}else{$listlabel='';}
include('scripts/set_list_vars.php');
/* ALL categories of this type must use the same rating_name!!!
 * If there's no ratings then no select displayed.
 */
$d_ratname=mysql_query("SELECT DISTINCT rating_name FROM categorydef WHERE type='$cattype'");
$rating_name=mysql_result($d_ratname,0);
$d_rating=mysql_query("SELECT longdescriptor AS name, value AS id FROM rating WHERE
	        name='$rating_name' ORDER BY value");
if(mysql_num_rows($d_rating)>0){
?>
<div class="center">
	<?php list_select_db($d_rating,$listoptions,$book);?>
</div>
<?php
	mysql_free_result($d_rating);
	unset($listoptions);
	}
?>
