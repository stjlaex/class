<?php
/**                                  orders_list.php   
 * 
 * Works in two scenarios. Either called with budid set meaning it is
 * for a single budget and new orders can be placed. Or it is the result
 * of a search and ordernumber and orderstatus will be used to list
 * relevant orders, budid=-1 is set because this transcends budgets
 * and no orders can be placed from this page.
 *
 */

$action='orders_list_action.php';

include('scripts/sub_action.php');

if(isset($_GET['budgetyear'])){$budgetyear=$_GET['budgetyear'];}
if(isset($_POST['budgetyear']) and $_POST['budgetyear']!=''){$budgetyear=$_POST['budgetyear'];}

if((isset($_POST['ordernumber']) and $_POST['ordernumber']!='') or 
   (isset($_POST['orderstatus']) and $_POST['orderstatus']!='') or 
   (isset($_POST['ordersupid']) and $_POST['ordersupid']!='')){
		/* These are the three search terms */
	$ordersupid=$_POST['ordersupid'];
	$ordernumber=$_POST['ordernumber'];
	$orderstatus=$_POST['orderstatus'];
	$orders=(array)list_orders($ordernumber,$orderstatus,$ordersupid);
	$extrabuttons=array();
	$budid=-1;
	$colspan=7;
	}
elseif(isset($_POST['invoicenumber']) and $_POST['invoicenumber']!=''){
	$invoicenumber=$_POST['invoicenumber'];
	$orders=(array)list_invoice_orders($invoicenumber);
	//$orders=(array)list_orders('---'.$ordid,'','');
	$extrabuttons=array();
	$budid=-1;
	$colspan=7;
	}
else{
	if(isset($_POST['budid'])){$budid=$_POST['budid'];}
	if(isset($_GET['budid'])){$budid=$_GET['budid'];}
	$orders=list_budget_orders($budid);
	$extrabuttons['neworder']=array('name'=>'current','value'=>'new_order.php');
	$colspan=6;
	$perms=get_budget_perms($budid);
	$Budget=fetchBudget($budid);
	$balance=get_budget_projected($budid);
	}


two_buttonmenu($extrabuttons,$book);

	if($budid!=-1){
?>
  <div id="heading">
	<label><?php print_string('budget',$book);?></label>
<?php	print $Budget['Name']['value'].' ';?>
  </div>
<?php
		}
?>
  <div id="viewcontent" class="content">


