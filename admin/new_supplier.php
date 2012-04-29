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
<?php
	if($supid==-1){print '<label>'.get_string('newsupplier',$book).'</label>';}
	else{print '<label>'.get_string('supplier',$book). '</label>' .$Supplier['Name']['value'];}

?>
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


<?php

if($supid>-1){



	print '<fieldset id="viewcontent" class="center divgroup">';
	print '<table class="center listmenu">';
	$headrow='<th></th>';
	$firstrow='<th>Budget Year</th>';
	$secondrow='<th>Calendar Year</th>';
	for($c=2;$c>=0;$c--){
		if($c==2){}
		$year=date('Y')-$c;
		$lastyear=date('Y')-$c-1;

		$startdate=$lastyear.'-'.$CFG->budget_endmonth.'-01';
		$enddate=$year.'-'.$CFG->budget_endmonth.'-31';
		$amount=get_supplier_projected($supid,$startdate,$enddate);
		$firstrow.='<td> '.display_money($amount).'</td>';

		$startdate=$year.'-01-01';
		$enddate=$year.'-12-31';
	trigger_error($lastyear. ' '.$year,E_USER_WARNING);
		$amount=get_supplier_projected($supid,$startdate,$enddate);
		$secondrow.='<td> '.display_money($amount).'</td>';
		$headrow.='<th>'.$year.'</th>';
		}

	print '<tr>'.$headrow.'</tr>';
	print '<tr>'.$firstrow.'</tr>';
	print '<tr>'.$secondrow.'</tr></table>';

	print '<table class="center listmenu">';
	print '<th>'.get_string('total',$book).'</th><th>'.get_string('quantity',$book).'</th><th style="width:60%;">'.get_string('detail',$book).'</th><th>'.get_string('unitcost',$book).'</th>';

	$year--;
	$year--;
	$startdate=$year.'-'.$CFG->budget_endmonth.'-01';
	$enddate=date('Y-m-d');
	trigger_error($startdate. ' '.$enddate,E_USER_WARNING);
	$d_r=mysql_query("SELECT DISTINCT refno FROM ordermaterial JOIN orderorder ON orderorder.id=ordermaterial.order_id 
						WHERE orderorder.supplier_id='$supid' AND 
							orderorder.entrydate<='$enddate' AND orderorder.entrydate>'$startdate';");
	$rows=array();
	while($r=mysql_fetch_array($d_r,MYSQL_ASSOC)){
		$refno=$r['refno'];
		$d_m=mysql_query("SELECT SUM(m.quantity*m.unitcost) AS total, SUM(m.quantity) AS number, m.unitcost, m.detail, m.refno 
						FROM ordermaterial AS m JOIN orderorder AS o ON o.id=m.order_id 
						WHERE m.refno='$refno' AND o.supplier_id='$supid' AND 
							o.entrydate<='$enddate' AND o.entrydate>'$startdate';");
		$material=mysql_fetch_array($d_m,MYSQL_ASSOC);
		if($material['refno']==''){$material['detail']='Other items.';$material['unitcost']='';}
		$rows[$material['total']]='<tr><td>'.display_money($material['total']).'</td><td>'.$material['number'].'</td><td>'.$material['detail'].'  ('.$material['refno'].')</td><td>'.display_money($material['unitcost']).'</td>';
		}

	krsort($rows,SORT_NUMERIC);
	foreach($rows as $row){
		print $row;
		}
	print '</table>';
	print '</fieldset>';

	}


?>

  </div>