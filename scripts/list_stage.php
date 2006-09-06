<?php
/**										list_stage.php
 *	called within a form, returns stage
 */

$d_stage=mysql_query("SELECT DISTINCT stage FROM cohort WHERE
				course_id='$rcrid' ORDER BY year");
?>
	<label for="Stage"><?php print_string('stage');?></label>
	<select style="width:12em;" type="text" 
		tabindex="<?php print $tab++;?>" id="Stage" name="stage" class="required">
		<option value="" select="selected"></option>
		<option value="%"><?php print_string('allstages');?></option>
<?php
   	while($stage=mysql_fetch_array($d_stage,MYSQL_ASSOC)){
?> 				
		<option value="<?php print $stage['stage']; ?>"><?php print $stage['stage']; ?></option>
<?php
		}
?>
	</select>

