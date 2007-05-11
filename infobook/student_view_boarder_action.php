<?php
/*****									student_view_boarder_action.php
 *
 */

$accid=$_POST['id_db'];
$boarder=$_POST['boarder'];
$action='student_view_boarder.php';
include('scripts/sub_action.php');

if($sub=='Submit'){
	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);
	$neededperm='w';
	include('scripts/perm_action.php');

	mysql_query("UPDATE info SET boarder='$boarder' WHERE student_id='$sid'");

	if($boarder!='' and $boarder!='N'){
		if($accid=='-1'){
			mysql_query("INSERT INTO accomodation SET student_id='$sid'");
			$accid=mysql_insert_id();
			}
		$Stay=fetchStay();
		reset($Stay);
		while(list($key,$val)=each($Stay)){
			if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
				$field=$val['field_db'];
				$inname=$field;
				$inval=clean_text($_POST[$inname]);
				if($val['table_db']=='accomodation'){
					mysql_query("UPDATE accomodation SET
							$field='$inval' WHERE id='$accid'");
					}
				}
			}
		set_accomodation($sid,$accid);
		}
	elseif($accid!='-1' and ($boarder=='N' or $boarder=='')){
		mysql_query("DELETE FROM accomodation WHERE id='$accid' LIMIT 1");
		}

	}

	include('scripts/redirect.php');
?>
