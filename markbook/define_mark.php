<?php 
/** 			  				define_mark.php
 */

$host="markbook.php";
$current="define_mark.php";
$action="define_mark_action1.php";
$choice="define_mark.php";

three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method='post' action='<?php print $host;?>'> 
	  <fieldset class="lefttop">
		<legend><?php print_string('',$book);?>Choose a Mark-Type</legend>
		<table>
		  <tr>
			<td>
			  <label for="Type"><?php print_string('',$book);?></label>
			  <select class="required" name="type" id="Type" tabindex="1">
				<option value="" selected="selected"></option>
				<option value="comment"><?php print_string('',$book);?>text comment</option>
				<option value="value"><?php print_string('',$book);?>numeric score</option>
				<option value="percentage"><?php print_string('',$book);?>value and total</option>
				<option value="grade"><?php print_string('',$book);?>grade</option>
			  </select>
			</td>
		  </tr>
		</table>
	  </fieldset>

	  <fieldset class="centerrighttop">
		<legend><?php print_string('',$book);?>Details for the New Mark Definition</legend>
		<label for="Name"><?php print_string('',$book);?>Mark Definition's Title (an identifying name):</label>
		<input class="required" type="text" name="name" id="Name" 
		  tabindex="2" maxlength="20"  pattern="alphanumeric" />
		  <label for="Comment"><?php print_string('',$book);?>Comment (helps describe this mark definition):</label>
		  <input type="text" name="comment" id="Comment" 
			tabindex="3" maxlength="98" pattern="alphanumeric" />
	  </fieldset> 
<?php
	include ("classes_to_use.php");
?>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print $choice;?>" />
	</form>
  </div>
