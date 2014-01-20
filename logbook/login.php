<?php
require_once ('../school.php');
require_once ('classdata.php');
require_once ('session.php');
start_class_phpsession();
kill_class_phpsession();
?>
<?php print '<?xml version="1.0" encoding="utf-8"?' . '>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS LogIn</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 S T Johnson. All trademarks acknowledged. All rights reserved" />
<link href="css/bookstyle.css" rel="stylesheet" type="text/css" />
<link href="css/logbook.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/qtip.js"></script>
<script type="text/javascript" src="js/book.js?version=1013"></script>
</head>
<body>
<div style="visibility:hidden;" id="hiddenbookoptions">	
<p>
<?php
if ($CFG -> loginaside != '') {print $CFG -> loginaside;
} else {print_string('loginaside');
}
?>
</p>
</div>
<div style="visibility:hidden;" id="hiddenlang">
<?php
if (isset($_POST['langchoice'])) {
	$langchoice = $_POST['langchoice'];
	update_user_language($langchoice);
} else {
	$langchoice = '';
}
include ('logbook/language_select.php');
?>
</div>

<div id="coverbox" class="logincolor">

<?php 
if($CFG->sitestatus=='down'){
?>
<fieldset id="loginbox">
<div class="center">
<?php print_string('siteisdown'); ?>
</div>
</fieldset>
<?php
}
else{
?>

<form name="formtoprocess" id="formtoprocess" novalidate method="post" action="logbook/login_action.php">
<fieldset id="loginbox">
	<!--legend><?php print_string('classarea'); ?></legend-->
	
	
	
	
	
	
	
<div class="login-form">
	<div class="form-group">
	  <label for="Username" class="fa fa-user"></label>
	  <input type="text" id="Username" name="username" placeholder="<?php print_string('username'); ?>" tabindex="1" pattern="truealphanumeric" onkeypress="capsCheck(arguments[0]);" />
	</div>

	<div class="form-group">
		<label for="Password" class="fa fa-lock"></label>
		<input type="password" id="Password" name="password" placeholder="<?php print_string('password'); ?>" tabindex="2" pattern="truealphanumeric" onkeypress="capsCheck(arguments[0]);" />	</div>

		<!--a href="#" class="btn btn-primary btn-lg btn-block">Login</a>
		<a href="#" class="login-link">Lost your password?</a-->
</div>
	
	



	<button id="login" name="submitlogin" tabindex="3" onClick="return validateForm(this.form);">
	  <?php print_string('enter'); ?>
	</button>
</fieldset>
	<input type="hidden" id="lang" name="lang" value="<?php print $langchoice; ?>" />
</form>
<?php
}
?>
</div>
<script>
	parent.document.getElementById("langchoice").innerHTML = document.getElementById("hiddenlang").innerHTML;
	document.getElementById("coverbox").style.zIndex = "100";
	parent.loadRequired("logbook");
	parent.loadBookOptions("logbook");
</script>
</body>
</html>

