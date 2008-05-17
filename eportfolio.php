<?php 
/**												  eportfolio.php
 *
 *	This is the hostpage for the eportfolio
 *  It currently works only with a customised install of Elgg
 */

$host='eportfolio.php';
$book='eportfolio';
include ('scripts/head_options.php');

$epfusername=strtolower(get_staff_epfusername($tid));
$ip=$_SERVER['REMOTE_ADDR'];
$salt=$CFG->eportfolioshare;
$secret=md5($salt . $ip);
$token=md5($epfusername . $secret);
$entrypage='login/index.php';
$externalparams=array(
					  'action' => 'login',
					  'lang' => current_language(),
					  'password' => $token,
					  'username' => $epfusername
					  );
/*construct the redirect string*/
$externalred=$CFG->eportfoliosite . '/'.$entrypage;
while(list($param,$value)=each($externalparams)){
	if(!isset($joiner)){$joiner='?';}
	else{$joiner='&';}
	$externalred=$externalred . $joiner . $param . '=' . $value;
	}
?>

  <div style="visibility:hidden;" id="hiddenbookoptions">	
  </div>

  <div id="bookbox" class="eportfoliocolor">
	<div style="visibility:hidden;" id="eportfoliosite"	
	  logout="<?php print $CFG->eportfoliosite . '/login/logout.php';?>" >
	</div>
	<iframe id="externalbook" name="externalbook" class="externalbookframe"></iframe>
  </div>
<?php
	  include('scripts/end_options.php');
?>
<script>frames["externalbook"].location.href="<?php print $externalred;?>";</script>
