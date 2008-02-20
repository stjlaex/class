<?php
/**			       		report_comments.php
 */

$action='report_comments_list.php';
$choice='report_comments.php';

$tomonth=date('n')-1;
$today=date('j');
$toyear=date('Y');
$todate=$toyear.'-'.$tomonth.'-'.$today;

three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('search',$book);?></label>
<?php	print_string('comments',$book);?>
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

	 <fieldset class="left" >
		<legend><?php print_string('limittoonetype',$book);?></legend>
<?php
		$listlabel='category'; $listid='category';
		$required='no';
		include('scripts/list_rating.php');
?>
	  </fieldset>

	 <fieldset class="right" >
		<legend><?php print_string('limittoonesubject');?></legend>
<?php
		  $required='no';
		  include('scripts/list_subjects.php');
?>
	  </fieldset>

	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	  <input type="hidden" name="cancel" value="<?php print ''; ?>">
	</form>
  </div

