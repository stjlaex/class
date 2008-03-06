<?php
/**									new_order.php
 */

$action='new_order_action.php';
$action_post_vars=array('budid');

include('scripts/sub_action.php');

$budgetyear=$_POST['budgetyear'];
$budid=$_POST['budid'];
$Budget=fetchBudget($budid);
$Order=fetchOrder();
$Material=fetchMaterial();

three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('budget',$book);?></label>
	<?php print $Budget['Name']['value'];?>
  </div>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="left">
		<?php $tab=xmlarray_form($Order,'','neworder',$tab,$book); ?>
	  </div>

	  <div class="right">
		<table class="listmenu">
		  <caption><?php print_string('supplier',$book);?></caption>
		  <tr>
			<td>
<?php
		$d_sup=mysql_query("SELECT id, name FROM ordersupplier;");
		$required='yes';
		$listlabel='supplier';
		$listname='supid';
		include('scripts/set_list_vars.php');
		list_select_db($d_sup,$listoptions,$book);
		unset($listoptions);
?>
			</td>
		  </tr>
		</table>
	  </div>

	  <div class="center">
		<table class="listmenu">
		  <th>&nbsp;</th>
		  <th style="width:40%;"><?php print_string($Material['Detail']['label'],$book);?></th>
		  <th><?php print_string($Material['SupplierReference']['label'],$book);?></th>
		  <th><?php print_string($Material['Quantity']['label'],$book);?></th>
		  <th><?php print_string($Material['Unitcost']['label'],$book);?></th>
<?php
	  for($matn=1;$matn<6;$matn++){
?>
		  <tr>
			<td><?php print $matn;?></td>
			<td><?php $tab=xmlelement_input($Material['Detail'],$matn,$tab,$book);?></td>
			<td><?php $tab=xmlelement_input($Material['SupplierReference'],$matn,$tab,$book);?></td>
			<td><?php $tab=xmlelement_input($Material['Quantity'],$matn,$tab,$book);?></td>
			<td><?php $tab=xmlelement_input($Material['Unitcost'],$matn,$tab,$book);?></td>
		  </tr>
<?php
		  }
?>
		</table>
	  </div>

	    <input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>">
	    <input type="hidden" name="budid" value="<?php print $budid;?>">
	    <input type="hidden" name="matn" value="<?php print $matn;?>">
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>