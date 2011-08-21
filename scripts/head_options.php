<?php
	require_once('../dbh_connect.php');
	require_once('../school.php');
	require_once('classdata.php');
	require_once('logbook/session.php');
	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	start_class_phpsession();
	require_once('logbook/authenticate.php');
	if(!isset($_SESSION['uid'])){session_defaults();}
	$user=new user($db);
	require_once('lib/include.php');
	if($_SESSION['uid']==0){include('logbook/login.php'); exit;}
	require_once('logbook/permissions.php');
	$tid=$_SESSION['username'];
	$respons=$_SESSION['respons'];
	$r=$_SESSION['r'];
	$tab=1;
	$currentlang=current_language();
	print '<?xml version="1.0" encoding="utf-8"?'.'>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $currentlang;?>" xml:lang="<?php print $currentlang;?>">
<head>
<title>ClaSS</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2011 Stuart Thomas Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU Affero General Public License version 3" />
<style type="text/css">@import url(lib/jscalendar/skins/aqua/theme.css);</style>
<link rel="stylesheet" type="text/css" href="css/bookstyle.css?version=941" />
<link rel="stylesheet" type="text/css" href="css/selery.css" />
<link rel="stylesheet" type="text/css" href="css/<?php print $book; ?>.css" />
<script language="JavaScript" type="text/javascript">
var pathtobook = "<?php print $CFG->sitepath.'/'.$CFG->applicationdirectory.'/'.$book.'/';?>";
</script>
<script language="JavaScript" type="text/javascript" src="js/printing.js?version=101"></script>
<script language="JavaScript" type="text/javascript" src="js/qtip.js"></script>
<script language="JavaScript" type="text/javascript" src="js/bookfunctions.js?version=101"></script> 
<script language="JavaScript" type="text/javascript" src="js/register.js"></script>
<script language="JavaScript" type="text/javascript" src="lib/tiny_mce/tiny_mce.js"></script>
<script language="JavaScript" type="text/javascript" src="lib/jscalendar/calendar.js"></script>
<script language="JavaScript" type="text/javascript" src="lib/jscalendar/lang/calendar-<?php  print_string('shortlocale');?>.js"></script>
<script language="JavaScript" type="text/javascript" src="lib/jscalendar/calendar-setup.js"></script>
<?php 
if($book=='infobook'){
?>
<script language="JavaScript" type="text/javascript" src="js/gs_sortable.js?version=937"></script>
<?php 
	}
?>
</head>
<body onload="loadRequired();">
