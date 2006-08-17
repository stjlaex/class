<?php 
/** 									year_end.php
 */

$action='year_end_action.php';
$choice='year_end.php';

three_buttonmenu();
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center"> 
		<legend><?php print_string('yearend',$book);?></legend> 
		<?php print_string('yearendwarning',$book);?>
	</fieldset>

	  <fieldset class="center"> 
		<legend><?php print_string('confirm',$book);?></legend>
		<p><?php print_string('confidentwhatyouaredoing',$book);?></p>

		<div class="right">
		  <?php	check_yesno();?>
		</div>
	</fieldset> 


		<input type="hidden" name="cancel" value="<?php print ''; ?>">
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form> 
  </div>


