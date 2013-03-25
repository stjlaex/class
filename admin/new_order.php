<?php
/**									new_order.php
 *
 */

$action='new_order_action.php';
$action_post_vars=array('budid');
$maxmatn=12;
include('scripts/sub_action.php');

if(isset($_POST['ordid'])){$ordid=$_POST['ordid'];}else{$ordid=-1;}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid=-1;}
$Order=fetchOrder($ordid);
if($ordid==-1){
	$budid=$_POST['budid'];
	if(isset($_POST['supid'])){$supid=$_POST['supid'];}else{$supid=-1;}
	if(isset($_POST['catlogid'])){$catlogid=$_POST['catlogid'];}else{$catlogid=-1;}
	}
else{
	/* Editing an existing order.*/
	$budid=$Order['Budget']['value_db'];
	$supid=$Order['Supplier']['id_db'];
	$catlogid=$Order['Catalogue']['value_db'];
	}
$Budget=fetchBudget($budid);
$budgetyear=get_budgetyear($Budget['YearCode']['value']);
$Materialblank=fetchMaterial();
$perms=get_budget_perms($budid);
$balance=get_budget_projected($budid);

three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('budget',$book);?></label>
	<?php print $Budget['Name']['value'];?>
  </div>

  <div class="content" id="viewcontent">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="right">
		<div class="right">
			<label><?php print get_string('projectedbalance','admin').': ';?></label>
			<?php print display_money($balance);?>
		</div>
	  </fieldset>
<?php
/**
 * Only allow a new order or an order to be amended if the balance is
 * above zero. Allow an exception for those who can authorise the
 * budget.
 */
if(isset($CFG->budget_lock) and $CFG->budget_lock>0 and ($balance<(0.01*$Budget['Limit']['value']) or $balance<$CFG->budget_lock)){
	$locked=true;
	}
else{
	$locked=false;
	}



if($locked and $perms['x']!=1){
?>
	<fieldset class="left">
	<div class="center">
	<p class="warn">
	<?php print_string('balancetoolow',$book);?>
	</p>
	</div>
	</fieldset>
<?php
	}
else{
?>
	  <div class="left">
		<?php $tab=xmlarray_form($Order,'','neworder',$tab,$book); ?>
	  </div>

	  <div class="right">
		<table class="listmenu">
		  <caption><?php print_string('supplier',$book);?></caption>
		  <tr>
			<td>
<?php
		/**
		 * The specialaction=1 is for suppliers who are probably not
		 * 'suppliers' ie. petty cash or photocopy and which will bypass
		 * the action tracking for deilvery and invoices. But still
		 * need to be authorised. specialaction=2 are similar but for catalogue orders
		 */
		if($perms['x']==1 or $_SESSION['role']=='office'){
			$d_sup=mysql_query("SELECT ordersupplier.id, ordersupplier.name FROM ordersupplier 
					WHERE inactive='0' ORDER BY specialaction, name;");
			}
		else{
			$d_sup=mysql_query("SELECT ordersupplier.id, ordersupplier.name FROM ordersupplier 
					WHERE inactive='0' AND (specialaction='0' OR specialaction='2') ORDER BY specialaction, name;");
			}
		$listlabel='supplier';
		$selsupid=$supid;
		$listname='supid';
		$onchange='yes';
		//$required='yes';
		include('scripts/set_list_vars.php');
		list_select_db($d_sup,$listoptions,$book);
		unset($listoptions);
?>
			</td>
		  </tr>
<?php
			/* TODO: limit catalogue by year. */
		$d_c=mysql_query("SELECT DISTINCT id, detail AS name FROM ordercatalogue 
								WHERE catalogue_id='0' AND supplier_id='$supid' ORDER BY subject_id, detail;");
		if(mysql_num_rows($d_c)>0){
			/* Give a choice of catalogue references. */
?>
		  <tr>
			<td>
<?php
			$listlabel='catalogue';
			$selcatlogid=$catlogid;
			$listname='catlogid';
			$onchange='yes';
			$liststyle='width:80%;';
			include('scripts/set_list_vars.php');
			list_select_db($d_c,$listoptions,$book);
			unset($listoptions);
?>
			</td>
		  </tr>
<?php
			}
?>
		</table>
	  </div>


<?php
		if(!empty($catlogid) and $catlogid>0){
			/* List all catalogue items for this supplier. */
			$d_s=mysql_query("SELECT id, detail AS name FROM ordercatalogue 
								WHERE catalogue_id='$catlogid' AND supplier_id='$supid' ORDER BY subject_id, detail;");
			if(mysql_num_rows($d_s)>0){
?>
	  <div class="right">
		<table class="listmenu">
		  <caption><?php print_string('catalogue',$book);?></caption>
		  <tr>
			<td>
<?php
				$listlabel='item';
				$selcatid=$catid;
				$listname='catid';
				$onchange='yes';
				$liststyle='width:80%;';
				include('scripts/set_list_vars.php');
				list_select_db($d_s,$listoptions,$book);
				unset($listoptions);
				$specialaction=2;
?>
			</td>
		  </tr>
		</table>
	  </div>
<?php
				$maxmatn=0;
				$Order['Materials']['Material'][]=fetchCatalogueMaterial($catid);
				}
			}
?>

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
		if($materialno>($maxmatn-2)){$maxmatn=$materialno+$maxmatn;}
		if($materialno<$maxmatn){
			for($matn=$materialno;$matn<$maxmatn;$matn++){
				$Order['Materials']['Material'][]=$Materialblank;
				}
			  }

		foreach($Order['Materials']['Material'] as $index => $Material){
			$matn=$index+1;
?>
		  <tr>
			<td><?php print $matn;?></td>
			<td>
<?php 
			//$tab=xmlelement_input($Material['Type'],$matn,$tab,$book);
			$listlabel='';
			$listid='materialtype'.$matn; 
			$listname='materialtype'.$matn; 
			${'sel'.$listname}=$Material['Type']['value_db']; $cattype='mat';
			include('scripts/list_category.php');
			//unset(${'sel'.$listname});
			if($Material['catalogue_id_db']>0){$selcatid=$Material['catalogue_id_db'];}
			else{$selcatid=$catid;}
?>
			<input type="hidden" name="catalogue_id_db<?php print $matn;?>" value="<?php print $selcatid;?>">
			<input type="hidden" name="invoice_id_db<?php print $matn;?>" value="<?php print $Material['invoice_id_db'];?>">
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

<?php
	}
?>
	    <input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>">
	    <input type="hidden" name="budid" value="<?php print $budid;?>">
	    <input type="hidden" name="ordid" value="<?php print $ordid;?>">
	    <input type="hidden" name="maxmatn" value="<?php print $maxmatn;?>">
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>