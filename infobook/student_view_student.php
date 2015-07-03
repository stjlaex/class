<?php
/**									student_view_student.php
 */

$action='student_view_student1.php';
$cancel='student_view.php';

include('scripts/sub_action.php');

/*Check user has permission to view*/
$yid=$Student['YearGroup']['value'];
$perm=getYearPerm($yid);
include('scripts/perm_action.php');

three_buttonmenu();
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div id="viewcontent" class="center">
		<?php unset($Student['YearGroup']); //to avoid communities mixing up ?>
		<?php $tab=xmlarray_form($Student,'','studentdetails',$tab,$book);?>
	  </div>
<?php
	if(isset($Student['ExtraInfo']) and count($Student['ExtraInfo'])>0){
?>
	  <div class="center" style="margin: 0 0 30px; float: left;">
		<?php
			$tab=xmlarray_form($Student['ExtraInfo'],'','extrainfo',$tab,'infobook'); 
		?>
	  </div>
<?php
		}
?>
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $cancel;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>
