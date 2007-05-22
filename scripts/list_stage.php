<?php
/**										list_stage.php
 * 
 * should only be called when working with a respons has been checked 
 */

	if($r>-1){
		if($rcrid=='%'){
			$stages=array();
			$d_cridbid=mysql_query("SELECT DISTINCT course_id FROM cridbid WHERE
						subject_id='$rbid' ORDER BY course_id"); 
			while($course=mysql_fetch_array($d_cridbid,MYSQL_ASSOC)){
				$extrastages=array();
				$extrastages=(array)list_course_stages($course['course_id']);
				$stages=array_merge($stages,$extrastages);
				}
			}
		else{
			$stages=(array)list_course_stages($rcrid);
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


