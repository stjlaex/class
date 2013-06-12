<?php
/**						file_import_xml.php
 *
 * generic file import for xml
 *
 * aborts to the originating form page on failure
 */

$fname=$_FILES['importfile']['tmp_name'];
$fuser=$_FILES['importfile']['name'];
$ferror=$_FILES['importfile']['error'];
$ftype=$_FILES['importfile']['type'];

	if($ferror>0){
		$error[]='Unable to open remote file.';
		$action=$choice;
		}
	elseif($ftype!='text/xml'){
		$error[]=$ftype;
		$error[]='File is of the wrong type, it must be an xml file.';
		$action=$choice;
		}
	elseif(is_uploaded_file($fname)){

		//$filecontent=file_get_contents($fname);

		$handle=fopen($fname, "r");
		$filecontent=fread($handle, filesize($fname));
		fclose($handle);

		if($filecontent===false){
			$error[]='XML file failed';
			}
		else{
			$search=array('<![CDATA[',']]>','<?xml version="1.0" encoding="utf-8"?>','’','&');
			$replace=array('','','','\'','&amp;');
			$filecontent=str_replace($search,$replace,$filecontent);

			$filecontent=preg_replace("/(<.*>)/Ue","strtolower('\\1')",$filecontent);
			$filecontent=preg_replace("/(<.*>)/Ue","str_replace(' ','','\\1')",$filecontent);

			/**
			 * The container tag is just for the sake of the simple_xml
			 * functions. It is immediately stripped and not part of the $xml
			 * array. 
			 */

			$xml=xmlstringToArray('<container>'.$filecontent.'</container>');			
			}
		}
?>