<?php
/**								import_profiles.php
 *
 */

$action='import_profiles_action.php';
$choice='import_profiles.php';

$curryear=get_curriculumyear();

three_buttonmenu();
?>
<div id="heading">
<?php print get_string('importprofiles',$book);?>
</div>

<div class="content">
	<form id="formtoprocess" name="formtoprocess" 
	  enctype="multipart/form-data" method="post" action="<?php print $host;?>">

	  <fieldset class="left">
		<legend><?php print_string('selectfiletoimportfrom');?></legend>
		<label for="File name"><?php print_string('filename');?></label>
		<input style="width:20em;" type="file" id="File name" 
		  tabindex="<?php print $tab++;?>" class="required" name="importfile" />
		<input type="hidden" name="MAX_FILE_SIZE" value="800000">
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('fieldseparator',$book);?></legend>
		<label for="separator"><?php print_string('comma',$book);?></label>
		<input type="radio" name="separator" tabindex="<?php print $tab++;?>"
			eitheror="enrolno"   checked="checked" 
			id="comma" title="" value="comma" />

		<label for="separator"><?php print_string('semicolon','infobook');?></label>
		<input type="radio" name="separator" tabindex="<?php print $tab++;?>"
		  eitheror="sid"  
		  title="" id="semicolon" value="semicolon" />
	  </fieldset>


 	<input type="hidden" name="curryear" value="<?php print $curryear; ?>">
 	<input type="hidden" name="cancel" value="<?php print $choice; ?>">
 	<input type="hidden" name="current" value="<?php print $action; ?>">
 	<input type="hidden" name="choice" value="<?php print $choice; ?>">
	</form>
  </div>

