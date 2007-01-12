<?php
/*						list_componentstatus.php
 */
?>
    <label for="Componentstatus"><?php print_string('usesubjectcomponents');?></label>
	<select class="required" type="text" id="Componentstatus" 
		tabindex="<?php print $tab++;?>" name="componentstatus" size="1">
		<option value="" select="selected"></option>
<?php
		$enum=getEnumArray('component');
		while(list($inval,$description)=each($enum)){	
				print '<option ';
				print ' value="'.$inval.'">'.get_string($description,'reportbook').'</option>';
				}
?>
	</select>

