<?php
/*										scripts/list_assessment.php

filters by rcrid if set, shows all if not
$multi>1 returns eids[] or $multi=1 returns eid (default=10)
set $required to 'no' to make not required (default=yes)
first call returns eid, second call returns eid1
*/

if(isset($rcrid)){$crid=$rcrid;}
else{$crid='%';}
if(!isset($required)){$required='yes';}
if(!isset($multi)){$multi='10';}
if(!isset($ieid)){$ieid='';}else{$ieid++;}
?>
	<label for="Assessments"><?php print_string('assessment');?></label>
<?php
		$d_assessment=mysql_query("SELECT id, description, year,
		stage, course_id FROM assessment
			   WHERE (course_id LIKE '$crid' or course_id='%') ORDER
			   BY year DESC, id DESC");
?>
	<select style="width:20em;" id="Assessments"
	<?php if($required=='yes'){ print ' class="required" ';} ?>
	size="<?php print $multi;?>"
	<?php if($multi>1){print ' name="eids'.$ieid.'[]" multiple="multiple"';}
				else{print ' name="eid'.$ieid.'"';}?> 
	>
    <option value=""></option>
<?php
   		while ($assessment=mysql_fetch_array($d_assessment,MYSQL_ASSOC)){
?>
		<option value="<?php print $assessment['id'];?>">
				<?php print $assessment['course_id'].':'.$assessment['stage'].':'.$assessment['description'].' ('.$assessment['year'].')';?>
		</option>
<?php
				}
?>
	</select>
<?php
unset($required);
unset($multi);
?>