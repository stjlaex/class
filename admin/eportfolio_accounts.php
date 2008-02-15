<?php 
/**				   				eportfolio_accounts.php
 */

$choice='eportfolio_accounts.php';
$action='eportfolio_accounts_action.php';

include('scripts/sub_action.php');


three_buttonmenu();

?>
  <div id="heading">
  <?php print get_string('eportfolios',$book).' ';?>
  </div>
  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center"> 
		<legend><?php print_string('eportfolios',$book);?></legend> 
		<?php print_string('eportfoliowarning',$book);?>
	  </fieldset>
	  
	  <fieldset class="center"> 
		<legend><?php print_string('confirm',$book);?></legend>
		<p><?php print_string('confidentwhatyouaredoing',$book);?></p>

		<div class="right">
		  <?php include('scripts/check_yesno.php');?>
		</div>
	  </fieldset> 

		<table class="listmenu center">

		  <tr>

		  </tr>

		</table>

		<input type="hidden" name="cancel" value="" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
	</form>
</div>
