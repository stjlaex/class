<?php
	while(list($index,$out)=each($result)){
		error_log($out,0,$CFG->serverlog);
		}
	while(list($index,$out)=each($error)){
		error_log($out,0,$CFG->serverlog);
		}
 	header('Content-Type: text/xml'); 
	xmlechoer("$rootName",$returnXML);
?>