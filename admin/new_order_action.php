<?php
/**			  					new_order_action.php
 */

$action='orders_list.php';
$cancel='orders_list.php';
if(isset($_POST['ordid'])){$ordid=$_POST['ordid'];}else{$ordid=-1;}
$budid=$_POST['budid'];
$maxmatn=$_POST['maxmatn'];
$supid=$_POST['supid'];
$budgetyear=$_POST['budgetyear'];
$action_post_vars=array('budid','budgetyear');

include('scripts/sub_action.php');

if($sub=='Submit'){

	$yearcode=-1;
	$Order=fetchOrder();
	if($ordid==-1){
		mysql_query("INSERT INTO orderorder SET budget_id='$budid',
						supplier_id='$supid', teacher_id='$tid';");
		$ordid=mysql_insert_id();
		}
	else{
		mysql_query("UPDATE orderorder SET supplier_id='$supid' WHERE id='$ordid';");
		mysql_query("DELETE FROM ordermaterial WHERE order_id='$ordid';");	
		}
	reset($Order);
	while(list($index,$val)=each($Order)){
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
	 */
	for($matn=1;$matn<=$maxmatn;$matn++){
		if(isset($_POST['quantity'.$matn]) and
		   $_POST['quantity'.$matn]!='' and $_POST['quantity'.$matn]!='0'){
			$entryn++;
			mysql_query("INSERT INTO ordermaterial SET order_id='$ordid', entryn='$entryn';");
			reset($Material);
			while(list($index,$val)=each($Material)){
				if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
					$field=$val['field_db'];
					$inname=$field. $matn;
					$inval=clean_text($_POST[$inname]);
					if($val['table_db']=='ordermaterial'){
						mysql_query("UPDATE ordermaterial SET $field='$inval'
								WHERE entryn='$entryn' AND order_id='$ordid'");
						}
					}
				}
			}
		}
	}

include('scripts/redirect.php');
?>
