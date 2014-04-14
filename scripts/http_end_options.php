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
			$html="<!DOCTYPE html>
			<head>
			<meta charset=\"utf-8\">
			<link rel='stylesheet' type='text/css' href='../templates/".$profile['transform'].".css' media='all' title='Template Output' />
			<script language='JavaScript' type='text/javascript' src='js/raphael.js' charset='utf-8'></script>
			<script language='JavaScript' type='text/javascript' src='js/g.raphael-min.js' charset='utf-8'></script>
			<script language='JavaScript' type='text/javascript' src='js/g.bar-min.js' charset='utf-8'></script>
			<script language='JavaScript' type='text/javascript' src='js/d3/d3.v3.min.js' charset='utf-8'></script>
			<script language='JavaScript' type='text/javascript' src='js/jcrop/jquery.min.js' charset='utf-8'></script>
			<script language='JavaScript' type='text/javascript' src='../templates/".$profile['transform'].".js' charset='utf-8'></script>
			<meta http-equiv='pragma' content='no-cache'/>
			<meta http-equiv='Expires' content='0'/>
			</head>
			<body onLoad=\"".$profile['transform']."(); alert('Hello');\">"
			.xmlprocessor($xml,$profile['transform'].'.xsl')
			."</body>;
			</html>";
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
