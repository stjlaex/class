<?php
/**										scripts/list_resultqualifier.php
 *
 *$multi>1 returns resultqs[] or $multi=1 returns resultq (default multi=1)
 *set $required='yes' to make required (default=no)
 *first call returns resultq, second call returns resultq1
 */

	if($rcrid!='' and $r>-1){$selcrid=$rcrid;}
	elseif($crid!=''){$selcrid=$crid;}
	else{$selcrid='%';}

	if($rbid!='' and $r>-1){$selbid=$rbid;}
	elseif($bid!=''){$selbid=$bid;}
	else{$selbid='%';}

	if(!isset($required)){$required='no';}
	if(!isset($multi)){$multi='1';}
	if(!isset($iresultq)){$iresultq='';}else{$iresultq++;}

	$d_categorydef=mysql_query("SELECT id, name, subtype FROM categorydef WHERE
				(subject_id LIKE '$selbid' OR subject_id='%') AND (course_id
				LIKE '$selcrid' OR course_id='%') AND type='rsq' ORDER BY name");
?>
  <label for="Resultqualifier">
	<?php print_string('resultqualifier',$book);?>
  </label>
  <select type="text" id="Resultqualifier"
	<?php if($required=='yes'){ print ' class="required" ';} ?>
		size="<?php print $multi;?>"
		<?php if($multi>1){print ' name="resultqs'.$iresultq.'[]" multiple="multiple"';}
				else{print ' name="resultq'.$iresultq.'"';}?> >
	<option value=""></option>
<?php
		while($resultqualifier=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
?>
	<option value="<?php print $resultqualifier['subtype'];?>">
	  <?php print $resultqualifier['name'].' ('.$resultqualifier['subtype'].')';?>
	</option>
<?php
				}
?>
  </select>
<?php
unset($required);
unset($multi);
?>