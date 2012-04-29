<?php
/**								fees_remittance_export.php
 *
 */

$action='fees_remittance_export_action.php';
$choice='fees.php';

$remid=$_POST['remid'];
if(isset($_POST['sids'])){$charids=(array)$_POST['sids'];}else{$charids=array();}

three_buttonmenu();
?>

<div class="content">
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	<fieldset class="center"> 
	<legend><?php print_string('export',$book); ?></legend> 
	<?php print_string('feesexportwarning',$book); ?>
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

<?php
foreach($charids as $charid){
	print '	<input type="hidden" name="charids[]" value="'.$charid.'" />';
	}
?>
	<input type="hidden" name="remid" value="<?php print $remid;?>" />
	<input type="hidden" name="cancel" value="<?php print ''; ?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form> 
</div>
