<?php 
/**									scripts/list_teacher.php
 *	Returns $newtid
 */
 

$newusers=getTeachingStaff();
if(!isset($seltid)){$seltid='';}
?>
 
  <label for="Teachers"><?php print_string('teachers',$book);?></label>
  <select id="Teachers" name="newtid" size="1" style="width:95%;" 
	<?php if($required=='yes'){ print ' class="required" ';} ?> 
	tabindex="<?php print $tab++;?>" 
	>
	<option value="" ></option>
<?php
   	while(list($newtid,$newuser)=each($newusers)){
  		print '<option ';
		if(isset($seltid)){if($seltid==$newtid){print 'selected="selected"';}}
  		print	' value="'.$newtid.'">'.$newtid.' ('.$newuser['surname'].')</option>';
   		}
?>
  </select>
<?php
	unset($seltid);
	unset($newusers);
?>
