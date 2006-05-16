<?php 
/**					list_year.php
 *   returns $newyid
 */
 
	if(!isset($yid)){$yid='';}
	if(!isset($newyid)){$newyid='';}
	if(!isset($required)){$required='no';}
?>
	<label for="Year Group"><?php print_string('yeargroup');?></label>
	<select id="Year Group" name="newyid"
			<?php if($required=='yes'){ print ' class="required" ';} ?> >	
    <option value=""></option>
<?php
		$d_yids = mysql_query("SELECT id, name  FROM yeargroup ORDER BY id");
    	while($yids = mysql_fetch_array($d_yids,MYSQL_ASSOC)) {
			print '<option ';
			if(($yid==$yids['id'] and $newyid=='') 
				or $newyid==$yids['id']){print 'selected="selected"';}
			print	' value="'.$yids['id'].'"> '.$yids['name'].'</option>';
			}
?>
	</select>
<?php  unset($required);?>





















