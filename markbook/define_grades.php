<?php 
/* 									define_grades.php
*/

$host="markbook.php";
$current="define_grades.php";
$action="define_grades_action1.php";
$choice="class_view.php";
?>
<div class="content">
<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>" > 

<fieldset class="lefttop">
<legend>Define a New Grade Scheme</legend>
<label for="Name">An identifying name for the grade-scheme</label>
 	<input class="required" type="text" name="gena" id="Name" tabindex="1" maxlength="20" value=""  pattern="alphanumeric" />
<label for="Number of Levels">The number of grade levels</label>
	<input style="width:3em;" class="required" type="text" name="num" id="Number of levels" tabindex="2" maxlength="2" value=""  pattern="integer" />
</fieldset>

<fieldset class="centerrighttop">
<legend>Comment</legend>
	<label for="Comment">Brief description (up to 98 characters)</label>
	<input type="text" name="comment" id="Comment" style="width:93%" tabindex="3" value="" maxlength="98"  pattern="alphanumeric" />
</fieldset>
 
<?php
	include ("classes_to_use.php");
?>

<fieldset class="leftbottom">
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
</fieldset>
</form>
</div>

<div class="buttonmenu">
	<button onClick="processContent(this);" name="sub" value="Submit">Submit</button>
	<button onClick="processContent(this);" name="sub" value="Cancel">Cancel</button>
	<button onClick="processContent(this);" name="sub" value="Reset">Reset</button>
	<button style="visibility:hidden;" name="" value=""></button>
</div>












