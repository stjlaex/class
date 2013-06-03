<?php
	while(list($index,$out)=each($result)){
		error_log($out,0,$CFG->serverlog);
		}
	while(list($index,$out)=each($error)){
		error_log($out,0,$CFG->serverlog);
		}
	if(isset($returnXML)){
		header('Content-Type: text/xml'); 
		xmlechoer("$rootName",$returnXML);
		}
	elseif(isset($returnText)){
		header('Content-Type: text/plain'); 
		echo $returnText;
		}
?>