<?php 
/**	   	  										webmail.php
 *
 *	This is the hostpage for an external webmail
 *  application
 */

$host='webmail.php';
$book='webmail';
include ('scripts/head_options.php');

$entrypage='action.php';
$user=get_user($tid);
/* all emails have the same domain which is set in the webmail app so */
/* just need the name part of the address*/
$emailnames=explode('@',$user['email']);
$emailpasswd=$user['emailpasswd'];

$ip=$_SERVER['REMOTE_ADDR'];
$salt=$CFG->webmailshare;
$secret=md5($salt . $ip);
$token=md5($salt . $ip);

//$emailpasswd=endecrypt($salt,$emailpasswd,'de');

$externalparams=array(
			  'action' => 'login',
			  'lang' => current_language(),
			  'domainnum' => 0,
			  'token' => $token,
			  'user' => $emailnames[0],
			  'passwd' => $emailpasswd
			  );
  //trigger_error($ip. ' '.$salt. ' '.$emailnames[0].' '.$token,E_USER_WARNING);
/*construct the redirect string*/
$externalred=$CFG->webmailsite . '/'.$entrypage;
while(list($param,$value)=each($externalparams)){
	if(!isset($joiner)){$joiner='?';}
	else{$joiner='&';}
	$externalred=$externalred . $joiner . $param . '=' . $value;
	}
?>

  <div style="visibility:hidden;" id="hiddenbookoptions">
	<fieldset class="webmail"><legend><?php print_string('options');?></legend>
	</fieldset>
  </div>

  <div id="bookbox" class="webmailcolor">
	<div style="visibility:hidden;" id="webmailsite"	
	  logout="<?php print $CFG->webmailsite . '/logout.php';?>" >
	</div>
	<iframe id="externalbook" name="externalbook" class="externalbookframe"></iframe>
  </div>


<?php
include('scripts/end_options.php');
?>
<script>frames["externalbook"].location.href="<?php print $externalred;?>";</script>
