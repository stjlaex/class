<?php 
/**			  										webmail.php
 *	This is the hostpage for an external webmail
 *  application
 */

$host='webmail.php';
$book='webmail';
include ('scripts/head_options.php');

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
<script>frames["externalbook"].location.href="<?php print $CFG->webmailsite;?>/login/indexclass.php?token=<?php print $token;?>&user=<?php print $tid;?>";</script>
