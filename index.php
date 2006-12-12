<?php
require_once('../school.php');
require_once('classdata.php');
require_once('lib/include.php');
$books=$CFG->books;
print '<?xml version="1.0" encoding="utf-8"?'.'>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php print $CFG->sitename; ?></title>
<link rel="shortcut icon" href="images/favicon.ico" />
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002, 2003, 2004, 2005, 2006
	Stuart Thomas Johnson. All trademarks acknowledged. All rights reserved." />
<meta name="version" content="<?php print $CFG->version; ?>" />
<meta name="license" content="GNU General Public License version 2" />
<link href="css/parentstyle.css" rel="stylesheet" type="text/css" />
<link href="css/selery.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="js/fonter.js"></script>
<script language="JavaScript" type="text/javascript" src="js/hostfunctions.js"></script>
</head>
<body onload="loadLogin('cover');">

<div id="sitelogo">
	<img name="sitelogo" src="images/orangelogo.png"/>
</div>

<div id="navtabs">
	<div class="booktabs">
		<ul>
		<li id="logbooktab"><p class="logbook" onclick="loadLogin('logbook');">Log In</p></li>
		</ul>
	</div>
</div>
			
<div id="logbook">
	<form  id="loginchoice" name="workingas" method="post" action="logbook.php" target="viewlogbook">
	</form>
	<form  id="langchoice" name="langpref" method="post" action="logbook.php" target="viewlogbook">
	</form>
	<div id="sidebuttons">
	</div>
</div>

<iframe id="viewlogbook" name="viewlogbook" class="coverframe" scrolling="no"></iframe>
<div id="logbookoptions" class="bookoptions"></div>

<div id="aboutbookoptions" class="bookoptions"></div>
<iframe id="viewaboutbook" name="viewaboutbook" class="bookframe"></iframe>

<?php
	/*all because it contains all possible books*/
	/* even if after login user does not have access*/
	$showbooks=$books['all']+$books['external']['all'];
	foreach($showbooks as $bookhost=>$bookname){
?>
		<div id="<?php print $bookhost.'options';?>" class="bookoptions"></div>

		<iframe id="<?php print 'view'.$bookhost;?>" 
			name="<?php print 'view'.$bookhost;?>" class="bookframe">
		</iframe>
<?php
   		}
?>

</body>
</html>
