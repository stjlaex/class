<?php 
/**									scripts/list_template.php
 *
 */

if(!isset($required)){$required='yes';}
$showtemplates=(array)list_directory_files('../templates','xsl');
if(!isset($seltemplate)){$seltemplate='';}
?>
  <label for="template"><?php print_string('template');?></label>
  <select name="template" id="template" size="1" tabindex="<?php print $tab++;?>" 
  <?php if($required=='yes'){ print ' class="required" ';} ?>
	>
   	<option value=""></option>
<?php
	foreach($showtemplates as $key => $templatename){
		if((isset($listfilter) and strpos($templatename,$listfilter)!==false) or !isset($listfilter)){
			print '<option ';
			if($seltemplate==$templatename){print 'selected="selected"';}
			print	' value="'.$templatename.'">'.$templatename.'</option>';
			}
		}
?>
  </select>

 





















