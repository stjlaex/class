<?php
/**										scripts/list_method.php
 *
 *$multi>1 returns methods[] or $multi=1 returns method (default multi=1)
 *set $required='yes' to make required (default=no)
 *first call returns method, second call returns method1
 */

	if($rcrid!='' and $r>-1){$selcrid=$rcrid;}
	elseif($crid!=''){$selcrid=$crid;}
	else{$selcrid='%';}

	if($rbid!='' and $r>-1){$selbid=$rbid;}
	elseif($bid!=''){$selbid=$bid;}
	else{$selbid='%';}

	if(!isset($required)){$required='no';}
	if(!isset($multi)){$multi='1';}
	if(!isset($imethod)){$imethod='';}else{$imethod++;}

	$d_categorydef=mysql_query("SELECT id, name, subtype FROM categorydef WHERE
				(subject_id LIKE '$selbid' OR subject_id='%') AND (course_id
				LIKE '$selcrid' OR course_id='%') AND type='met' ORDER BY name");
?>
  <label for="Method">
	<?php print_string('method',$book);?>
  </label>
  <select type="text" id="Method"
	<?php if($required=='yes'){ print ' class="required" ';} ?>
		size="<?php print $multi;?>"
		<?php if($multi>1){print ' name="methods'.$imethod.'[]" multiple="multiple"';}
				else{print ' name="method'.$imethod.'"';}?> >
	<option value=""></option>
<?php
		while($method=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
?>
	<option value="<?php print $method['subtype'];?>">
	  <?php print $method['name'].' ('.$method['subtype'].')';?>
	</option>
<?php
				}
?>
  </select>
<?php
unset($required);
unset($multi);
?>