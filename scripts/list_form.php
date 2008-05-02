<?php
/**								list_form.php
 *
 *	returns $newfid
 */
	if(!isset($selfid)){$selfid='';}
	if(isset($fid)){$selfid=$fid;}
	if(isset($newfid)){$selfid=$newfid;}
	if(!isset($required)){$required='no';}
	if(!isset($onsidechange)){$onsidechange='no';}
	$forms=list_formgroups();
?>
  <label for="Form Group"><?php print_string('formgroup');?></label>
  <select type="text" id="Form Group" name="newfid"
		<?php if(isset($tab)){print ' tabindex="'.$tab++.'" ';}?> 
		style="<?php if(isset($liststyle)){print $liststyle;}?>"
	<?php if($onsidechange=='yes'){
				print 'onchange="document.'.$book.'choice.submit();"'.
				' onkeypress="seleryCheckKey(event,document.'.$book.'choice);"';
				}
	?>
		<?php if($required=='yes'){ print ' class="required" ';} ?> >
	<option value=""></option>
<?php
        while(list($index,$form)=each($forms)){
			print '<option value="'.$form['id'].'" ';
			if($selfid==$form['id']){print ' selected="selected" ';}
			print ' >'.$form['name'].'</option>';
   			}
?>
  </select>
<?php  unset($required); unset($selfid); unset($onsidechange);?>