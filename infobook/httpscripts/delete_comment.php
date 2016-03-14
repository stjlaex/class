<?php
/**                    httpscripts/delete_comment.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	/*First delete comment from eportfolio Classic is the comment is shared*/
	$d_c=mysql_query("SELECT detail,subject_id,guardians,student_id FROM comments WHERE id='$xmlid';");
	$shared=mysql_result($d_c,0,'guardians');
	if($shared){
		$comment=mysql_result($d_c,0,'detail');
		$subject_id=mysql_result($d_c,0,'subject_id');
		$sid=mysql_result($d_c,0,'student_id');
		$Student=fetchStudent_short($sid);
		$epfusername=$Student['EPFUsername']['value'];
		if($subject_id=="form"){$subject="Subject: form";}
		else{
			$d_s=mysql_query("SELECT name FROM subject WHERE id='$subject_id';");
			$subject="Subject: ".mysql_result($d_s,0,'name');
			}
		}

	$d_incidents=mysql_query("DELETE FROM comments WHERE id='$xmlid' LIMIT 1");

$returnXML=array('id_db'=>$xmlid,'exists'=>'false');
$rootName='Comment';
$xmlechoer=true;
require_once('../../scripts/http_end_options.php');
exit;
?>
