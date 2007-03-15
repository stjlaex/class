<?php
/**
 *                                  student_view_medical.php
 */

$action='student_view_medical1.php';
$cancel='student_view.php';

two_buttonmenu();
?>
  <div id="heading">
	<?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center">	
		<legend><?php print_string('medicalcaption',$book);?></legend>

<?php 
if($Student['MedicalFlag']['value']=='N'){
?>

		<p><?php print_string('nomedicalinfo',$book);?></p>
		<button name="sub" value="MedicalStatus">
		  <?php print_string('medicalbutton',$book);?></button>

<?php
	}
else{
?>
		<div class="center">
		  <p>Medical information is available on file.</p>
		</div>
<?php
		}
?>
	  </fieldset>
 	<input type="hidden" name="current" value="<?php print $action;?>" />
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
</form>
</div>
