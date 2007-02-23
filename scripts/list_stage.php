<?php
/**										list_stage.php
 *	called within a form, returns stage
 * should only be called when working wit a respons has been checked 
 */

	$stages=array();
	if($r>-1){
		if($rcrid=='%'){
			$d_cridbid=mysql_query("SELECT DISTINCT course_id FROM cridbid WHERE
						subject_id='$rbid' ORDER BY course_id"); 
			while($course=mysql_fetch_array($d_cridbid,MYSQL_ASSOC)){
				$crid=$course['course_id'];
				$d_stage=mysql_query("SELECT DISTINCT stage FROM cohort WHERE
					course_id='$crid' ORDER BY year");
				while($stage=mysql_fetch_array($d_stage,MYSQL_ASSOC)){
					$stages[]=$stage['stage'];	
					}
				}
			}
		else{
			$d_stage=mysql_query("SELECT DISTINCT stage FROM cohort WHERE
				course_id='$rcrid' ORDER BY year");
			while($stage=mysql_fetch_array($d_stage,MYSQL_ASSOC)){
				$stages[]=$stage['stage'];
				}
			}
		}
?>
	<label for="Stage"><?php print_string('stage');?></label>
	<select style="width:12em;" type="text" 
		tabindex="<?php print $tab++;?>" id="Stage" name="stage" class="required">
		<option value="" select="selected"></option>
		<option value="%"><?php print_string('allstages');?></option>
<?php
   	while(list($index,$stage)=each($stages)){
?> 				
		<option value="<?php print $stage; ?>"><?php print $stage; ?></option>
<?php
		}
unset($stages);
?>
	</select>


