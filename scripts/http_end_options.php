<?php
	while(list($index,$out)=each($result)){
		error_log($out." # ",3,"/var/tmp/my-errors.log");
		}
	while(list($index,$out)=each($error)){
		error_log($out." # ",3,"/var/tmp/my-errors.log");
		}
 	header('Content-Type: text/xml'); 
	xmlechoer("$rootName",$returnXML);
?>