<?php
/**
 *                                  student_photo.php
 */

$action='student_view.php';

two_buttonmenu();

	/*Check user has permission to view*/
	$perm=getFormPerm($Student['RegistrationGroup']['value']);
	if($perm['r']!=1){$perm=getSENPerm($Student['YearGroup']['value']);}
	include('scripts/perm_action.php');

?>
  <div id="heading">
	<?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print $cancel;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
<?php
	require_once('lib/eportfolio_functions.php');
	html_document_drop($Student['EPFUsername']['value'],'icon','%');
?>
</div>
