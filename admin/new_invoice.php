<?php
/**									new_inovice.php
 */

$action='new_invoice_action.php';

$ordid=$_POST['ordid'];
$budid=$_POST['budid'];
$entryn=$_POST['entryn'];

three_buttonmenu();

$Invoice=fetchInvoice();
$Order=fetchOrder($ordid);

?>

  <div id="heading">
	<label><?php print_string('newinvoice',$book);?></label>
	<?php print get_string('order',$book).' '.$Order['Reference']['value'];?>
  </div>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center">
		<div class="center">
<?php 

?>
		</div>
	  </fieldset>

	  <div class="center">
		  <?php $tab=xmlarray_form($Invoice,'','newinvoice',$tab,$book); ?>
	  </div>

		<input type="hidden" name="ordid" value="<?php print $ordid;?>" />
		<input type="hidden" name="entryn" value="<?php print $entryn;?>" />
		<input type="hidden" name="budid" value="<?php print $budid;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>