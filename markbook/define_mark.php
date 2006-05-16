<?php 
/* 			  				define_mark.php
*/

$host="markbook.php";
$current="define_mark.php";
$action="define_mark_action1.php";
$choice="define_mark.php";

?>
<div class="content">
<form name="formtoprocess" id="formtoprocess" method='post' action='<?php print $host;?>'> 
<fieldset class="lefttop">
<legend>Choose a Mark-Type</legend>
<table>
<tr>
	<td><label for="Type">Type of mark:</label>
	<select class="required" name="type" id="Type" tabindex="1">
		<option value="" selected="selected"></option>
		<option value="comment">text comment</option>
		<option value="value">numeric score</option>
		<option value="percentage">value and total</option>
		<option value="grade">grade</option>
		<option value="tier">tier</option>
		</select>
	</td>
</tr>
</table>
</fieldset>

<fieldset class="centerrighttop">
<legend>Details for the New Mark Definition</legend>
<label for="Name">Mark Definition's Title (an identifying name):</label>
 	<input class="required" type="text" name="name" id="Name" 
				tabindex="2" maxlength="20"  pattern="alphanumeric" />
<label for="Comment">Comment (helps describe this mark definition):</label>
  	<input type="text" name="comment" id="Comment" 
		tabindex="3" maxlength="98" pattern="alphanumeric" />
</fieldset> 
<?php
	include ("classes_to_use.php");
?>
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
</form>
</div>
<div class="buttonmenu">
	<button onClick="processContent(this);" name="sub" value="Submit">Submit</button>
	<button onClick="processContent(this);" name="sub" value="Cancel">Cancel</button>
	<button onClick="processContent(this);" name="sub" value="Reset">Reset</button>
</div>



















































