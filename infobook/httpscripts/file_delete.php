<?php
/**                    httpscripts/file_delete.php
 *
 */

require_once('../../scripts/http_head_options.php');

require_once('../../lib/eportfolio_functions.php');

if(isset($_POST['fileids'])){$fileids=(array)$_POST['fileids'];}else{$fileids=array();}

$Files=array();
$File['File']=array();

if(sizeof($fileids)>0){

	foreach($fileids as $fileid){

		$filedata=(array)get_filedata($fileid);
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
