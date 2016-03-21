<?php
/**									add_register_event.php
 *
 */


$action='add_register_event_action.php';

include('scripts/sub_action.php');

three_buttonmenu();
?>
  <div id="viewcontent" class="content">
      <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	<fieldset class="lefttop">
	    <legend><?php print_string('addevent', $book); ?></legend>

	    <label for="Date"><?php print_string('date', $book); ?></label>
	    <input class="required" type="date" name="date" id="Date" tabindex="1" value="" />
<?php
	    $required='yes';
	    $listlabelstyle='external';
	    include('scripts/list_session.php');
?>
	</fieldset>

	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print '';?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
      </form>
  </div>
