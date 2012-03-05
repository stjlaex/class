<?php
/**                    httpscripts/merit_delete.php
 *
 */

require_once('../../scripts/http_head_options.php');


if(!isset($xmlid)){print "Failed"; exit;}

$d_der=mysql_query("DELETE FROM merits WHERE id='$xmlid'");

$returnXML=fetchMerit();
$rootName='Merit';

require_once('../../scripts/http_end_options.php');
exit;
?>