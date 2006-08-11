<?php 
/** 			  				define_mark.php
 */

$action='define_mark_action1.php';
$choice='define_mark.php';

three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method='post' action='<?php print $host;?>'> 
	  <fieldset class="left">
		<legend><?php print_string('thetypeofmark',$book);?></legend>
		<table>
		  <tr>
			<td>
			  <label for="Type"><?php print_string('thetypeofmark',$book);?></label>
			  <select class="required" name="type" id="Type" tabindex="1">
				<option value="" selected="selected"></option>
				<option value="value"><?php print_string('numericscore',$book);?></option>
				<option value="percentage"><?php print_string('valueandtotal',$book);?></option>
				<option value="grade"><?php print_string('grade',$book);?></option>
			  </select>
			</td>
		  </tr>
		</table>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('descriptionofnewdefinition',$book);?></legend>
		<label for="Name"><?php print_string('name',$book);?></label>
		<input class="required" type="text" name="name" id="Name" 
		  tabindex="2" maxlength="20"  pattern="alphanumeric" />
		  <label for="Comment"><?php print_string('descriptivecomment',$book);?></label>
		  <input type="text" name="comment" id="Comment" 
			tabindex="3" maxlength="98" pattern="alphanumeric" />
	  </fieldset>

	  <fieldset class="center">
<?php
	include ("classes_to_use.php");
?>
	  </fieldset>

	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $current;?>" />
	  <input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
