<?php
/**                    httpscripts/file_upload.php
 *
 */

require_once('../../scripts/http_head_options.php');

require_once('../../lib/eportfolio_functions.php');


if(isset($_SERVER['HTTP_X_FILENAME'])){$filename=$_SERVER['HTTP_X_FILENAME'];}else{$filename='';}
if(isset($_SERVER['HTTP_X_FILEOWNER'])){$owner=$_SERVER['HTTP_X_FILEOWNER'];}else{$owner='';}
if(isset($_SERVER['HTTP_X_FILECONTEXT'])){$context=$_SERVER['HTTP_X_FILECONTEXT'];}else{$context='';}


$Files=array();

if($filename!='' and $owner!=''){
	/* From an AJAX call */
	$filepath=$CFG->eportfolio_dataroot. '/cache/files/';
	$uniquename=uniqid();
	$tmp=$filepath . $uniquename;
	file_put_contents($tmp,file_get_contents('php://input'));

	$publishdata['foldertype']=$context;
	$publishdata['title']='';
	$publishdata['batchfiles'][]=array('epfusername'=>$owner,
									   'filename'=>$uniquename,
									   'originalname'=>$filename,
									   'description'=>'',
									   'tmpname'=>$tmp);

	upload_files($publishdata,false);

	//$File=array('name'=>$filename);
	$Files[]=$File;
	}

$returnXML=$Files;
$rootName='Files';
require_once('../../scripts/http_end_options.php');
exit();
?>
