<?php 
/**														registerbook.php
 *	This is the hostpage for the register
 */

$host='registerbook.php';
$book='registerbook';
$current='';
$choice='';
$cancel='';

include ('scripts/head_options.php');

if(!isset($sid)){$sid='';}
if(isset($_POST{'current'})){$current=$_POST{'current'};} else{$current='';}
if(isset($_POST{'choice'})){$choice=$_POST{'choice'};}else{$choice='';}
if(isset($_POST{'cancel'})){$cancel=$_POST{'cancel'};}
?>
  <div id="bookbox" class="registercolor">
<?php
	if($current!=''){
		$view = 'registerbook/'.$current;
		include($view);
		}
	else{
?>
	<div class="content">
	  <fieldset class="center">
		<h2>The RegisterBook is under development!</h2>
	  </fieldset>
	</div>
<?php
		}
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions">	
	<fieldset class="registerbook">
	  <legend><?php print_string('options');?></legend>
	</fieldset>
  </div>
<?php include('scripts/end_options.php'); ?>








