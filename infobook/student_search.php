<?php 
/********										student_search.php	
*/
	$current="student_search.php";
	$action="search_action1.php";
	$host="infobook.php";
?>
<form method="post" novalidate action="<?php print $host ?>" name="studentsearch" id="studentsearch">

<fieldset class="leftcentertop"><legend>Name of Student</legend>
<br />
	<label for="Surname">Surname:&nbsp;</label>
	<input class="required" type="text" id="Surname" name="surname" value="" maxlength="30" pattern="[^A-Za-z ]+" />
<br />
<br />
	<label for="Forename">Forename:</label>
	<input type="text" id="Forename" name="forename" value="" maxlength="30" pattern="[^A-Za-z ]+" />
</fieldset>
	
<fieldset class="leftmiddle"><legend>Year (if known)</legend>
<br />
		<?php	include("scripts/list_year.php");?>
</fieldset>
		
<fieldset class="centermiddle"><legend>Form (if known)</legend>
<br />
		<?php	include("scripts/list_form.php");?>
</fieldset>
				

<fieldset class="leftbottom"><legend>Submit Search</legend>
<br />
	<button type="submit" id="submit" name="submit" value="Advanced Search" onClick="return validateForm(this.form);">Advanced Search</button>
<br />
<br />
	<button style="background-color:#666666; color:#ff9900;" type="reset" id="reset" name="reset">Reset</button>
</fieldset>

	<input type="hidden" name="choice" value="<?php print $current;?>">	
	<input type="hidden" name="current" value="<?php print $action;?>">

	</form>










