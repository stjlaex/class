<?php 
/**									scripts/list_roles.php
 *
 */

$roles=$CFG->roles;

if(!isset($required)){$required='yes';}
?>
  <label for="Role"><?php print_string('role');?></label>
  <select name="role" id="Role" size="1" tabindex="<?php print $tab++;?>" 
	<?php if($required=='yes'){ print ' class="required" ';} ?>
	>
   	<option value=""></option>
<?php
	foreach($roles as $index => $role){
		if($role!='admin' or ($role=='admin' and $_SESSION['role']=='admin')){
			print '<option ';
			if(isset($selrole)){if($selrole==$role){print 'selected="selected"';}}
			print	' value="'.$role.'">'.get_string($role).'</option>';
			}
		}
?>
  </select>
