<?php
/**						list_category.php
 *
 */
if(!isset($cattype)){$cattype='con';}
if(!isset($multi)){$multi='1';}
if(!isset($icatid)){$icatid='';}
if(!isset($required)){$required='yes';}
?>
	<select id="Category"  id="Category" tabindex="<?php print $tab++;?>"
		<?php if($required=='yes'){ print ' class="required" ';} ?>
		size="<?php print $multi;?>"
		<?php if($multi>1){print ' name="catids'.$icatid++.'[]" multiple="multiple"';}
				else{print ' name="catid'.$icatid++.'"';}?> >
    <option value=""></option>
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
<?php
        $d_rating=mysql_query("SELECT descriptor, longdescriptor, value FROM rating WHERE
	        name='$rating_name' ORDER BY value");
		if(mysql_num_rows($d_rating)>0){
?>
  <div class="left">
	<select id="Rating"  name="ratvalue" 
	  tabindex="<?php print $tab++;?>"
		<?php if($required=='yes'){ print ' class="required" ';} ?>
	  size="1">
    <option value=""></option>
<?php
		   while($rats=mysql_fetch_row($d_rating)){
			   print '<option value="'.$rats[2].'" ';
			   //if($rats[2]=='-1'){print ' selected="selected" ';}
			   print ' >'.$rats[1].'</option>';
			   }
?>
	</select>
  </div>
<?php
			}
unset($required);
?>