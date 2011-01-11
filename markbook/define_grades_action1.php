<?php 
/* 									define_grades_action1.php
*/

$host='markbook.php';
$current='define_grades_action1.php';
$action='define_grades_action2.php';
$choice='class_view.php';

$gena=$_POST['gena'];
$num=$_POST['num'];
$bid=$_POST['bid'];
$crid=$_POST['crid'];
$comment=$_POST['comment'];
?>
<div class="content">
<form name="formtoprocess" id="formtoprocess" novalidate method="post" action="<?php print $host; ?>" > 

<fieldset class="leftcentertopmiddlebottom">
<legend>Assign Weighting of Grades</legend>

<label>Enter grades in <em>ascending</em> order, giving each a numerical weighting:</label>
<table>
	<tr>
		<td><label for="Grades">Grade</label></td><td><label for="Weights">Weighting Value (0-99)</label></td>
	</tr>
<?php	 
	for($c3=0; $c3<$num; $c3++){
?>				
	<tr>
		<td><input class="required" type="text" id="Grades <?php print $c3+1; ?>" name="grades[]" maxlength="2" value="" pattern="alphanumeric" /></td>
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
<div class="buttonmenu">
	<button onClick="processContent(this);" name="sub" value="Submit">Submit</button>
	<button onClick="processContent(this);" name="sub" value="Cancel">Cancel</button>
	<button onClick="processContent(this);" name="sub" value="Reset">Reset</button>
	<button style="visibility:hidden;" name="" value=""></button>
</div>
