<?php
/**										scripts/list_gradescheme.php
 *
 *$multi>1 returns genas[] or $multi=1 returns gena (default multi=1)
 *set $required='no' to make not required (default=yes)
 *first call returns gena, second call returns gena1
 */


	if(!isset($required)){$required='yes';}
	if(!isset($multi)){$multi='1';}
	if(!isset($igena)){$igena='';}else{$igena++;}

	$d_grading=mysql_query("SELECT name, comment FROM grading WHERE ORDER BY name");
?>
  <label for="Gradingscheme"><?php print_string('gradingscheme');?></label>
  <select id="Gradingscheme"  tabindex="<?php print $tab++;?>" 
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