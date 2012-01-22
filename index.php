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
require_once('../school.php');
require_once('classdata.php');
require_once('lib/include.php');
/* Just if last logout wasn't clean. */
$past=time()-7200;
foreach($_COOKIE as $key=>$value){
	setcookie($key, $value, $past, $CFG->sitepath);
	}
session_unset();
session_destroy();
$books=$CFG->books;
$currentlang=current_language();
print '<?xml version="1.0" encoding="utf-8"?'.'>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $currentlang;?>" xml:lang="<?php print $currentlang;?>">
<head profile="http://www.w3.org/2005/10/profile">
<title><?php print $CFG->sitename; ?></title>
<link rel="icon" type="image/png" href="images/classicon.png" />
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 Stuart Thomas Johnson. All trademarks acknowledged. All rights reserved." />
<meta name="version" content="<?php print $CFG->version; ?>" />
<meta name="license" content="GNU Affero General Public License version 3" />
<link href="css/hoststyle.css" rel="stylesheet" type="text/css" />
<link href="css/selery.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="js/host.js?version=1014"></script>
</head>
<body onload="loadLogin('cover.php');">

<div id="navtabs">
	<div class="booktabs">
		<ul>
		<li id="logbooktab"><p class="logbook" onclick="loadLogin('logbook.php');">Log In</p></li>
		</ul>
	</div>
</div>
			
<div id="logbook">
	<form id="langchoice" name="langpref" method="post" action="logbook.php" target="viewlogbook">
	</form>
</div>

<iframe id="viewlogbook" name="viewlogbook" class="coverframe" scrolling="no"></iframe>
<div id="logbookoptions" class="bookoptions"></div>

<div id="aboutbookoptions" class="bookoptions"></div>
<iframe id="viewaboutbook" name="viewaboutbook" class="bookframe"></iframe>

<?php
	/* Use all because it contains all possible books*/
	/* even if after login user does not have access*/
	$showbooks=$books['all'];
	foreach($showbooks as $bookhost=>$bookname){
?>
		<div id="<?php print $bookhost.'options';?>" class="bookoptions"></div>

		<iframe id="<?php print 'view'.$bookhost;?>" 
			name="<?php print 'view'.$bookhost;?>" class="bookframe">
		</iframe>
<?php
   		}
?>
<?php
	$showbooks=$books['external']['all'];
	foreach($showbooks as $bookhost=>$bookname){
?>
		<div id="<?php print $bookhost.'options';?>" style="display:none;" class="bookoptions"></div>

		<iframe id="<?php print 'view'.$bookhost;?>" 
			name="<?php print 'view'.$bookhost;?>" class="bookframe">
		</iframe>
<?php
   		}
?>

</body>
</html>
