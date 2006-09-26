<?php
/**								update_curriculum_check.php
 *
 */

$action='update_curriculum_action.php';

include('scripts/answer_action.php');

three_buttonmenu();
?>

<div class="content">
<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	<fieldset class="center">
	<legend><?php print_string('updatecurriculum',$book); ?></legend> 
	<?php print_string('updatecurriculumwarning',$book); ?>
	</fieldset>

	<fieldset class="center">
		<legend><?php print_string('assessmentmethods',$book);?></legend>
		<p><?php print_string('deletesallmarksetc',$book);?></p>
		<div class="right">
		  <?php $checkname='asscheck'; include('scripts/check_yesno.php');?>
		</div>
	</fieldset>

	<fieldset class="center">
		<legend><?php print_string('curriculum',$book);?></legend>
		<p><?php print_string('deletesallclassesetc',$book);?></p>
		<div class="right">
		  <?php $checkname='coursecheck'; include('scripts/check_yesno.php');?>
		</div>
	</fieldset>

	<fieldset class="center">
		<legend><?php print_string('pastoralgroups',$book);?></legend>
		<p><?php print_string('deletesallyeargroupsetc',$book);?></p>
		<div class="right">
		  <?php $checkname='groupcheck'; include('scripts/check_yesno.php');?>
		</div>
	</fieldset>



	<input type="hidden" name="cancel" value="<?php print ''; ?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
</form> 
</div>
