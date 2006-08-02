<?php
/**								update_curriculum.php
 *
 *Update the database tables to match with entries from the curriculum
 *files. It does not (as yet) remove any data fro mthe database even if 
 *it has been removed from the curriculum files.
 */

$action='update_curriculum_action.php';
$choice='update_curriculum.php';

three_buttonmenu();
?>

<div class="content">
<form id="formtoprocess" name="formtoprocess" onChange="return
	validateForm();" method="post" action="<?php print $host;?>">
	<fieldset class="center"> 
	<legend><?php print_string('updatecurriculum',$book); ?></legend> 
	<?php print_string('updatecurriculumwarning',$book); ?>
	</fieldset>

	<fieldset class="center"> 
		<legend><?php print_string('confirm',$book);?></legend>
		<p><?php print_string('confidentwhatyouaredoing',$book);?></p>
		<div class="right">
<?php
	check_yesno();
?>
		</div>
	</fieldset>


	<input type="hidden" name="cancel" value="<?php print ''; ?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
</form> 
</div>


