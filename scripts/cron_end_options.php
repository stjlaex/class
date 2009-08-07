<?php
while(list($index,$out)=each($result)){
	error_log($out,0,$CFG->serverlog);
	}
while(list($index,$out)=each($error)){
	error_log($out,0,$CFG->serverlog);
	}
$endtime=time();
$runtime=elapsedtime($starttime,$endtime);
$rundate=date('j F Y, H:i:s',$starttime);
$runend=date('j F Y, H:i:s',$endtime);
trigger_error($current.': '.$rundate.' took '.$runtime,E_USER_WARNING);
?>