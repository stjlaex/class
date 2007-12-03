<?php
/**									new_budget.php
 */

$action='new_budget_action.php';

three_buttonmenu();

$Budget=fetchBudget();

?>

  <div id="heading">
	<label><?php print_string('newbudget',$book);?></label>
  </div>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center">
		<div class="center">
<?php 
  	$d_group=mysql_query("SELECT gid AS id, name AS name FROM groups 
						WHERE course_id='%' ORDER BY name"); 
	$listname='gid';
	$listlabel='department';
	$required='yes';
	include('scripts/set_list_vars.php');
	list_select_db($d_group,$listoptions,$book);
	unset($listoptions);
?>
		</div>
	  </fieldset>

	  <div class="center">
		  <?php $tab=xmlarray_form($Budget,'','newbudget',$tab,'admin'); ?>
	  </div>

	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>