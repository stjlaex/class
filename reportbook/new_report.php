<?php
/*												new_report.php
 */

$action='new_report_action.php';
$choice='new_report.php';

include('scripts/course_respon.php');

three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center">
		<legend><?php print_string('identityofreport',$book);?></legend>

		<div class="left">
		  <label for="Title"><?php print_string('title');?></label>
		  <input class="required" type="text" id="Title" name="title"
		  length="40" maxlength="60" />
		</div>
		<div class="right">
		  <label><?php print_string('publisheddate');?></label>
		  <?php include('scripts/jsdate-form.php'); ?>
		</div>
		<div class="left">
		  <label for="Comment"><?php print_string('description');?></label>
		  <input type="text" id="Comment" name="comment" length="40" maxlength="250"
				onChange="tickAndSelect(this);	" />
		</div>
		<div class="right">
		  <label><?php print_string('deadlineforcompletion',$book);?></label>
		  <?php include('scripts/jsdate-form.php'); ?>
		</div>
	  </fieldset>

	  <fieldset class="left">
		<legend><?php print_string('includeassessmentscores',$book);?></legend>
		<?php include('scripts/list_assessment.php');?>
	  </fieldset>


	  <fieldset class="right">
		<legend><?php print_string('writtencomments',$book);?></legend>

		<div class="left">
		  <label><?php print_string('addcategories',$book);?></label>
		  <?php check_yesno('addcategory')?>
		</div>
		<div class="left">
		  <label><?php print_string('allowsubjectcomments',$book);?></label>
		  <?php check_yesno('reptype')?>
		</div>
		<div class="left">
		  <label><?php print_string('commentsarecompulsory',$book);?></label>
		  <?php check_yesno('commentcomp')?>
		</div>
		<div class="right">
		  <label for="Comment Length">
			<?php print_string('restrictcommentscharacterlength',$book);?>
		  </label>
		  <input type="text" pattern="integer" id="Comment Length"
			name="commentlength" maxlength="5" length="4"/>
		</div>
	  </fieldset>


	  <fieldset class="right">
		<legend><?php print_string('properties',$book);?></legend>
		<div>
		  <?php include('scripts/list_stage.php') ;?>
		</div>

		<div class="left">
		  <?php include('scripts/list_componentstatus.php'); ?>
		</div>
	  </fieldset>

	  <fieldset class="left">
		<legend><?php print_string('nameoftemplate',$book);?></legend>
		<?php include('scripts/list_template.php');?>
	  </fieldset>


		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice; ?>"/>
		<input type="hidden" name="cancel" value="<?php print ''; ?>"/>
	</form>
  </div>
