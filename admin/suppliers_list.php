<?php
/**                                  suppliers_list.php    
 */

$action='suppliers_list_action.php';

$budgetyear=$_POST['budgetyear'];

include('scripts/sub_action.php');

$extrabuttons['newsupplier']=array('name'=>'current','value'=>'new_supplier.php');

two_buttonmenu($extrabuttons,$book);
?>
  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string('suppliers',$book);?></caption>
		<thead>
		  <tr>
			<th></th>
			<th><?php print_string('supplier',$book);?></th>
		  </tr>
		</thead>
<?php
		$entryno=0;
		$d_sup=mysql_query("SELECT id FROM ordersupplier ORDER BY specialaction, name;");
		while($supplier=mysql_fetch_array($d_sup)){
			$supid=$supplier['id'];
			$Supplier=fetchSupplier($supid);
			$entryno++;
			$actionbuttons=array();
			$rown=0;
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus <?php if($Supplier['Inactive']['value']=='1'){print 'lowlite';} ?>" 
			  onClick="clickToReveal(this)" id="<?php print $entryno.'-'.$rown++; ?>">
			<th>&nbsp</th>
			<td>
			  <a href="admin.php?current=new_supplier.php&cancel=suppliers_list.php&supid=<?php print $supid;?>&budgetyear=<?php print $budgetyear;?>">
			  <?php 
			  print $Supplier['Name']['value'];
			  ?>
			  </a>
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="2">
				<p>
<?php 
			  print $Supplier['Address']['Street']['value'].', '. 
				  $Supplier['Address']['Neighbourhood']['value'].', '.
				  $Supplier['Address']['Town']['value'].', '. $Supplier['Address']['Country']['value'] 
				  .'. '. $Supplier['Address']['Postcode']['value'];
?>
				</p>
			</td>
		  </tr>
		</tbody>
<?php
			}
?>
	  </table>
	</div>
	
	<input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>

