<?php
/**		   					eportfolio_accounts_check.php
 *
 */

$action='eportfolio_accounts_action.php';

include('scripts/answer_action.php');

three_buttonmenu();
?>

  <div id="heading">
	<?php print get_string('eportfolios',$book).' ';?>
	</div>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center">
		<legend><?php print_string('eportfolios',$book); ?></legend> 
		<?php print_string('eportfoliowarning',$book); ?>
		<div class="right">
<?php
		  $checkcaption=get_string('blankfirst',$book); $checkname='blank'; 
		  include('scripts/check_yesno.php');
?>
		</div>
	  </fieldset>

	  <fieldset class="center">
		<legend><?php print_string('staff',$book);?></legend>
		<p></p>
		<div class="right">
		  <?php $checkcaption=get_string('update',$book);
		  $checkname='staffcheck'; include('scripts/check_yesno.php');?>
		</div>
	  </fieldset>

	  <fieldset class="center">
		<legend><?php print_string('students','infobook');?></legend>
		<p></p>
		<div class="right">
		  <?php $checkcaption=get_string('update',$book); 
			$checkname='studentcheck'; include('scripts/check_yesno.php');?>
		</div>
	  </fieldset>

	  <fieldset class="center">
		<legend><?php print_string('contacts','infobook');?></legend>
		<p></p>
		<div class="left">
<?php 
		  $checkcaption=get_string('blankaccounts',$book); $checkname='contactblank'; 
		  include('scripts/check_yesno.php');
?>
		</div>
		<div class="right">
<?php 
		  $checkcaption=get_string('update',$book); $checkname='contactcheck';
		  include('scripts/check_yesno.php');
?>
		</div>
	  </fieldset>


	<input type="hidden" name="cancel" value="<?php print ''; ?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
</form> 
</div>
