<?php
/**						list_category.php
 *
 */

if(!isset($listname)){$listname='catid';}
if(!isset($cattype)){$cattype='con';}
include('scripts/set_list_variables.php');
$d_catdef=mysql_query("SELECT id AS value, name AS description FROM categorydef WHERE
										type='$cattype' ORDER BY rating, name");
list_select($listoptions,$d_catdef,$book);
mysql_free_result($d_catdef);
$required=$listoptions['required'];
unset($listoptions);

/*ALL* categories of this type must use the same rating_name!!!*/
/*if there's no ratings then no select displayed*/
$d_ratname=mysql_query("SELECT DISTINCT rating_name FROM categorydef WHERE
										type='$cattype'");
$rating_name=mysql_result($d_ratname,0);
$d_rating=mysql_query("SELECT longdescriptor AS description, value FROM rating WHERE
	        name='$rating_name' ORDER BY value");
if(mysql_num_rows($d_rating)>0){
	$listname='ratvalue';
	$listid='rating';
	$listlabel='';
	include('scripts/set_list_variables.php');
?>
  <div class="left">
	<?php list_select($listoptions,$d_rating,$book);?>
  </div>
<?php
	mysql_free_result($d_rating);
	unset($listoptions);
	}
?>