<?php
/**									new_order.php
 *
 */

$action='new_order_action.php';
$action_post_vars=array('budid');
$maxmatn=12;
include('scripts/sub_action.php');

if(isset($_POST['ordid'])){$ordid=$_POST['ordid'];}else{$ordid=-1;}
$Order=fetchOrder($ordid);
if($ordid==-1){
	$budid=$_POST['budid'];
	}
else{
	/* Editing an existing order.*/
	$budid=$Order['Budget']['value_db'];
	}
$Budget=fetchBudget($budid);
$budgetyear=get_budgetyear($Budget['YearCode']['value']);
$Materialblank=fetchMaterial();
$perms=get_budget_perms($budid);

three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('budget',$book);?></label>
	<?php print $Budget['Name']['value'];?>
  </div>

  <div class="content" id="viewcontent">
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
		  /* The specialaction=1 is for suppliers who are probably not
		   * 'suppliers' ie. petty cash or photocopy and which will bypass
		   * the action tracking for deilvery and invoices. But still
		   * need to be authorised.
		   */
		if($perms['x']==1 or $_SESSION['role']=='office'){$special='%';}
		else{$special=0;}
		$d_sup=mysql_query("SELECT id, name FROM ordersupplier WHERE
						inactive='0' AND specialaction LIKE '$special' ORDER
						BY specialaction, name;");
		$listlabel='supplier';
		$selsupid=$Order['Supplier']['id_db'];
		$listname='supid';
		$required='yes';
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
		  <th><?php print_string($Materialblank['Type']['label'],$book);?></th>
		  <th style="width:40%;"><?php print_string($Materialblank['Detail']['label'],$book);?></th>
		  <th><?php print_string($Materialblank['SupplierReference']['label'],$book);?></th>
		  <th><?php print_string($Materialblank['Quantity']['label'],$book);?></th>
		  <th><?php print_string($Materialblank['Unitcost']['label'],$book);?></th>
<?php

		$materialno=sizeof($Order['Materials']['Material']);
		if($materialno>($maxmatn-2)){$maxmatn=$materialno+12;}
		if($materialno<$maxmatn){
			for($matn=$materialno;$matn<$maxmatn;$matn++){
				$Order['Materials']['Material'][]=$Materialblank;
				}
			  }

		while(list($index,$Material)=each($Order['Materials']['Material'])){
			$matn=$index+1;
?>
		  <tr>
			<td><?php print $matn;?></td>
			<td>
<?php 
			//$tab=xmlelement_input($Material['Type'],$matn,$tab,$book);
			$listlabel='';
			$listid='materialtype'.$matn; $listname='materialtype'.$matn; 
			${'sel'.$listname}=$Material['Type']['value_db']; $cattype='mat';
			include('scripts/list_category.php');
			unset(${'sel'.$listname});
?>
			</td>
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
	    <input type="hidden" name="maxmatn" value="<?php print $maxmatn;?>">
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>