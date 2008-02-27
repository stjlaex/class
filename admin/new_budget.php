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
		<div class="left">
<?php 
  	$d_group=mysql_query("SELECT id, name FROM section 
						ORDER BY sequence"); 
	$listname='secid';
	$listlabel='section';
	$required='yes';
	include('scripts/set_list_vars.php');
	list_select_db($d_group,$listoptions,$book);
	unset($listoptions);
?>
		</div>

		<div class="left">
<?php 
	list($ratingnames,$catdefs)=fetch_categorydefs('bud');
	$listname='catid';
	$listlabel='category';
	$listeitheror='Gid';
	$required='eitheror';
	include('scripts/set_list_vars.php');
	list_select_list($catdefs,$listoptions,$book);
	unset($listoptions);
?>
		</div>

		<div class="right">
<?php 
	/* crid must be % to only grab curriculum subject groups*/
  	$d_group=mysql_query("SELECT gid AS id, subject.name AS name FROM groups 
						JOIN subject ON subject_id=subject.id 
						WHERE groups.course_id='%' ORDER BY groups.name"); 
	$listname='gid';
	$listlabel='department';
	$listeitheror='Catid';
	$required='eitheror';
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