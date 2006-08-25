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
        $d_forms=mysql_query("SELECT id, name FROM form ORDER BY yeargroup_id, id");
        while($form=mysql_fetch_array($d_forms,MYSQL_ASSOC)) {
			print '<option value="'.$form['id'].'" ';
			if(isset($selfid)){if($selfid==$fids[0]){print ' selected="selected" ';}}
			print ' >'.$form['name'].'</option>';
   			}
?>
	</select>
<?php  unset($required); unset($selfid);?>