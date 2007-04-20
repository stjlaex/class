<?php
/**									new_contact.php
 */

$choice='new_contact.php';
$action='new_contact_action.php';

three_buttonmenu();

$Contact=fetchContact(array('guardian_id'=>'-1'));
$Address=fetchAddress(array('guardian_id'=>'-1'));
$Phone=fetchPhone(array('guardian_id'=>'-1'));
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		  <?php $tab=xmlarray_form($Contact,'','newcontact',$tab,'infobook'); ?>
	  </div>

	  <div class="left">
		  <?php $tab=xmlarray_form($Address,'','contactaddress',$tab,'infobook'); ?>
	  </div>

	  <div class="right">
		  <?php $tab=xmlarray_form($Phone,'','contactphones',$tab,'infobook'); ?>
	  </div>

	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print '';?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>