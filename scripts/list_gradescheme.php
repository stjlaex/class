<?php
/**										scripts/list_gradescheme.php
 *
 *$multi>1 returns genas[] or $multi=1 returns gena (default multi=1)
 *set $required='no' to make not required (default=yes)
 *first call returns gena, second call returns gena1
 */

	if($rcrid!='' and $r>-1){$selcrid=$rcrid;}
	elseif($crid!=''){$selcrid=$crid;}
	else{$selcrid='%';}

	if($rbid!='' and $r>-1){$selbid=$rbid;}
	elseif($bid!=''){$selbid=$bid;}
	else{$selbid='%';}

	if(!isset($required)){$required='yes';}
	if(!isset($multi)){$multi='1';}
	if(!isset($igena)){$igena='';}else{$igena++;}

	$d_grading=mysql_query("SELECT name, comment FROM grading WHERE
				(subject_id LIKE '$selbid' OR subject_id='%') AND (course_id
				LIKE '$selcrid' OR course_id='%') ORDER BY name");
?>
  <label for="Grade scheme" ><?php print_string('gradescheme');?></label>
  <select class="required" id="Grade scheme"
		<?php if($required=='yes'){ print ' class="required" ';} ?>
		size="<?php print $multi;?>"
		<?php if($multi>1){print ' name="genas'.$igena.'[]" multiple="multiple"';}
				else{print ' name="gena'.$igena.'"';}?> >
	<option value=""></option>
<?php
		while($grading=mysql_fetch_array($d_grading,MYSQL_ASSOC)){
?>
	<option value="<?php print $grading['name'];?>">
	  <?php print $grading['name'];?>
	</option>
<?php
				}
?>
  </select>
<?php
unset($required);
unset($multi);
?>