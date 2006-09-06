<?php
/**									new_student.php
 */

$choice='new_student.php';
$action='new_student_action.php';

three_buttonmenu();

$Student=fetchStudent();
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		  <?php xmlarray_form($Student,'','newstudent',$tab); ?>
	  </div>
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print '';?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>