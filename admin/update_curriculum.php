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
	<legend>Update Curriculum</legend> 

	<p>This will reload the curriculum packs for your database. The
	curriculum packs should have first been configured to match the
	needs of your school. Please refer to the ClaSS Administrators Guide for
	instructions on how to do this.<p/>

	<p>The curriculum should, in general, only be updated in advance
	of the start of an academic year.<p/>

	</fieldset>

	<fieldset class="center"> 
	<legend>Confirm</legend>
	<p>You really do need to be confident you know what your doing to
	continue.</p>

<?php
	check_yesno();
?>

	</fieldset>


	<input type="hidden" name="cancel" value="<?php print ''; ?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
</form> 
</div>


