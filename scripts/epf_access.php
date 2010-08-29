<?php
$epfusername=strtolower($Student['EPFUsername']['value']);
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
  <div style="visibility:hidden;">
	<iframe id="externalbook" name="externalbook" class="externalbookframe"></iframe>
	<script>frames["externalbook"].location.href="<?php print $externalred;?>";</script>  
  </div>

