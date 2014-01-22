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
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/uniform.default.css" media="screen" />
</head>

<body class="login">
    <div style="visibility:hidden;" id="hiddenbookoptions">
        <p>
            <?php
                if ($CFG -> loginaside != '') {print $CFG -> loginaside;
                } 
                else {print_string('loginaside');
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
        <div class="login-form">
            <form name="formtoprocess" id="formtoprocess" novalidate method="post" action="logbook/login_action.php">
                <div class="form-group">
                    <label for="Username" class="fa fa-user"></label>
                    <input type="text" placeholder="<?php print_string('username'); ?>" id="Username" name="username" class="required" tabindex="1" pattern="truealphanumeric" onkeypress="capsCheck(arguments[0]);" />
                </div>
                <div class="form-group">
                    <label for="Password" class="fa fa-lock"></label>
                    <input type="password" placeholder="<?php print_string('password'); ?>" id="Password" name="password" class="required" tabindex="2" pattern="truealphanumeric" onkeypress="capsCheck(arguments[0]);" />
                </div>
                <button id="login" name="submitlogin" tabindex="3" onClick="return validateForm(this.form);">
                  <?php print_string('enter'); ?>
                </button>
                <input type="hidden" id="lang" name="lang" value="<?php print $langchoice; ?>" />
            </form>
        </div>
        <div>
            <?php print_string('theme'); ?>
            <select>
                <option value="1">Classis 1.0</option>
                <option value="2">Classis 2.0</option>
            </select>
        </div>
    <?php
    }
    ?>
    <div id="schoollogo" class="schoollogo">
        <h1>Welcome</h1>
        <p>This a Demo database for Learning Data's school information management system Classis. For further information please contact us through our website at www.learningdata.ie</p>
    	<img src="../images/<?php print $CFG -> schoollogo; ?>" />
    </div>
    <script type="text/javascript" src="js/qtip.js"></script>
    <script type="text/javascript" src="js/book.js?version=1013"></script>
    <script type="text/javascript" >
        parent.document.getElementById("langchoice").innerHTML = document.getElementById("hiddenlang").innerHTML;
        //document.getElementById("coverbox").style.zIndex = "100";
        parent.loadRequired("logbook");
        parent.loadBookOptions("logbook");
    </script>

</body>
</html>

