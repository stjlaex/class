<?php
/**			  					new_order_action.php
 */

$action='orders_list.php';
$cancel='orders_list.php';
if(isset($_POST['ordid'])){$ordid=$_POST['ordid'];}else{$ordid=-1;}
$budid=$_POST['budid'];
$maxmatn=$_POST['maxmatn'];
if(isset($_POST['supid'])){$supid=$_POST['supid'];}else{$supid=-1;}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid=-1;}
$budgetyear=$_POST['budgetyear'];
$action_post_vars=array('budid','budgetyear','supid','ordid','catid');

include('scripts/sub_action.php');

if($sub=='Submit' and $supid>0){

	$yearcode=-1;
	$Order=fetchOrder();
	$d_sup=mysql_query("SELECT specialaction FROM ordersupplier WHERE id='$supid';");
	$specialaction=mysql_result($d_sup,0);/* value=2 for catalogue items */

	if($ordid==-1){
		mysql_query("INSERT INTO orderorder SET budget_id='$budid',
						supplier_id='$supid', teacher_id='$tid';");
		$ordid=mysql_insert_id();
		}
	else{
		mysql_query("UPDATE orderorder SET supplier_id='$supid' WHERE id='$ordid';");
		mysql_query("DELETE FROM ordermaterial WHERE order_id='$ordid';");
		}

	foreach($Order as $val){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='orderorder'){
				mysql_query("UPDATE orderorder SET $field='$inval' WHERE id='$ordid';");
				}
			}
		}

	$Material=fetchMaterial();
	$entryn=0;
	/*
	 * The index in the db is entryn which is not neccessarily the
	 * same as matn if entries have been deleted
	 * and must avoid blanks in entryn
	 * Only accept entries where both unitcost and quantity are not zero.
	 */
	for($matn=1;$matn<=$maxmatn;$matn++){
		if(isset($_POST['quantity'.$matn]) and isset($_POST['unitcost'.$matn]) and
		   $_POST['quantity'.$matn]!='' and $_POST['unitcost'.$matn]!='' 
		   and $_POST['quantity'.$matn]!='0' and $_POST['unitcost'.$matn]!='0'){
			$entryn++;

			mysql_query("INSERT INTO ordermaterial SET order_id='$ordid', entryn='$entryn';");
			$mat=array();
			foreach($Material as $val){
				if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
					$field=$val['field_db'];
					$inname=$field. $matn;
					$inval=clean_text($_POST[$inname]);
					if($val['table_db']=='ordermaterial'){
						mysql_query("UPDATE ordermaterial SET $field='$inval'
								WHERE entryn='$entryn' AND order_id='$ordid';");
						}
					$mat[$field]=$inval;
					}
				}

			/* If the catalogue id is already set then don't change otherwise set to new value. */
			if($_POST["catalogue_id_db$matn"]>0){$incatid=$_POST["catalogue_id_db$matn"];}
			else{$incatid=$catid;}
			if($specialaction==2){
				trigger_error('SPECIAL: '.$incatid.' : '.$catid,E_USER_WARNING);
				$mat['catalogue_id_db']=$incatid;
				$mat['supplier_id_db']=$supid;
				$mat['currency']=$_POST['currency'];
				$incatid=updateCatalogueMaterial($mat);
				}

			$ininvid=$_POST["invoice_id_db$matn"];
			mysql_query("UPDATE ordermaterial SET catalogue_id='$incatid', invoice_id='$ininvid'
								WHERE entryn='$entryn' AND order_id='$ordid';");
			}
		}
	}
elseif($supid>0){
	if($ordid>0){
		mysql_query("UPDATE orderorder SET supplier_id='$supid' WHERE id='$ordid';");
		}
	$action='new_order.php';
	}

include('scripts/redirect.php');
?>
