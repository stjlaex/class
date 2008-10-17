<?php
/**						list_category.php
 *
 */

if(!isset($listname)){$listname='catid';}
if(!isset($cattype)){$cattype='con';}
include('scripts/set_list_vars.php');
if(!isset($catsecid)){
   	$d_catdef=mysql_query("SELECT id, name FROM categorydef WHERE
								  type='$cattype' ORDER BY rating, name;");
   	}
else{
   /*filter by secid if set but still include secid=0 for backward compatibility*/
   	$d_catdef=mysql_query("SELECT id, name FROM categorydef WHERE
			   type='$cattype' AND (section_id='$catsecid' OR section_id='0') 
						ORDER BY rating, name;");
   	}
list_select_db($d_catdef,$listoptions,$book);
mysql_free_result($d_catdef);
$required=$listoptions['required'];
unset($listoptions);

include('scripts/list_rating.php');
?>
