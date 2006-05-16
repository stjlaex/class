<?php
/**						list_category.php
 *
 */
if(!isset($cattype)){$cattype='con';}
?>
	<select id="Category" class="required" multiple="multiple" 
		name="catid[]" size="4">
<?php
        $d_categorydef=mysql_query("SELECT id, name, rating_name FROM categorydef WHERE
										type='$cattype' ORDER BY name, id");
        while($catids=mysql_fetch_row($d_categorydef)){
			print '<option value="'.$catids[0].'" ';
			if(isset($catid)){if($catid==$catids[0]){print ' selected="selected" ';}}
			print ' >'.$catids[1].'</option>';
			/*ALL* categories of this type must use the same rating_name!!!*/
			$rating_name=$catids[2];
			}
?>
	</select>
<div class="left">
	<select id="Rating" class="required"  
		name="ratvalue" size="1">
<?php
        $d_rating=mysql_query("SELECT descriptor, longdescriptor, value FROM rating WHERE
	        name='$rating_name' ORDER BY value");
        while($rats=mysql_fetch_row($d_rating)){
			print '<option value="'.$rats[2].'" ';
			if($rats[2]=='-1'){print ' selected="selected" ';}
			print ' >'.$rats[1].'</option>';
			}
?>
	</select>
</div>