<?php
/**
 *                                  student_view_enrolment.php
 */

$action='student_view_enrolment_action.php';

three_buttonmenu();

	/*Check user has permission to view*/
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid,$respons);
	include('scripts/perm_action.php');

?>
  <div id="heading">
	<?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center">
		<div class="left">
<?php 
	$Enrolment=fetchEnrolment($sid);
	$enrolstatus=$Enrolment['EnrolmentStatus']['value'];
	$listname='enrolstatus';$listlabel='enrolstatus';$required='yes';
	include('scripts/set_list_vars.php');
	list_select_enum('enrolstatus',$listoptions,$book);
?>
		</div>
		<div class="right">
<?php 
	$listname='enrolyear';$listlabel='year';$required='yes';
	$enrolyear=$Enrolment['Year']['value'];
	include('scripts/list_calendar_year.php');
?>
		</div>

	  </fieldset>

	  <div class="center">

		<table class="listmenu">
		  <tr>
			<td>&nbsp
			</td>
			<td>
<?php 
	$listname='enrolyid';$listlabel='yeargroup';$required='yes';
	$enrolyid=$Enrolment['YearGroup']['value'];
	include('scripts/list_year.php');
?>
			</td>
		  </tr>
		</table>


<?php 	$tab=xmlarray_form($Enrolment,'','',$tab,'infobook');?>
	  </div>

	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print $cancel;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
</div>
