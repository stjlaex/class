<?php
/**                    httpscripts/file_delete.php
 *
 */

require_once('../../scripts/http_head_options.php');

require_once('../../lib/eportfolio_functions.php');

if(isset($_POST['fileids'])){$fileids=(array)$_POST['fileids'];}else{$fileids=array();}
if(isset($_POST['FILEOWNER'])){$fileowner=$_POST['FILEOWNER'];}else{$fileowner='';}
if(isset($_POST['FILECONTEXT'])){$filecontext=$_POST['FILECONTEXT'];}else{$filecontext='';}

$Files=array();
$File['File']=array();

if(sizeof($fileids)>0){

	foreach($fileids as $fileid){

		if($filecontext=='icon'){
			$filedata=array();
			$filedata['context']='icon';
			$filedata['owner']=$fileowner;
			$filedata['fname']=$fileid;
		}
                else{
                    $filedata=(array)get_filedata($fileid);
                    $filedata['owner']=$fileowner;
                    $filedata['context']=$filecontext;
                }
		
		$success=delete_file($filedata);
		$File=array();

		if($success){
			$File['id_db']=$fileid;
			//trigger_error('DELETE: '.$fileid,E_USER_WARNING);
			$Files['File'][]=$File;
			}

		}

	}

$returnXML=$Files;
$rootName='Files';
require_once('../../scripts/http_end_options.php');
exit();
?>
