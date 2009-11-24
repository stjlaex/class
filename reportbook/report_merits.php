<?php
/**			       		report_merits.php
 */

$action='report_merits_list.php';
$choice='report_merits.php';


//last two weeks by default
$todate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-14,date('Y')));

three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('search',$book);?></label>
<?php	print_string('merits');?>
  </div>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <fieldset class="center">
		<legend><?php print_string('collateforstudentsfrom',$book);?></legend>
		  <?php $required='yes'; include('scripts/'.$listgroup);?>
	  </fieldset>

	  <fieldset class="left">
		<legend><?php print_string('collatesince',$book);?></legend>
		<?php include('scripts/jsdate-form.php'); ?>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('collateuntil',$book);?></legend>
		<?php $required='no'; unset($todate); include('scripts/jsdate-form.php'); ?>
	  </fieldset>


	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	  <input type="hidden" name="cancel" value="<?php print ''; ?>">
	</form>
  </div

