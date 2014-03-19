<?php
/**							httpscripts/file_share.php
 *
 */

require_once('../../scripts/http_head_options.php');

require_once('../../lib/eportfolio_functions.php');

if(isset($_POST['fileids'])){$fileids=(array)$_POST['fileids'];}else{$fileids=array();}
if(isset($_POST['FILECONTEXT'])){$filecontext=$_POST['FILECONTEXT'];}else{$filecontext='';}
if(isset($_POST['sharearea'])){$filesharearea=$_POST['sharearea'];}else{$filesharearea='';}

if(sizeof($fileids)>0 and $filesharearea!=""){

	$ffname=$filesharearea;
	foreach($fileids as $fid){
		$d_ff=mysql_query("SELECT folder_id,file.owner_id FROM file JOIN file_folder ON file_folder.id=folder_id WHERE file.id='$fid' AND name='$filecontext';");
		$pfid=mysql_result($d_ff,0,'folder_id');
		$oid=mysql_result($d_ff,0,'owner_id');
		if($pfid!="" and $oid!=""){
			mysql_query("INSERT INTO file_folder (id,owner,owner_id,parent_folder_id,name,access) VALUE ('','s','$oid','$pfid','$ffname','');");
			$newffid=mysql_insert_id();
			mysql_query("UPDATE file SET folder_id='$newffid' WHERE id=$fid;");
			}
		}

	}

header("Location: ".$_POST['upload_redirect']);
?>
