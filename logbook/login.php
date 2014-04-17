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
<?php
    if ($CFG->debug == 'dev' or !file_exists("css/applogbook.min.css")) {
        print '<link rel="stylesheet" type="text/css" href="css/bookstyle.css" />
          <link rel="stylesheet" type="text/css" href="css/logbook.css" />
          <link rel="stylesheet" href="css/uniform.edit.css" media="screen" />';
    } else {
        print '<link href="css/applogbook.min.' . str_replace('.', '', $CFG->version) . '.css" rel="stylesheet" type="text/css" /';
    }
?>
<link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900' rel='stylesheet' type='text/css'>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

</head>

<body class="login" onload="parent.refreshloginscreen(window.frameElement)">
    <div style="display:none;" id="hiddenbookoptions">
        <p>
            <?php
            if ($CFG -> loginaside != '') {print $CFG -> loginaside;
            } else {print_string('loginaside');
            }
            ?>
        </p>
    </div>
    <div style="display:none;" id="hiddenlang">
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

      <div class="login-left">
            <div class="login-form">
                <fieldset>
                    <form name="formtoprocess" id="formtoprocess" novalidate method="post" action="logbook/login_action.php">
                        <div class="form-group">
                            <label for="Username" class="fa fa-user"></label>
                            <input type="text" placeholder="<?php print_string('username'); ?>" id="Username" name="username" tabindex="1" pattern="truealphanumeric" onkeypress="capsCheck(arguments[0]);" />
                        </div>
                        <div class="form-group">
                            <label for="Password" class="fa fa-lock"></label>
                            <input type="password" placeholder="<?php print_string('password'); ?>" id="Password" name="password" tabindex="2" pattern="truealphanumeric" onkeypress="capsCheck(arguments[0]);" />
                        </div>
                        <button id="login" name="submitlogin" tabindex="3" onClick="return validateForm(this.form);">
                          <?php print_string('enter'); ?>
                        </button>
                        <input type="hidden" id="lang" name="lang" value="<?php print $langchoice; ?>" />
                    </form>
                </fieldset>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="login-right">
        <div id="schoollogo" class="schoollogo">
            <h1><?php print_string('welcome'); ?></h1>
            <p><?php print $CFG -> loginaside; ?></p>
            <img src="../images/<?php print $CFG -> schoollogo; ?>" />
        </div>
    </div>
    <footer>
            <div class="container">
                <ul>
                    <li>Powered by</li>
                    <li><a title="learningdata" href="http://www.learningdata.ie/"><img width="156px" height="47px" alt="learningdata" src="images/learningdata.png"></a></li>
                </ul>
                <ul class="school-col">
                    <!--li>School</li>
                    <li><a href="http://learningdata.ie"><img src="images/generic-school.png" id="school_logo" data-pb-tint-colour="#d2e5ed" data-pb-tint-opacity="1" class="filter-tint pb-ref-filter-tint-0"></a></li-->
                </ul>
                <ul class="legal-col">
                    <li>&copy; 2014 Learning Data</li>
                    <li><a href="http://demo.learningdata.net/classic/mod/invite/terms.php">Terms and conditions</a></li>
                </ul>
        </div>        
    </footer>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
    <?php
    if ($CFG->debug == 'dev' or !file_exists("js/applogbook.min.js")) {
        print '<script type="text/javascript" src="js/qtip.js"></script>
          <script type="text/javascript" src="js/book.js"></script>';
    } else {
        print '<script type="text/javascript"  src="js/applogbook.min.' . str_replace('.', '', $CFG->version) . '.js"></script>';
    }
    ?>
    
    <script type="text/javascript" >
        parent.document.getElementById("langchoice").innerHTML = document.getElementById("hiddenlang").innerHTML;
        //document.getElementById("coverbox").style.zIndex = "100";
        parent.loadRequired("logbook");
        parent.loadBookOptions("logbook");
    </script>

</body>
</html>