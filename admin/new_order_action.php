<?php
/**			  					new_order_action.php
 */

$action='orders_list.php';
$cancel='orders_list.php';
$budid=$_POST['budid'];
$maxmatn=$_POST['matn'];
$supid=$_POST['supid'];
$action_post_vars=array('budid');

include('scripts/sub_action.php');

if($sub=='Submit'){

	$yearcode=-1;
	$Order=fetchOrder();
	mysql_query("INSERT INTO orderorder SET budget_id='$budid',
						supplier_id='$supid', teacher_id='$tid';");
	$ordid=mysql_insert_id();
	reset($Order);
	while(list($index,$val)=each($Order)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			//trigger_error(''.$val['value'].''.$val['field_db'],E_USER_WARNING);
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='orderorder'){
				mysql_query("UPDATE orderorder SET $field='$inval' WHERE id='$ordid'");
				}
			}
		}

	$Material=fetchMaterial();
	for($matn=1;$matn<$maxmatn;$matn++){
		if(isset($_POST['detail'.$matn]) and $_POST['detail'.$matn]!=''){
			reset($Material);
			mysql_query("INSERT INTO ordermaterial SET order_id='$ordid', entryn='$matn';");
			while(list($index,$val)=each($Material)){
				if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
					$field=$val['field_db'];
					$inname=$field. $matn;
					$inval=clean_text($_POST[$inname]);
					if($val['table_db']=='ordermaterial'){
						mysql_query("UPDATE ordermaterial SET $field='$inval'
								WHERE entryn='$matn' AND order_id='$ordid'");
						}
					}
				}
			}
		}
	$result[]=get_string('neworderadded',$book);
	}


include('scripts/results.php');
include('scripts/redirect.php');
?>
