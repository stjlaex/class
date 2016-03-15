<?php
/*									define_grades.php
*/

$current="define_grades.php";
$action="define_grades_action1.php";
$choice="new_assessment.php";

three_buttonmenu();
?>
<div class="content">
    <form name="formtoprocess" id="formtoprocess" novalidate method="post" action="<?php print $host; ?>" >

    <fieldset class="lefttop">
    <legend><?php print_string('definegrades', $book); ?></legend>
    <label for="Name"><?php print_string('gradingsname', $book); ?></label>
	<input class="required" type="text" name="gena" id="Name" tabindex="1" maxlength="20" value=""  pattern="alphanumeric" />
    <label for="Number of Levels"><?php print_string('levelsno', $book); ?></label>
	<input style="width:3em;" class="required" type="text" name="num" id="Number of levels" tabindex="2" maxlength="2" value=""  pattern="integer" />
    </fieldset>

    <fieldset class="centerrighttop">
    <legend><?php print_string('comment', $book); ?></legend>
	<label for="Comment"><?php print_string('gradingsdescription', $book); ?></label>
	<input type="text" name="comment" id="Comment" style="width:93%" tabindex="3" value="" maxlength="98"  pattern="alphanumeric" />
    </fieldset>

	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
    </form>
</div>
