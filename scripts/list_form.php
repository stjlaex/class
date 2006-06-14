<?php
/**								list_form.php
 *	returns $newfid
 */
	if(!isset($required)){$required='no';}
	if(isset($fid)){$selfid=$fid;}
?>
	<label for="Form Group"><?php print_string('formgroup');?></label>
	<select type="text" id="Form Group" name="newfid"
		<?php if($required=='yes'){ print ' class="required" ';} ?> >
	<option value=""></option>
<?php
        $d_fids=mysql_query("SELECT id FROM form ORDER BY yeargroup_id, id");
        while($fids=mysql_fetch_row($d_fids)) {
			print '<option value="'.$fids[0].'" ';
			if(isset($selfid)){if($selfid==$fids[0]){print ' selected="selected" ';}}
			print ' >Form '.$fids[0].'</option>';
   			}
?>
	</select>
<?php  unset($required); unset($selfid);?>