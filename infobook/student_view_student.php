<?php
/**									student_view_student.php
 */

$action='student_view_student1.php';
$cancel='student_view.php';

include('scripts/sub_action.php');

/*Check user has permission to view*/
$yid=$Student['YearGroup']['value'];
$perm=getYearPerm($yid, $respons);
include('scripts/perm_action.php');

three_buttonmenu();
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		<?php		  $tab=xmlarray_form($Student,'','studentdetails',$tab);?>	  
	  </div>
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $cancel;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>