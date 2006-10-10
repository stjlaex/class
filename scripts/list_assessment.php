<?php
/**										scripts/list_assessment.php
 *
 *$multi>1 returns eids[] or $multi=1 returns eid (default=10)
 *set $required='no' to make not required (default=yes)
 *first call returns eid, second call returns eid1
 */

	if($rcrid!='' and $r>-1){$selcrid=$rcrid;}
	elseif(sizeof($ryids)==1){
		$d_class=mysql_query("SELECT DISTINCT course_id
				FROM class WHERE yeargroup_id='$selyid'");
		$selcrid=mysql_result($d_class,0);
		}
	elseif(sizeof($rfids)==1){
		$selfid=$rfids[0];
		$d_yeargroup=mysql_query("SELECT yeargroup_id FROM form WHERE id='$selfid'");
		$selyid=mysql_result($d_yeargroup,0);
		$d_class=mysql_query("SELECT DISTINCT course_id
				FROM class WHERE yeargroup_id='$selyid'");
		$selcrid=mysql_result($d_class,0);
		}
	else{
		$selcrid='%';
		}
	$d_assessment=mysql_query("SELECT id, description, year,
			   stage, course_id FROM assessment
			   WHERE (course_id LIKE '$selcrid' or course_id='%') ORDER
			   BY year DESC, id DESC");

	if(!isset($required)){$required='yes';}
	if(!isset($multi)){$multi='10';}
	if(!isset($ieid)){$ieid='';}else{$ieid++;}
?>
	<label for="Assessments"><?php print_string('assessment');?></label>
	<select style="width:25em;" id="Assessments" tabindex="<?php print $tab++;?>"
		<?php if($required=='yes'){ print ' class="required" ';} ?>
		size="<?php print $multi;?>"
		<?php if($multi>1){print ' name="eids'.$ieid.'[]" multiple="multiple"';}
				else{print ' name="eid'.$ieid.'"';}?> >
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