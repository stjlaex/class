<?php
/**									new_supplier.php
 */

$action='new_supplier_action.php';

if(isset($_GET['supid'])){$supid=$_GET['supid'];}else{$supid=-1;}
if(isset($_POST['supid'])){$supid=$_POST['supid'];}

if(isset($_GET['budgetyear'])){$budgetyear=$_GET['budgetyear'];}else{$budgetyear='';}
if(isset($_POST['budgetyear'])){$budgetyear=$_POST['budgetyear'];}

three_buttonmenu();

$Supplier=fetchSupplier($supid);

?>

  <div id="heading">
	<label><?php print_string('newsupplier',$book);?></label>
  </div>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		  <?php $tab=xmlarray_form($Supplier,'','supplier',$tab,'admin'); ?>
	  </div>

	  <div class="center">
		  <?php $tab=xmlarray_form($Supplier['Address'],'','contactaddress',$tab,'infobook'); ?>
	  </div>

	    <input type="hidden" name="supid" value="<?php print $supid;?>">
	    <input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>">
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>