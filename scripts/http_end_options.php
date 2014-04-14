<?php
	while(list($index,$out)=each($result)){
		error_log($out,0,$CFG->serverlog);
		}
	while(list($index,$out)=each($error)){
		error_log($out,0,$CFG->serverlog);
		}
	if(isset($returnXML)){
		if(!$xmlechoer){
			$xml=xmlpreparer($rootName,$returnXML);
			$xml='<'.'?xml version="1.0" encoding="utf-8"?'.'>'.$xml.'';
			$html="<link rel='stylesheet' type='text/css' href='../templates/".$profile['transform'].".css' media='all' title='Template Output' />"
				.xmlprocessor($xml,$profile['transform'].'.xsl');
			header('Content-Type: text/html'); 
			$array=array("html"=>$html,"template"=>$profile['transform']);
			echo json_encode($array);
			}
		else{
			header('Content-Type: text/xml'); 
			xmlechoer("$rootName",$returnXML);
			}
		}
	elseif(isset($returnText)){
		header('Content-Type: text/plain'); 
		echo $returnText;
		}
?>
