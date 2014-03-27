<?php
/**                    httpscripts/upload_file_action.php
 *
 */

require_once('../../scripts/http_head_options.php');
require_once('../../lib/eportfolio_functions.php');

$sub=$_POST['sub'];
$action=$_POST['action'];
$sid=$_POST['sid'];
$tid=$_SESSION['username'];

if($sub=='Cancel'){
	$openerId='-100';
	$incom='';
	}
elseif($sub=='Submit'){
	$openerId=$_POST['openid'];
	if(isset($_POST['inmust'])){$inmust=$_POST['inmust'];}
	if(isset($_POST['eid'])){$eid=$_POST['eid'];}
	if(isset($_POST['bid'])){$bid=$_POST['bid'];}
	if(isset($_POST['pid'])){$pid=$_POST['pid'];}
	if(isset($_POST['comment'])){$comment=clean_text($_POST['comment']);}else{$comment='';}
	$todate=date('Y-m-d');
	$Student=fetchStudent_singlefield($sid,'EPFUsername');


	if($inmust=='yes'){
		/*Create a new entry*/
		trigger_error($sid.' : '.$comment. ' : '.$bid.' : '.$pid,E_USER_WARNING);

		//$score=(array)get_assessment_score($eid,$sid,$bid,$pid);
		//if($score['id']>0){
		//	$eidsid_id=$score['id'];
		//	}
		//else{
		//	mysql_query("INSERT INTO eidsid (assessment_id, student_id, subject_id, component_id, result, value, date) 
		//					VALUES ('$eid','$sid','$bid','$pid','','','$todate');");
		//	$eidsid_id=mysql_insert_id();
		//	}

		if($openerId==""){$folder="comment";}
		else{$folder="assessment";}

		/*Only inserts comments or file links if there is a comment or a file uploaded (avoids empty fields)*/
		$d_f=mysql_query("SELECT * FROM file WHERE owner_id='$sid' AND other_id='$eid' AND owner='s';");
		
		if(mysql_num_rows($d_f)>0 or $comment!=''){
			/*$d_c=mysql_query("INSERT INTO report_skill_log SET student_id='$sid', skill_id='$bid', comment='$comment', 
								report_id='$eid', teacher_id='$tid';");
			$entid=mysql_insert_id();*/
			require_once('../../lib/eportfolio_functions.php');
			//link_files($Student['EPFUsername']['value'],$folder,$entid);
			//link_files($Student['EPFUsername']['value'],$folder,$bid);
			//echo "linked: ".$folder."-".$bid."/".$eid."\ ".$sid;
			}

		//$Student=fetchStudent_short($sid);
		//elgg_upload_files($publishdata);
		//$EPFUsername=$Student['EPFUsername'];
		//$publishdata['foldertype']='work';
		//$publishdata['batchfiles'][]=array('epfusername'=>$EPFUsername['value'],
		//							   'filename'=>$filename,
		//							   'tmpname'=>$tmpname);


		}
	elseif($inmust!='yes'){
		/* TODO: Update an existing file*/
		$entryn=$inmust;
		}
	}

if($action=="Copy"){
	if(isset($_POST['files'])){$filesids=$_POST['files'];}
	if(isset($_POST['eid'])){$eid=$_POST['eid'];}
	if(isset($_POST['bid'])){$bid=$_POST['bid'];}
	if(isset($_POST['pid'])){$pid=$_POST['pid'];}
	if(isset($_POST['openid'])){$openid=$_POST['openid'];}
	if(isset($_POST['pid'])){$pid=$_POST['pid'];}else{$pid="";}
	$d_ff=mysql_query("SELECT id FROM file_folder WHERE owner_id=$openid;");
	$ffid=mysql_result($d_ff,0);
	foreach($filesids as $fileid){
		$d_f=mysql_query("SELECT * FROM file WHERE id=$fileid;");
		$files[$fileid]=mysql_fetch_array($d_f,MYSQL_ASSOC);
		}
	foreach($files as $file){
		$owner=$file['owner'];
		$owner_id=$file['owner_id'];
		$folder_id=$ffid;
		$title=$file['title'];
		$originalname=$file['originalname'];
		$description=$file['description'];
		$location=$file['location'];
		$access=$file['access'];
		$size=$file['size'];
		$other_id=$eid;
		$d_s=mysql_query("SELECT id FROM report_skill WHERE id='$bid' AND subject_id='$pid';");
		if(mysql_num_rows($d_s)>0){$other_id=$bid;}
		$d_l=mysql_query("SELECT * FROM file WHERE location='$location' and folder_id='$folder_id' and other_id='$other_id';");
		if(mysql_num_rows($d_l)==0){
			$d_f=mysql_query("INSERT INTO file (owner, owner_id, folder_id, title, originalname,
										description, location, access, size, other_id) VALUES 
										('$owner', '$owner_id','$folder_id','$title','$originalname',
										'$description','$location','$access','$size','$other_id');");
			}
		}
	$redirect="upload_file.php?sid=$sid&eid=$eid&bid=$bid&pid=$pid&openid=$openid";
	header("Location:".$redirect);
	}
elseif($action=="Remove"){
	if(isset($_POST['files'])){$filesids=$_POST['files'];}
	if(isset($_POST['eid'])){$eid=$_POST['eid'];}
	if(isset($_POST['bid'])){$bid=$_POST['bid'];}
	if(isset($_POST['pid'])){$pid=$_POST['pid'];}
	if(isset($_POST['openid'])){$openid=$_POST['openid'];}
	foreach($filesids as $fileid){
		mysql_query("DELETE FROM file WHERE id=$fileid;");
		}
	$redirect="upload_file.php?sid=$sid&eid=$eid&bid=$bid&pid=$pid&openid=$openid";
	header("Location:".$redirect);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS File Uploader</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU Affero General Public License version 3" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<script language="JavaScript" type="text/javascript" src="../../js/book.js?version=1013"></script>
</head>
<body onload="closeHelperWindow(<?php print '\''.$openerId.'\',\''.$entryn.'\',\'\'';?>);">
	<div id="bookbox">
	  <div id="heading">
	  </div>
	  <div id="viewcontent" class="content">
	  </div>
	</div>
</body>
</html>
