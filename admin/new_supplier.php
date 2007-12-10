<?php
/**									new_supplier.php
 */

$action='new_supplier_action.php';

three_buttonmenu();

$Supplier=fetchSupplier();

?>

  <div id="heading">
	<label><?php print_string('newbudget',$book);?></label>
  </div>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		  <?php $tab=xmlarray_form($Supplier,'','newsupplier',$tab,'admin'); ?>
	  </div>

	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>