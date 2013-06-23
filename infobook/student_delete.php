<?php
/**									student_delete.php
 *
 */

$action='student_delete_action.php';

/* Check user has permission to view students within this section. */
$perm=get_section_perm($student_secid);
include('scripts/perm_action.php');
/**/

three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('student'); ?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">


	  <fieldset class="center"> 
		<legend><?php print get_string('delete',$book).' '.get_string('student',$book);?></legend> 
		<?php print_string('studentdeletewarning',$book);?>
	  </fieldset>
	  
	  <fieldset class="center divgroup"> 
		<legend><?php print_string('confirm',$book);?></legend>
		<p><?php print_string('confidentwhatyouaredoing','admin');?></p>

		<div class="right">
		  <?php include('scripts/check_yesno.php');?>
		</div>
	  </fieldset> 



	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print 'student_list.php';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
</div>

