<?php 
/**														register.php
 *	This is the hostpage for the register
 */

$host='register.php';
$book='register';
$current='';
$choice='';
$cancel='';

include ('scripts/head_options.php');

if(!isset($sid)){$sid='';}
if(isset($_POST['current'])){$current=$_POST['current'];}
if(isset($_POST['choice'])){$choice=$_POST['choice'];}
if(isset($_POST['cancel'])){$cancel=$_POST['cancel'];}
if(isset($_GET['choice'])){$choice=$_GET['choice'];}
if(isset($_GET['cancel'])){$cancel=$_GET['cancel'];}
if(isset($_GET['current'])){$current=$_GET['current'];}
?>
  <div id="bookbox" class="registercolor">
<?php
	if($current!=''){
		$view = 'register/'.$current;
		include($view);
		}
	else{
?>
	<div class="content">
	  <fieldset class="center">
		<h2>The Register is under development...</h2>
	  </fieldset>
	</div>
<?php
		}
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">	
	<form id="registerchoice" name="registerchoice" method="post" 
		action="register.php" target="viewregister">

	<fieldset class="register">
	  <legend><?php print_string('options');?></legend>
	</fieldset>

	</form>
  </div>
<?php
 include('scripts/end_options.php'); 
?>
