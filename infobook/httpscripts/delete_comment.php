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

		global $CFG;
		require_once('../../lib/eportfolio_functions.php');
		if($CFG->eportfolio_db!=''){
			$dbepf=db_connect(true,$CFG->eportfolio_db);
			mysql_query("SET NAMES 'utf8'");
			}
		$table=$CFG->eportfolio_db_prefix.'weblog_posts';
		if(isset($CFG->clientid)){$school=$CFG->clientid;}
		else{$school='';}

		$epfuid=elgg_get_epfuid($epfusername,'person');
		$group=array('epfgroupid'=>'','owner'=>$epfuid,'name'=>'Family','access'=>'');
		$epfgroupid=elgg_update_group($group,array('owner'=>'','name'=>'','access'=>''),false);
		$access='group'.$epfgroupid;

		$d_p=mysql_query("SELECT ident FROM $table WHERE weblog='$epfuid' AND access='$access' 
							AND body LIKE '<p>$comment</p>%' AND title='$subject' ORDER BY ident DESC;");
		$post_id=mysql_result($d_p,0,'ident');

		mysql_query("DELETE FROM $table WHERE ident=$post_id;");

		$db=db_connect();
		mysql_query("SET NAMES 'utf8'");
		}

	$d_incidents=mysql_query("DELETE FROM comments WHERE id='$xmlid' LIMIT 1");

$returnXML=array('id_db'=>$xmlid,'exists'=>'false');
$rootName='Comment';
$xmlechoer=true;
require_once('../../scripts/http_end_options.php');
exit;
?>
