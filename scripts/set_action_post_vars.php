<?php
/**								scripts/set_action_post_vars.php
 *
 * For any page variables which need to be posted as part of an action
 * pages redirect.
 * The vars are listed in array $action_post_vars and can include
 * numericaly indexed arrays.
 */
while(list($index,$varname)=each($action_post_vars)){
	if(isset($$varname)){
		if(is_array($$varname)){
			reset($$varname);
			while(list($index,$value)=each($$varname)){
?>
	 	<input type="hidden" name="<?php print $varname;?>[]" value="<?php print $value;?>">
<?php
				 }
			}
		else{
			if($book=='entrybook'){trigger_error($varname.': '.$$varname,E_USER_WARNING);}
?>
		<input type="hidden" name="<?php print $varname;?>" value="<?php print $$varname;?>">
<?php
			}
		}
	}
?>
