<?php
/**
 *
 * ClaSS is the ClaSS Student System, a complete student
 * tracking, reporting, and information management system for schools.
 *
 * Copyright (C) 2002-2012 by Stuart Thomas Johnson.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/agpl.html>.
 *
 * @package    class
 * @subpackage core
 * @author     Stuart Thomas Johnson
 * @license    http://www.gnu.org/licenses/agpl.html GNU AGPL
 * @copyright  (C) 2012 Stuart Thomas Johnson
 *
 */
setcookie("theme", basename(dirname(__FILE__)), time() + (86400));
require_once ('../school.php');
require_once ('classdata.php');
require_once ('lib/include.php');
/* Just maybe last logout wasn't clean... */
require_once ('logbook/session.php');
start_class_phpsession();
kill_class_phpsession();
$books = $CFG -> books;
$currentlang = current_language();
print '<?xml version="1.0" encoding="utf-8"?' . '>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $currentlang; ?>" xml:lang="<?php print $currentlang; ?>">
  <head profile="http://www.w3.org/2005/10/profile">
    <title><?php print $CFG -> sitename; ?></title>
    <link rel="icon" type="image/png" href="images/classicon.png" />
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
    <meta http-equiv="Content-Script-Type" content="text/JavaScript" />
    <meta name="copyright" content="Copyright 2002-2012 Stuart Thomas Johnson. All trademarks acknowledged. All rights reserved." />
    <meta name="version" content="<?php print $CFG -> version; ?>" />
    <meta name="license" content="GNU Affero General Public License version 3" />
    <link href="css/selery.css?version=1042" rel="stylesheet" type="text/css" />
    <link href='//fonts.googleapis.com/css?family=Lato:100,300,400,700,900' rel='stylesheet' type='text/css'>
    <link href="css/font-awesome.min.css" rel="stylesheet">
<?php
      if($CFG->debug == 'dev' or !file_exists("css/apphost.min.css")){
          print '<link rel="stylesheet" href="css/uniform.edit.css" media="screen" />
            <link href="css/hoststyle.css?version='.$CFG->version.'" rel="stylesheet" type="text/css" />
            <link rel="stylesheet" type="text/css" href="css/vex.css" />
            <link rel="stylesheet" type="text/css" href="css/vex-ld-theme.css" />
            <link rel="stylesheet" type="text/css" href="css/ld-ui-elements.css" />';
		} 
	  else{
          print '<link href="css/apphost.min.' . str_replace('.', '', $CFG->version) . '.css" rel="stylesheet" type="text/css" /';
		}
?>      
  </head>
  <body onload="loadLogin('logbook.php');">
    <header>
      <div class="container">
        <div id="navtabs">
          <div class="booktabs"></div>
        </div>
        <div id="branded" class="branded">
          <img src="images/logo.png" onClick="loadBook('');"/>
        </div>
        <div id="logbook">
          <form id="langchoice" name="langpref" method="post" action="logbook.php" target="viewlogbook">
          </form>
        </div>
      </div>
    </header>
    <iframe id="viewlogbook" name="viewlogbook" class="coverframe" scrolling="no"></iframe>
    <div id="logbookoptions" style="display: none" class="bookoptions"></div>
    <?php
      /* Use all because it contains all possible books*/
      /* even if after login user does not have access*/
      $showbooks=$books['all'];
      foreach($showbooks as $bookhost=>$bookname){
      ?>
    <div id="<?php print $bookhost . 'options'; ?>" style="display: none" class="bookoptions"></div>
    <div id="<?php print $bookhost . 'optionshandle'; ?>" style="display: none" class="bookoptionshandle">
      <span class="fa fa-angle-double-down">
    </div>
    <iframe id="<?php print 'view' . $bookhost; ?>" style="display:none;" name="<?php print 'view' . $bookhost; ?>" class="bookframe"></iframe>
    <?php
    }
      ?>
    <?php
      $showbooks=$books['external']['all'];
      foreach($showbooks as $bookhost=>$bookname){
      ?>
    <div id="<?php print $bookhost . 'options'; ?>" style="display:none;" class="bookoptions"></div>
    <iframe id="<?php print 'view' . $bookhost; ?>" style="display:none;" name="<?php print 'view' . $bookhost; ?>" class="bookframe"></iframe>
    <?php
    }
      ?>
      <script src="js/jquery-1.8.2.min.js"></script>
      <?php
        if ($CFG->debug == 'dev' or !file_exists("js/apphost.min.js")) {
          print '<script src="js/host.js?version='.$CFG->version.'"></script>
              <script language="Javascript" type="text/javascript" src="js/vex.combined.min.js"></script>
              <script src="js/jquery.uniform.min.js"></script>
              <script src="js/ld-ui-elements.js"></script>';
        } else {
          print '<script src="js/apphost.min.' . str_replace('.', '', $CFG->version) . '.js"></script>';
        }
      ?>
    <script>
        $("iframe").load(function() {
            $('#loginlang select').uniform({wrapperClass: "loginlang"});
            //$('.infobook select').uniform({wrapperClass: "default infoBook"});
            //$('#loginlang select').uniform({wrapperClass: "blueLight"});
        });
        vex.defaultOptions.className ="vex-ld-theme";
    </script>
  </body>
</html>

