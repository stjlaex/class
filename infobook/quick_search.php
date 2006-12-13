<?php 
/**										quick_search.php
 */
$action='search_action1.php'
?>	
  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">	
	<form id="infobookchoice" name="infobookchoice" method="post"
		action="infobook.php" target="viewinfobook">

	<fieldset class="infobook">
		<legend><?php print_string('studentgroups');?></legend>
<?php	
	$onsidechange='yes'; include('scripts/list_year.php');
	$onsidechange='yes'; include('scripts/list_form.php');
?>
	</fieldset>

	  <input type="hidden" name="current" value="<?php print $action;?>"/>
	</form>

	<form id="quicksearch" name="quicksearch" method="post"
		action="infobook.php" target="viewinfobook">
	<fieldset class="infobook">
		<legend><?php print_string('studentsearch');?></legend>
		<label for="Surname"><?php print_string('surname');?></label>
		<input type="text" id="Surname" name="surname" value="" maxlength="30"/>
		  <label for="Forename"><?php print_string('forename');?></label>
		  <input type="text" id="Forename" name="forename" value="" maxlength="30"/>

			<button type="submit" name="submit">
				<?php print_string('search');?>
			</button>
			<button type="reset" name="reset" value="Reset">
				<?php print_string('reset');?>
			</button>
	</fieldset>

			<input type="hidden" name="current" value="<?php print $action;?>"/>
	  </form>
  </div>
