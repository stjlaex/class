<?php
/**                                  orders_list.php    
 */

$action='orders_list_action.php';

include('scripts/sub_action.php');

if(isset($_GET['budgetyear'])){$budgetyear=$_GET['budgetyear'];}
if(isset($_POST['budgetyear']) and $_POST['budgetyear']!=''){$budgetyear=$_POST['budgetyear'];}

if(isset($_POST['ordernumber']) or isset($_POST['orderstatus'])){
	$ordernumber=$_POST['ordernumber'];
	$orderstatus=$_POST['orderstatus'];
	$orders=list_orders($ordernumber,$orderstatus);
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
<?php  	if($budid==-1){ print '<th>'.get_string('budget',$book).'</th>';}?>
			<th>&nbsp;</th>
		  </tr>
		</thead>
<?php
		while(list($entryno,$order)=each($orders)){
			$actionbuttons=array();
			$ordid=$order['id'];
			$Order=(array)fetchOrder($ordid);
			$status=$Order['Status']['value'];
			$Supplier=(array)$Order['Supplier'];
			if($budid==-1){$perms=get_budget_perms($Order['Budget']['value_db']);}
			if($perms['r']==1){
				$rown=0;
				if($status=='closed'){$styleclass='';}
				elseif($status=='lodged'){$styleclass='class="midlite"';}
				elseif($status=='authorised'){$styleclass='class="hilite"';}
				elseif($status=='placed'){$styleclass='class="golite"';}
				else{$styleclass=' class="nolite"';}
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" 
							id="<?php print $entryno.'-'.$rown++;?>">
			<th>&nbsp</th>
			<td><?php print display_date($Order['Date']['value']);?></td>
			<td><?php print $Supplier['Name']['value'];?></td>
			<td><?php print $Order['Reference']['value'];?></td>
			<td><?php print $Order['Lodged']['value'];?></td>
			  <?php if($budid==-1){ print '<td>'.$Order['Budget']['value'].'</td>';}?>
			<td	<?php print $styleclass;?>>&nbsp 
			  <?php if($status!='closed'){print_string($status,$book);}?>
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="<?php print $colspan;?>">
				<p>
<?php
				 while(list($index,$Material)=each($Order['Materials']['Material'])){
					 print ''.$Material['Detail']['value'].' - ' 
					 .$Material['Quantity']['value']. 
					 ' ('.$Material['Unitcost']['value']. ' '. 
					 displayEnum($Order['Currency']['value'],$Order['Currency']['field_db']). 
					 ')<br />';
					}
?>
				</p>
<?php
					if($status=='lodged' and $perms['w']==1){
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
				while(list($index,$Action)=each($Order['Actions']['Action'])){
?>
				<p>
				  <label><?php print_string($Action['Category']['value'],$book);?></label>
				  <?php print ' '.display_date($Action['Date']['value']).' - ';?>
				  <?php print $Action['Teacher']['value']. ' ';?>
				  <?php print $Action['Detail']['value'];?>
				</p>
<?php
					}

				if($status!='closed'){
					$orderaction='';
					$actionbuttons=array();
					if($status=='lodged' and $perms['x']==1){
						$orderaction='authorise';
						}
					elseif($status=='authorised' and $perms['w']==1){$orderaction='place';}
					elseif($status=='placed' and $perms['w']==1){$orderaction='delivery';}
					elseif($status=='delivered' and $perms['w']==1){
						$actionbuttons['close']=array('name'=>'process','value'=>'close');
						$orderaction='delivery';
						}

					if($orderaction!=''){
						$actionbuttons[$orderaction]=array('name'=>'process',
														   'value'=>$orderaction);
?>
				<label>
				  <?php print get_string($orderaction,$book).' '.get_string('note',$book);?>
				</label>
				<input style="width:30em;" name="detail<?php print $ordid;?>" value="" />
<?php
						all_extrabuttons($actionbuttons,
										 $book,'clickToAction(this)','class="rowaction" ');
						}
					}
?>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
<?php
					  xmlechoer('Order',$Order);
?>
		  </div>
		</tbody>
<?php
				}
			}
?>
	  </table>
	</div>
	
	<input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>" />
	<input type="hidden" name="budid" value="<?php print $budid;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>

