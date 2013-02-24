<?php
/**                    httpscripts/delete_comment.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	$d_incidents=mysql_query("DELETE FROM comments WHERE id='$xmlid' LIMIT 1");

$returnXML=array('id_db'=>$xmlid,'exists'=>'false');
$rootName='Comment';
require_once('../../scripts/http_end_options.php');
exit;
?>

















