<?php
require_once('school.php');
global $CFG;
if(isset($_GET['theme']) and $_GET['theme']!=""){$theme=$_GET['theme'];}
else{$theme=$CFG->applicationdirectory;}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>ClaSS</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="copyright" content="Copyright 2002 - 2009 Stuart Thomas Johnson. All trademarks acknowledged. All rights reserved." />
<meta name="copyright" content="Copyright 2010 ClaSS Information Services S.L. All trademarks acknowledged. All rights reserved." />
<meta name="license" content="GNU Affero General Publice License version 3 or later" />

<script>
<!-- hide script from old browsers-->

// checks the window is top i.e. has not been opened in a frame
function preventFraming(){
	if(window.top != window){
		window.top.location = window.location;
		}
	}

// checks this is Firefox or some other Gecko-based browser
function checkDomBrowser(){
	if(document.documentElement && document.createElement){
        if(navigator.userAgent.indexOf('Gecko')!=-1){
            window.top.location.replace('<?php echo $theme;?>/index.php');
            }
		else{
			window.location.replace("class/wrongbrowser.html");
			}
		}
	}

preventFraming();
checkDomBrowser();

// end hide script from old browsers -->
</script>
</head>

<body>

<noscript>
<h2>Your browser has JavaScript turned off.</h2> 
<h2>To access ClaSS, you need to enable JavaScript.</h2> 

<p>To turn on JavaScript in Firefox:
<ol>
<li>select Tools (or Edit on Linux)</li>
<li>select Preferences</li>
<li>select Web Features</li>
<li>Ensure that the "Enable JavaScript" option is selected.</li>
</ol>
</p>

</noscript>

</body>
</html>
