<?php
/*					scripts/list_component.php
 */

	$d_subject = mysql_query("SELECT id, name FROM subject
		LEFT JOIN component ON component.subject_id=subject.id WHERE 
		component.subject_id='$bid' AND component.course_id='$crid' ORDER BY name, id");

?>
	<label for="pid" ><?php print_string('subjectcomponent');?></label>
	<select id="pid" class="required"  name="pid" size="1">
	<option value=''><?php print_string('all');?></option>
<?php
        while($pids=mysql_fetch_row($d_subject)){
			print "<option value='".$pids[0]."' ";
			if(isset($pid)){if($pid==$pids[0]){print " selected='selected' ";}}
			print" >".$pids[1]."</option>";
			}
?>
	</select>
    	