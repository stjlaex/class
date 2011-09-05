<?php
/**                                  inventory_list.php    
 */

$action='inventory_list_action.php';
if(isset($_POST['budid'])){$budid=$_POST['budid'];}else{$budid=-1;}
$budgetyear=$_POST['budgetyear'];

include('scripts/sub_action.php');


two_buttonmenu($book);
?>
  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
<?php
		$entryno=0;
		$d_sup=mysql_query("SELECT id, name FROM ordersupplier 
		   		WHERE inactive='0' AND specialaction='2' ORDER BY name;");
		while($supplier=mysql_fetch_array($d_sup)){
			$supid=$supplier['id'];
			$Supplier=(array)fetchSupplier($supid);
			$entryno++;
			$rown=0;
			if($budid==-1){
			$d_bud=mysql_query("SELECT DISTINCT budget_id AS id FROM orderorder
		   		WHERE supplier_id='$supid' ORDER BY budget_id;");
				}
			else{
				$d_bud=mysql_query("SELECT DISTINCT budget_id AS id FROM orderorder
		   		WHERE supplier_id='$supid' AND budget_id='$budid';");
				}
?>

	  <table class="listmenu">
			<caption colspan="3"><?php print $Supplier['Name']['value'];?></caption>
<?php
			while($budget=mysql_fetch_array($d_bud)){
				$thisbudid=$budget['id'];
				$Budget=fetchBudget($thisbudid);
				$d_c=mysql_query("SELECT DISTINCT ordermaterial.catalogue_id AS id FROM ordermaterial
						JOIN orderorder ON orderorder.id=ordermaterial.order_id 
						WHERE orderorder.supplier_id='$supid' AND orderorder.budget_id='$thisbudid';");
?>
		  <tr>
			<th>&nbsp</th>
			<th colspan="3"><?php print $Budget['Name']['value'];?></th>
		  </tr>
<?php
			   while($c=mysql_fetch_array($d_c)){
				   $Material=fetchCatalogueMaterial($c['id'],$thisbudid);
?>
		  <tr>
			<td>
			</td>
			<td>
			<?php print $Material['SupplierReference']['value']; ?>
			</td>
			<td>
			  <a href="admin.php?current=inventory_distribution.php&cancel=inventory_list.php&budid=<?php print $thisbudid;?>&catid=<?php print $c['id'];?>">
			<?php print $Material['Detail']['value']; ?>
			  </a>
			</td>
			<td>
			<?php print $Material['Stock']['value']; ?>
			</td>
		  </tr>
<?php
				   }
				}
?>
	  </table>
	  <br />
<?php
			}
?>
	</div>
	
	<input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>

