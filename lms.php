<?php 
/*														lms.php
	This is the hostpage for the lms
	The page to be included is set by $current
	A preselected menu option is set by $choice

*/

$host='lms.php';
$book='lms';
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
	<fieldset class="lms"><legend><?php print_string('options');?></legend>
	</fieldset>
  </div>

  <div id="bookbox" class="lmscolor">
	<iframe id="externalbook" name="externalbook" class="externalbookframe"></iframe>
  </div>


<?php
include('scripts/end_options.php');
?>
<script>frames["externalbook"].location.href="<?php print $CFG->lmssite;?>";</script>








