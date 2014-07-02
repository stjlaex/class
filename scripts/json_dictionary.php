<?php
require_once('../../school.php');
require_once('../classdata.php');
include('../lib/language.php');
if(file_exists('../schoollang.php')){include('../schoollang.php');}

if(isset($_GET['lang']) and $_GET['lang']!=''){$lang=$_GET['lang'];}else{$lang='en';}

$strings=array();
$langfiles=langfile_locations($lang);
foreach($langfiles as $langfile){
	if(file_exists($langfile)){
		include($langfile);
		$strings=array_merge($strings,$string);
		}
	}

echo json_encode($strings);
?>
