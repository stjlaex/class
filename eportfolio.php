<?php 
/**												  eportfolio.php
 *	This is the hostpage for the eportfolio
 *  It currently works only with a customised install of Elgg
 */

$host='eportfolio.php';
$book='eportfolio';
include ('scripts/head_options.php');

$ip=$_SERVER['REMOTE_ADDR'];
$salt=$CFG->eportfolioshare;
$secret=md5($salt . $ip);
$token=md5($tid . $secret);
?>

  <div style="visibility:hidden;" id="hiddenbookoptions">	
	<fieldset class="eportfolio">
	  <legend><?php print_string('options');?></legend>
	</fieldset>
  </div>

  <div id="bookbox" class="eportfoliocolor">
	<iframe id="externalbook" name="externalbook" class="externalbookframe"></iframe>
  </div>
<?php
include('scripts/end_options.php');
?>
<script>frames["externalbook"].location.href="<?php print $CFG->eportfoliosite;?>/login/indexclass.php?token=<?php print $token;?>&user=<?php print $tid;?>";</script>
