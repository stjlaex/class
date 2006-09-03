<?php 
/**					list_year.php
 *   returns $newyid
 */
 
	if(!isset($selyid)){$selyid='';}
	if(isset($yid)){$selyid=$yid;}
	if(isset($newyid)){$selyid=$newyid;}
	if(!isset($required)){$required='no';}
	if(!isset($onchange)){$onchange='no';}
?>
	<label for="Yeargroup"><?php print_string('yeargroup');?></label>
	<select id="Yeargroup" name="newyid"
			<?php if($onchange=='yes'){ print ' onChange="document.entrybookchoice.submit();" ';} ?> >
			<?php if($required=='yes'){ print ' class="required" ';} ?> >
    <option value=""></option>
<?php
		$d_yeargroup=mysql_query("SELECT id, name  FROM yeargroup ORDER BY sequence");
    	while($year=mysql_fetch_array($d_yeargroup,MYSQL_ASSOC)){
			print '<option ';
			if(($selyid==$year['id'])){print 'selected="selected"';}
			print	' value="'.$year['id'].'"> '.$year['name'].'</option>';
			}
?>
	</select>
<?php  unset($required); unset($selyid); unset($onchange);?>





















