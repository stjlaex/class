<?php
/**									new_order.php
 */

$action='new_order_action.php';
$action_post_vars=array('budid');

include('scripts/sub_action.php');

if(isset($_POST['ordid'])){$ordid=$_POST['ordid'];}else{$ordid=-1;}
$budgetyear=$_POST['budgetyear'];
$budid=$_POST['budid'];
$Budget=fetchBudget($budid);
$Order=fetchOrder($ordid);
$Materialblank=fetchMaterial();

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
		$listlabel='supplier';
		$selsupid=$Order['Supplier']['id_db'];
		$listname='supid';
		include('scripts/set_list_vars.php');
		list_select_db($d_sup,$listoptions,$book);
		unset($listoptions);
?>
			</td>
		  </tr>
		  <tr>
			<td>
<?php
$checkcaption='Petty cash?';
$checkname='pettycash';
if($Order['Supplier']['id_db']==0){$checkchoice='yes';}
include('scripts/check_yesno.php');
?>
			</td>
		  </tr>
		</table>
	  </div>

	  <div class="center">
		<table class="listmenu">
		  <th>&nbsp;</th>
		  <th><?php print_string($Materialblank['Type']['label'],$book);?></th>
		  <th style="width:40%;"><?php print_string($Materialblank['Detail']['label'],$book);?></th>
		  <th><?php print_string($Materialblank['SupplierReference']['label'],$book);?></th>
		  <th><?php print_string($Materialblank['Quantity']['label'],$book);?></th>
		  <th><?php print_string($Materialblank['Unitcost']['label'],$book);?></th>
<?php

		$materialno=sizeof($Order['Materials']['Material']);
		if($materialno<8){
			for($matn=$materialno;$matn<8;$matn++){
				$Order['Materials']['Material'][]=$Materialblank;
				}
			  }

		while(list($index,$Material)=each($Order['Materials']['Material'])){
			$matn=$index+1;
?>
		  <tr>
			<td><?php print $matn;?></td>
			<td><?php $tab=xmlelement_input($Material['Type'],$matn,$tab,$book);?></td>
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
	    <input type="hidden" name="ordid" value="<?php print $ordid;?>">
	    <input type="hidden" name="matn" value="<?php print $matn;?>">
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>