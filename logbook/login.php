<?php
	require_once('../school.php');
	require_once('classdata.php');
?>
<?php print '<?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS LogIn</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2005 S T Johnson. All trademarks acknowledged. All rights reserved" />
<link href="css/bookstyle.css" rel="stylesheet" type="text/css" />
<link href="css/logbook.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/qtip.js"></script>
<script type="text/javascript" src="js/bookfunctions.js"></script>
</head>


<body onLoad="loadRequired();">

<div style="visibility:hidden;" id="hiddenbookoptions">	
<p>
<?php 
if($CFG->loginaside!=''){print $CFG->loginaside;}
else{print_string('loginaside');}
?>
</p>
</div>

<div style="visibility:hidden;" id="hiddenlang">
<?php
	if(isset($_POST['langchoice'])){update_user_language($_POST['langchoice']);};
	include('logbook/language_select.php');
?>
</div>

<div id="coverbox" class="logincolor">

<?php 
if($CFG->sitestatus=='down'){
?>
<fieldset id="loginbox">
<div class="center">
<?php print_string('siteisdown');?>
</div>
</fieldset>
<?php
	}
else{
?>

<form name="formtoprocess" id="formtoprocess" novalidate method="post" action="logbook/login_action.php">
<fieldset id="loginbox">
		 <legend><?php print_string('classarea');?></legend>

<div class="center">
<table>
<tr>
<td><label for="Username"><?php print_string('username');?></label></td>
<td>
<input type="text" id="Username" name="username" class="required" tabindex="1" 
	maxlength="20" pattern="truealphanumeric" 
		onkeypress="capsCheck(arguments[0]);" />
</td>
</tr>

<tr>
<td>
<label for="Password"><?php print_string('password');?></label>
<td>
<td><input type="password" id="Password" name="password" class="required" tabindex="2" 
	maxlength="20" pattern="truealphanumeric" 
		onkeypress="capsCheck(arguments[0]);" />
</td>
</tr>
</table>

<button id="login" name="submitlogin" tabindex="3" 
	onClick="return validateForm(this.form);">
<?php print_string('enter');?>
</button>

</div>
</fieldset>
</form>

<?php 
		  }
?>

</div>

<script>
parent.document.getElementById("langchoice").innerHTML=document.getElementById("hiddenlang").innerHTML;
document.getElementById("coverbox").style.zIndex="100";
parent.loadBookOptions("logbook");
</script>

</body>
</html>

