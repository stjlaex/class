<?php 
/**			  										webmail.php
 *	This is the hostpage for the webmail
 *	The page to be included is set by $current
 *	A preselected menu option is set by $choice
 *
 */

$host='webmail.php';
$book='webmail';
include ('scripts/head_options.php');
if(!isset($sid)){$sid='';}
if(isset($_POST{'current'})){$current=$_POST{'current'};} else{$current='';}
if(isset($_POST{'choice'})){$choice=$_POST{'choice'};}else{$choice='';}

$ip=$_SERVER['REMOTE_ADDR'];
$salt=$CFG->lmsshare.$CFG->support;
$secret=md5($salt . $ip);
$token=md5($tid . $secret);
?>

  <div style="visibility:hidden;" id="hiddenbookoptions">	
	<fieldset class="webmail"><legend><?php print_string('options');?></legend>
	</fieldset>
  </div>

  <div id="bookbox" class="webmailcolor">
<iframe id="externalbool" name="externalbook" class="externalbookframe"></iframe>
  </div>


<?php
include('scripts/end_options.php');
?>
<script>frames["externalbook"].location.href="<?php print $CFG->webmailsite;?>";</script>








