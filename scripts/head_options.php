<?php
	require_once('../dbh_connect.php');
	require_once('../school.php');
	require_once('classdata.php');
	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	session_name("$session");
	session_cache_limiter('nocache');
	session_start();
	require_once('logbook/authenticate.php');
	if(!isset($_SESSION['uid'])){session_defaults();}
	$user=new user($db);
	require_once('lib/include.php');
	if($_SESSION['uid']==0){include('logbook/login.php'); exit;}
	require_once('logbook/permissions.php');
	$tid=$_SESSION['username'];
	$respons=getRespons($tid);
	if(isset($_POST{'new_r'})){$_SESSION['r']=$_POST{'new_r'};$fresh='yes';}
	if(!isset($_SESSION{'r'})){$_SESSION['r']=-1;$fresh='very';}
	$r=$_SESSION['r'];
	print '<?xml version="1.0" encoding="utf-8"?'.'>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2005 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU General Public License version 2" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="css/viewstyle.css" />
<link id="bookstyle" rel="stylesheet" type="text/css" href="css/<?php print $book; ?>.css" />
<style type="text/css">@import url(css/nicetitle.css);</style>
<style type="text/css">@import url(lib/jscalendar/skins/aqua/theme.css);</style>
<script language="JavaScript" type="text/javascript">
var pathtobook = "<?php print $CFG->sitepath.'/'.$CFG->applicationdirectory.'/'.$book.'/';?>";
</script>
<script language="JavaScript" type="text/javascript" src="js/formfunctions.js"></script>
<script language="JavaScript" type="text/javascript" src="js/nicetitle.js"></script>
<script language="JavaScript" type="text/javascript" src="js/notnicetitles.js"></script>
<script language="JavaScript" type="text/javascript" src="js/extras.js"></script>
<script language="JavaScript" type="text/javascript" src="js/diagram.js"></script>
<script language="JavaScript" type="text/javascript" src="js/diagram_dom.js"></script>
<script language="JavaScript" type="text/javascript" src="js/printing.js"></script>
<script type="text/javascript" src="lib/jscalendar/calendar.js"></script>
<script type="text/javascript" src="lib/jscalendar/lang/calendar-<?php print_string('shortlocale');?>.js"></script>
<script type="text/javascript" src="lib/jscalendar/calendar-setup.js"></script>
</head>
<body onload="loadRequired();">
