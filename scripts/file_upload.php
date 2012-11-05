<?php
/**                    httpscripts/file_upload.php
 *
 */

require_once('../../dbh_connect.php');
require_once('../../school.php');
require_once('../classdata.php');
require_once('../logbook/session.php');
$db=db_connect();
mysql_query("SET NAMES 'utf8'");
start_class_phpsession();
require_once('../logbook/authenticate.php');
if(!isset($_SESSION['uid'])){session_defaults();} 
$user=new user($db);
if($_SESSION['uid']==0){exit;}

require_once('../lib/include.php');
require_once('../lib/eportfolio_functions.php');
//require_once('../logbook/permissions.php');
//$respons=$_SESSION['respons'];


if(isset($_SERVER['HTTP_X_FILENAME'])){$filename=$_SERVER['HTTP_X_FILENAME'];}else{$filename='';}
if(isset($_SERVER['HTTP_X_FILEOWNER'])){$owner=$_SERVER['HTTP_X_FILEOWNER'];}else{$owner='';}
if(isset($_SERVER['HTTP_X_FILECONTEXT'])){$context=$_SERVER['HTTP_X_FILECONTEXT'];}else{$context='';}


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

	print $filename.' uploaded';
	exit();
	}
/* From a form submit 
else{
	$files=$_FILES['fileselect'];
	foreach($files['error'] as $id => $err){
		if($err==UPLOAD_ERR_OK){
			$filename=$files['name'][$id];
			//move_uploaded_file($files['tmp_name'][$id],'uploads/' . $filename);
			print '<p>File '.$filename.' uploaded.</p>';
			}
		}
	}
*/

?>
