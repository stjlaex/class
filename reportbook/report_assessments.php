<?php
/**												report_assessments.php
 */

$action='report_assessments_view.php';
$choice='report_assessments.php';

three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" 
		name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <fieldset class="center">
		<legend><?php print_string('collateforstudentsfrom',$book);?></legend>
		  <?php $required='yes'; include('scripts/'.$listgroup);?>
	  </fieldset>
<?php
	  if($r>-1){
?>

	  <fieldset class="center">
		<legend><?php print_string('limitbysubject',$book);?></legend>
		<div class="left" >
		<?php $multi='4'; include('scripts/list_subjects.php');?>
		</div>
	  </fieldset>
<?php
	  }
?>

	  <fieldset class="center">
		<legend><?php print_string('choosetoinclude',$book);?></legend>
		<div class="center" >
		  <?php include('scripts/list_assessment.php');?>
		</div>
	  </fieldset>
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>
