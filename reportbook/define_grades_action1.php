<?php
/*									define_grades_action1.php
*/

$current='define_grades_action1.php';
$action='define_grades_action2.php';
$choice='new_assessment.php';

include('scripts/sub_action.php');

$gena=$_POST['gena'];
$num=$_POST['num'];
$bid=$_POST['bid'];
$crid=$_POST['crid'];
$comment=$_POST['comment'];

three_buttonmenu();
?>
<div class="content">
    <form name="formtoprocess" id="formtoprocess" novalidate method="post" action="<?php print $host; ?>" >

	<fieldset class="leftcentertopmiddlebottom">
	    <legend><?php print_string('assignweighting', $book); ?></legend>

	    <label><?php print_string('entergradesweighting', $book); ?></label>
	    <table>
		<tr>
			<td><label for="Grades"><?php print_string('grades', $book); ?></label></td><td><label for="Weights"><?php print_string('weightingvalues', $book); ?></label></td>
		</tr>
<?php
	for($c3=0; $c3<$num; $c3++){
?>
		<tr>
			<td><input class="required" type="text" id="Grades <?php print $c3+1; ?>" name="grades[]" maxlength="25" value="" pattern="alphanumeric" /></td>
			<td><input class="required" type="text" id="Weights <?php print $c3+1; ?>" name="weights[]" maxlength="2" value="" pattern="integer" /></td>
		</tr>
<?php
	    }
?>
	    </table>
	</fieldset>

	<input type="hidden" name="crid" value="<?php print $crid; ?>" />
	<input type="hidden" name="bid" value="<?php print $bid; ?>" />
	<input type="hidden" name="gena" value="<?php print $gena; ?>" />
	<input type="hidden" name="num" value="<?php print $num; ?>" />
	<input type="hidden" name="comment" value="<?php print $comment; ?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
    </form>
</div>
