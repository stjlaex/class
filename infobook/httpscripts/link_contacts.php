<?php
/**                    httpscripts/link_contacts.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

$tolinkgid=$xmlid;
if(isset($_GET['linkedtogid']) and $_GET['linkedtogid']!=''){$linkedtogid=$_GET['linkedtogid'];}else{$linkedtogid=-1;}

$Contact=fetchContact($gidsid=array('guardian_id'=>$linkedtogid));
$ContactToLink=fetchContact($gidsid=array('guardian_id'=>$tolinkgid));

foreach($Contact as $param=>$val){
	$returnXML[$param]=$ContactToLink[$param];
	if(isset($val['table_db']) and $val['table_db']=='guardian' and $Contact[$param]['value']=='' and $ContactToLink[$param]['value']!=''){
		$field=$val['field_db'];
		$newval=$ContactToLink[$param]['value'];
		mysql_query("UPDATE guardian SET $field='$newval' WHERE id='$linkedtogid'");
		}
	}
if(isset($ContactToLink['Phones']) and count($ContactToLink['Phones'])>0){
	if(isset($Contact['Phones']) and count($Contact['Phones'])>0){
		foreach($Contact['Phones'] as $Phone){
			foreach($ContactToLink['Phones'] as $PhoneToLink){
				if($PhoneToLink['PhoneNo']['value']==$Phone['PhoneNo']['value']){
					mysql_query("DELETE FROM phone WHERE id='".$PhoneToLink['id_db']."'");
					}
				}
			}
		}
	mysql_query("UPDATE phone SET some_id='$linkedtogid' WHERE some_id='$tolinkgid'");
	}
if(isset($ContactToLink['Addresses']) and count($ContactToLink['Addresses'])>0){
	if(!isset($Contact['Addresses']) or count($Contact['Addresses'])==0){
		mysql_query("UPDATE gidaid SET guardian_id='$linkedtogid' WHERE guardian_id='$tolinkgid';");
		}
	}
mysql_query("UPDATE gidsid SET guardian_id='$linkedtogid' WHERE guardian_id='$tolinkgid';");
mysql_query("UPDATE guardian SET surname='DUPLICATE',forename='DUPLICATE' WHERE id='$tolinkgid';");

$returnXML=array('id_db'=>$xmlid,'exists'=>'false');
$rootName='Contact';
$xmlechoer=true;
require_once('../../scripts/http_end_options.php');
exit;
?>