<?php
		if(isset($balance)){
			if($balance<(0.05*$Budget['Limit']['value']) or $balance<20){
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
?>

	  <fieldset class="right">
		<div class="right">
			<label><?php print get_string('projectedbalance','admin').': ';?></label>
<?php print $balance. ' '.displayEnum(0,'currency');?>
		</div>
	  </fieldset>
<?php
			}
?>



  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string('orders',$book);?></caption>
		<thead>
		  <tr>
			<th></th>
			<th><?php print_string('date');?></th>
			<th><?php print_string('supplier',$book);?></th>
			<th><?php print_string('ordernumber',$book);?></th>
			<th><?php print_string('lodged',$book);?></th>
<?php
  	if($budid==-1){ print '<th>'.get_string('budget',$book).'</th>';}
?>
			<th>&nbsp;</th>
		  </tr>
		</thead>
<?php

	if(isset($_POST['startno'])){$startno=$_POST['startno'];}
	else{$startno=0;}
	$totalno=sizeof($orders);
	$nextrowstep=15;
	if($startno>$totalno){$startno=$totalno-$nextrowstep;}
	if($startno<0){$startno=0;}
		$endno=$startno+$nextrowstep;
		if($endno>$totalno){$endno=$totalno;}
		for($entryno=$startno;$entryno<$endno;$entryno++){
			$order=$orders[$entryno];
			$actionbuttons=array();
			$imagebuttons=array();
			$imagebuttons['clicktoprint']=array('name'=>'print',
												'value'=>'order_print.php',
												'title'=>'print');
			$ordid=$order['id'];
			$Order=(array)fetchOrder($ordid);
			$status=$Order['Status']['value'];
			$Supplier=(array)$Order['Supplier'];
			/* If budid=-1 its a search result. Only want those with
			 * permissions to be able to see the entry. Office are allowed to
			 * see all entries but they see the overall budget status. 
			 */
			if($budid==-1){
				$perms=get_budget_perms($Order['Budget']['value_db']);
				}
			if($perms['r']==1){
				$rown=0;
				if($status=='closed'){$styleclass='';}
				elseif($status=='lodged'){$styleclass='class="midlite"';}
				elseif($status=='authorised'){$styleclass='class="hilite"';}
				elseif($status=='placed' or $status=='process'){$styleclass='class="golite"';}
				else{$styleclass=' class="nolite"';}
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" 
							id="<?php print $entryno.'-'.$rown++;?>">
			<th>&nbsp;</th>
			<td><?php print display_date($Order['Date']['value']);?></td>
			<td><?php print $Supplier['Name']['value'];?></td>
			<td><?php print $Order['Reference']['value'];?></td>
			<td><?php print $Order['Lodged']['value'];?></td>
			  <?php if($budid==-1){ print '<td>'.$Order['Budget']['value'].'</td>';}?>
			<td	<?php print $styleclass;?>> 
			   <?php if($status!='closed'){print_string($status,$book);}?> &nbsp;
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="<?php print $colspan;?>">
<?php 
				if($Order['Detail']['value']!=''){
					print '<div class="center nolite">'.$Order['Detail']['value'].'</div>';
					}
?>
				<ul class="listmenu">
<?php
				 $sumcost=0;
				 foreach($Order['Materials']['Material'] as $Material){
					 if($Material['invoice_id_db']==0){$listclass='positive';}
					 else{$listclass='lowlite';}
					 print '<li class="'.$listclass.'">'.$Material['Detail']['value'].' &nbsp; - '
							 .$Material['SupplierReference']['value'].' &nbsp; (' 
					 .$Material['Quantity']['value']. 
					 ' x '. $Material['Unitcost']['value']. ' '. 
					 displayEnum($Order['Currency']['value'],$Order['Currency']['field_db']). 
					 ')</li>';
					 $sumcost+=$Material['Quantity']['value']*$Material['Unitcost']['value'];
					}
?>
				</ul>
				<div class="center nolite">
<?php
				 print 'Projected total cost = '.$sumcost. 
				   ' '.displayEnum($Order['Currency']['value'],$Order['Currency']['field_db']);
?>
				</div>

<?php
					/* Once an order is authorised it is too late to
					amend unless you have extra priviliges*/
					if(($status=='lodged' and $perms['r']==1) or (($status=='authorised' 
						or $status=='process') and $perms['w']==1)){
						$actionbuttons['edit']=array('name'=>'process','value'=>'edit');
						all_extrabuttons($actionbuttons,
										 $book,'clickToAction(this)','class="rowaction" ');
						}

?>
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="<?php print $colspan;?>">
<?php
				if(sizeof($Order['Actions'])>0){
					while(list($index,$Action)=each($Order['Actions']['Action'])){
						if($Action['Category']['value_db']==3 and isset($Action['Invoice'])){
							$Invoice=$Action['Invoice'];
?>
				<div class="center divgroup">
				  <?php 
							if($Invoice['Credit']['value']==1){
								print get_string('creditnote','admin').' '.$Invoice['Reference']['value'];
								}
							else{
								print get_string('invoice','admin').' '.$Invoice['Reference']['value'];
								}
							print ': '.$Invoice['DebitCost']['value']. 
							' '. displayEnum($Invoice['Currency']['value'],$Invoice['Currency']['field_db']);
							print ' '.display_date($Invoice['Date']['value']). 
							' - '.$Action['Teacher']['value']. 
							' ('.display_date($Action['Date']['value']).') '.
							$Action['Detail']['value'];
?>
				</div>
<?php
							}
						else{
?>
				<p>
				  <label><?php print_string($Action['Category']['value'],$book);?></label>
					<?php print ' '.display_date($Action['Date']['value']).' - ';?>
					<?php print $Action['Teacher']['value']. ' ';?>
					<?php print $Action['Detail']['value'];?>
				</p>
<?php
							}
						}
					}

				 /* Now the buttons to progress the order to the next status.*/
				$actionbuttons=array();
  				if($status!='closed'){
					$orderactions=array();
					if($status=='lodged' and $perms['x']==1){
						$orderactions[]='cancel';
						$orderactions[]='authorise';
						}
					elseif($status=='authorised' and $perms['w']==1){
						$orderactions[]='cancel';
						$orderactions[]='process';
						$orderactions[]='place';
						}
					elseif($status=='process' and $perms['w']==1){
						$orderactions[]='cancel';
						$orderactions[]='process';
						$orderactions[]='place';
						}
					elseif($status=='placed' and $perms['w']==1){
						$orderactions[]='cancel';
						$orderactions[]='delivery';
						$orderactions[]='place';
						}
					elseif($status=='delivered' and $perms['w']==1){
						$actionbuttons['close']=array('name'=>'process','value'=>'close');
						$orderactions[]='delivery';
						}
					elseif($status=='cancelled' and $perms['w']==1){
						$orderactions[]='reopen';
						}

					if(sizeof($orderactions)>0){
						if($orderactions[0]=='cancel'){
							$imagebuttons['clicktodelete']=array('name'=>'process',
																 'value'=>array_shift($orderactions),
																 'title'=>'cancelled');
							}
						while(list($indexoa,$orderaction)=each($orderactions)){
							$actionbuttons[$orderaction]=array('name'=>'process',
														   'value'=>$orderaction);
							}
?>
				<label>
				  <?php print get_string($orderaction,$book).' '.get_string('note',$book);?>
				</label>
				<input style="width:30em;" name="detail<?php print $ordid;?>" value="" />
<?php
						rowaction_buttonmenu($imagebuttons,$actionbuttons,$book);
						}
					}
				elseif($perms['x']==1){
					$actionbuttons['reopen']=array('name'=>'process',
												   'value'=>'reopen');
					rowaction_buttonmenu($imagebuttons,$actionbuttons,$book);
					}
?>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;"><?php xmlechoer('Order',$Order);?></div>
		</tbody>
<?php
				}
			}
?>
		<tr>
		<th colspan="4">&nbsp;</th>
		<th colspan="2">
<?php 
			$dstartno=$startno+1;
			print 'Show '. $dstartno.' - '.$endno.' of '.$totalno;
			$buttons=array();
			if($startno>0){
				$buttons['<']=array('title'=>'previous','name'=>'nextrow','value'=>'minus');
				}
			if($endno<$totalno){
				$buttons['>']=array('title'=>'next','name'=>'nextrow','value'=>'plus');
				}
			all_extrabuttons($buttons,'admin','processContent(this)')
?>
		</th>
		</tr>
	  </table>
	</div>
<?php
	if($budid==-1){
?>
	<input type="hidden" name="ordernumber" value="<?php print $ordernumber;?>" />
	<input type="hidden" name="orderstatus" value="<?php print $orderstatus;?>" />
	<input type="hidden" name="ordersupid" value="<?php print $ordersupid;?>" />
<?php
		}
?>

	<input type="hidden" name="startno" value="<?php print $startno;?>" />
	<input type="hidden" name="nextrowstep" value="<?php print $nextrowstep;?>" />
	<input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>" />
	<input type="hidden" name="budid" value="<?php print $budid;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>
