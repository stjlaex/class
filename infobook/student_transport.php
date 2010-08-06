<?php
/**
 *                                  student_transport.php
 */

$action='student_transport_action.php';

three_buttonmenu();

	/*Check user has permission to view*/
	$perm=getFormPerm($Student['RegistrationGroup']['value'],$respons);
	include('scripts/perm_action.php');

?>
  <div id="heading">
	<?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center listmenu">
		<div class="left">
<?php 
	$Enrolment=fetchEnrolment($sid);
	$enrolstatus=$Enrolment['EnrolmentStatus']['value'];
	$listname='enrolstatus';$listlabel='enrolstatus';$required='yes';
	include('scripts/set_list_vars.php');
	list_select_enum('enrolstatus',$listoptions,$book);
?>
		</div>
	  </fieldset>

	  <fieldset class="center listmenu">
<?php 	$tab=xmlarray_form($Enrolment,'','',$tab,'infobook');?>
	  </fieldset>


	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print $cancel;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
</div>
