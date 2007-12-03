<?php 
/**	   	  										webmail.php
 *
 *	This is the hostpage for an external webmail
 *  application
 */

$host='webmail.php';
$book='webmail';
$entrypage='action.php';//this is the page to be called in nocc
include ('scripts/head_options.php');

$user=get_user($tid);
$emailnames=array();
/* All emails have the same domain which is set in the webmail app so */
/* just need the name part of the address. This formula can be */
/* over-ridden by explicity setting the emailuser field.*/
if($user['emailuser']!='' and $user['emailuser']!=' '){
	$emailnames[]=$user['emailuser'];
	}
else{
	$emailnames=explode('@',$user['email']);
	}
$mailbcc='';
$emailpasswd=$user['emailpasswd'];
$ip=$_SERVER['REMOTE_ADDR'];
$salt=$CFG->webmailshare;
$secret=md5($salt . $ip);
$token=md5($salt . $ip);
//$emailpasswd=endecrypt($salt,$emailpasswd,'de');
//trigger_error($ip. ' '.$salt. ' '.$emailnames[0].' '.$token,E_USER_WARNING);


if(isset($_GET['recipients'])){
	$recipients=(array)$_GET['recipients'];
	}
elseif(isset($_POST['recipients'])){
	$recipients=(array)$_POST['recipients'];
	}
if(isset($recipients)){
	$action='write';
	while(list($index,$email)=each($recipients)){
		if(!isset($joiner)){$joiner='&mail_bcc=';}
		else{$joiner=',';}
		$mailbcc.=$joiner. $email;
		}
	unset($joiner);
	}
else{
	$action='login';
	}

$lang=current_language();
$externalparams=array(
					  'lang' => $lang,
					  'action' => $action,
					  'domainnum' => 0,
					  'token' => $token,
					  'user' => $emailnames[0],
					  'mail_to' => $user['email'],
					  'passwd' => $emailpasswd
					  );

/*construct the redirect string*/
$externalred=$CFG->webmailsite . '/'.$entrypage;
while(list($param,$value)=each($externalparams)){
	if(!isset($joiner)){$joiner='?';}
	else{$joiner='&';}
	$externalred.=$joiner . $param . '=' . $value;
	}
$externalred.=$mailbcc;
?>

  <div style="visibility:hidden;" id="hiddenbookoptions">
	<fieldset class="webmail">
	  <legend>WebMail</legend>
	  <div>
		<?php print $CFG->webmailaside;?>
	  </div>
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
if($user['emailpasswd']!='' and $user['email']!=''){
?>
<script>frames["externalbook"].location.href="<?php print $externalred;?>";</script>
<?php
	}
?>
