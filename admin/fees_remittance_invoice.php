<?php
/**								fees_remittance_invoice.php
 *
 */

$action='fees_remittance_invoice_action.php';
$cancel='fees_remittance_list.php';
$choice='fees.php';

$remid=$_POST['remid'];
if(isset($_POST['sids'])){$charids=(array)$_POST['sids'];}else{$charids=array();}

three_buttonmenu();
?>

<div class="content">
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	<fieldset class="center"> 
	<legend><?php print_string('invoice',$book); ?></legend> 
	<?php print_string('feesinvoicewarning',$book); ?>
	</fieldset>

	<fieldset class="center divgroup"> 
		<legend><?php print_string('confirm',$book);?></legend>
		<p><?php print_string('confidentwhatyouaredoing',$book);?></p>
		<div class="right">
<?php 
	$checkcaption=get_string('payment',$book); $checkname='payment';
	include('scripts/check_yesno.php');
?>
		</div>
	</fieldset>

	<input type="hidden" name="remid" value="<?php print $remid;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel; ?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form> 
</div>
