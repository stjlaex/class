<?php 
/**								new_homework_action.php
 */

$action='class_view.php';
$action_post_vars=array('displaymid');

include('scripts/sub_action.php');

$title=clean_text($_POST['title']);
$defname=$_POST['defname'];
$description=clean_text($_POST['description']);
$dateset=$_POST['dateset'];
$datedue=$_POST['datedue'];
if(!isset($_POST['refs'])){$refs='';}else{$refs=clean_text($_POST['refs']);}
if(!isset($_POST['total'])){$total=0;}else{$total=clean_text($_POST['total']);}
if(!isset($_POST['newpid'])){$newpid=$pid;}else{$newpid=$_POST['newpid'];}
$hwid=$_POST['hwid'];
$crid=$_POST['crid'];
$bid=$_POST['bid'];
$stage=$_POST['stage'];

	if($hwid==-1){
		mysql_query("INSERT INTO homework (title, description, refs, 
				def_name, course_id, subject_id, stage, component_id, author) 
				VALUES ('$title', '$description', '$refs', 
				'$defname', '$crid', '$bid', '$stage', '$newpid', '$tid');");
		$hwid=mysql_insert_id();
		}

	mysql_query("INSERT INTO mark (entrydate, marktype, midlist, 
			topic, total, comment, author, def_name, component_id) 
		    VALUES ('$datedue', 'hw', '$hwid', '$title', '$total', 
			'$dateset', '$tid', '$defname', '$newpid')");

   	$mid=mysql_insert_id();
	$displaymid=$mid;
	$cid=$cids[0];
	mysql_query("INSERT INTO midcid (mark_id, class_id) VALUES ('$mid', '$cid')");

	if($CFG->eportfoliosite!=''){
		require_once('lib/eportfolio_functions.php');
		$body=$description. '<hr />'.$refs. 
				'<hr /> <p>Work set: '.display_date($dateset). 
				'&nbsp;&nbsp;&nbsp; Work due by: '. display_date($datedue).'</p><hr />';
		$subject=get_subjectname($bid);
		$component=get_subjectname($pid);
		$d_c=mysql_query("SELECT name FROM class WHERE id='$cid';");
		$classname=mysql_result($d_c,0);
		$epfpostid=elgg_new_homework($tid,$classname,$subject,$component,$title,$body,$dateset);
		/*Link mark to Classic homework*/
		mysql_query("UPDATE mark SET elgg_weblog_post_id='$epfpostid' WHERE id='$mid';");
		}

	/*Add homework to Schoolbag*/
	if($CFG->schoolbag_api_url!='' and $CFG->schoolbag_api_key!=''){
		$query=$CFG->schoolbag_api_url.'homework?key='.$CFG->schoolbag_api_key.'&clientid='.$CFG->clientid;
		$homework=http_build_query(array(
					'classname' => $classname,
					'dateset' => $dateset,
					'datedue' => $datedue,
					'title' => $title,
					'text' => $description
						)
					);
		$opts=array('http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content' => $homework
					)
				);
		$context=stream_context_create($opts);
		$result=file_get_contents($query, false, $context);
		}

	//include('scripts/results.php');
	include('scripts/redirect.php');
?>
