<?php 
/**												entrybook.php
 *	This is the hostpage for the entrybook.
 *	
 */
$host='entrybook.php';
$book='entrybook';
$current='';
$choice='';
$cancel='';

include('scripts/head_options.php');

if(isset($_POST{'current'})){$current=$_POST{'current'};}
if(isset($_POST{'choice'})){$choice=$_POST{'choice'};}
if(isset($_POST{'cancel'})){$cancel=$_POST{'cancel'};}
if(isset($_GET{'choice'})){$choice=$_GET{'choice'};}
if(isset($_GET{'cancel'})){$cancel=$_GET{'cancel'};}
if(isset($_GET{'current'})){$current=$_GET{'current'};}

include('infobook/quick_search.php');/*to be in the sidebar at all times*/
?>
  <div id="bookbox" class="infocolor">
<?php
	if($current!=''){
		$view = 'entrybook/'.$current;
		include($view);
		}
?>
  </div>
<?php
include('scripts/end_options.php');
?>