<?php
include('../school.php');
include('classdata.php');
print '<?xml version="1.0" encoding="utf-8"?'.'>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002, 2003, 2004, 2005, 2006
	Stuart Thomas Johnson. All trademarks acknowledged. All rights reserved." />
<meta name="version" content="<?php print $CFG->version; ?>" />
<meta name="license" content="GNU General Public License version 2" />
<link href="css/bookstyle.css" rel="stylesheet" type="text/css" />
<link href="css/logbook.css" rel="stylesheet" type="text/css" />
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900' rel='stylesheet' type='text/css'>
</head>
<body>

<div id="coverbox">

<div class="background">
	<img src="../images/gradient.png" style="width:95%;height:100%;float:right;" />
</div>

<div id="branded">
<?php
if($CFG->support!='' and $CFG->support!='laex.org'){
?>
	<img onClick="window.open('http://<?php print $CFG->support;?>','support');"
		src="../images/bannerlogo.png" 
		alt="<?php print $CFG->support;?>" title="<?php print $CFG->support;?> support" />
<?php
	}
else{
?>
	<img onclick="window.open('http://laex.org/class','support');"
				alt="ClaSS" title="ClaSS Homepage" src="images/orangelogo.png" />
<?php
	}
?>
</div>

<div id="schoollogo">
	<img src="../images/<?php print $CFG->schoollogo;?>" />
</div>

</div>

</body>
</html>
