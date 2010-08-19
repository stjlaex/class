<?php
/**                    httpscripts/transport_editor_action.php
 *
 */

require_once('../../scripts/http_head_options.php');
include('../../lib/fetch_transport.php');
$sub=$_POST['sub'];
$sid=$_POST['sid'];

if($sub=='Cancel'){
	$openerId='-100';
	}
else{

	if(isset($_POST['openid'])){$openerId=$_POST['openid'];}
	if(isset($_POST['sid'])){$sid=$_POST['sid'];}
	if(isset($_POST['date'])){$date=$_POST['date'];}
	if(isset($_POST['busid'])){$busid=$_POST['busid'];}
	if(isset($_POST['stopid'.$busid])){$stopid=$_POST['stopid'.$busid];}
	if(isset($_POST['bookid'])){$oldbookid=$_POST['bookid'];}
	if(isset($_POST['dayrepeat'])){$dayrepeat=$_POST['dayrepeat'];}
	if(isset($_POST['comment'])){$comment=$_POST['comment'];}else{$comment='';}


	if($sub=='Submit'){

		add_journey_booking($sid,$busid,$stopid,$date,$dayrepeat,$comment);

		}
	elseif($sub=='Delete'){

		delete_journey_booking($sid,$oldbookid);

		}

	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Transport Editor</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2010 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU Affero General Public License version 3" />
<script language="JavaScript" type="text/javascript" src="../../js/bookfunctions.js"></script>
</head>
<body onload="closeTransportHelper(<?php print '\''.$sid.'\',\''.$date.'\',\''.$openerId.'\'';?>);">
	<div id="bookbox">
	  <div id="viewcontent" class="content">
	  </div>
	</div>
</body>
</html>
