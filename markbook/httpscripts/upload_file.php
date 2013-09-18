<?php
/**		   					httpscripts/upload_file.php
 */

$book='markbook';
$tab=1;
/* $inmust forces an insert of the submissions as a new entry
 * TODO: would be to allow updates for existing files.
 */
$inmust='yes';

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sid'])){$sid=$_GET['sid'];}else{$sid='';}
if(isset($_POST['sid'])){$sid=$_POST['sid'];}
if(isset($_GET['eid'])){$eid=$_GET['eid'];}else{$eid='';}
if(isset($_POST['eid'])){$eid=$_POST['eid'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}else{$bid='';}
if(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}else{$pid='';}
if(isset($_POST['pid'])){$pid=$_POST['pid'];}
if(isset($_GET['openid'])){$openid=$_GET['openid'];}

	if($sid==''){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{
		$Student=fetchStudent($sid);
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Upload File Helper</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content="<?php print $CFG->version; ?>" />
<meta name="licence" content="GNU Affero General Public License version 3" />
<link rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link rel="stylesheet" type="text/css" href="../../css/markbook.css" />
<script src="../../js/editor.js" type="text/javascript"></script>
<script src="../../js/book.js?version=1043" type="text/javascript"></script>
<script src="../../js/documentdrop.js?version=1043" type="text/javascript"></script>
<script src="../../js/qtip.js" type="text/javascript"></script>
</head>
<body onload="loadRequired('<?php print $book;?>');documentdropInit();">

	<div class="markcolor" id="bookbox">

	  <?php three_buttonmenu(); ?>

	  <div id="heading">
		<label><?php print_string('student'); ?></label>
			<?php print $Student['DisplayFullName']['value'];?>
	  </div>

	  <div class="content">
		<div class="listmenu fileupload">
<?php
		require_once('../../lib/eportfolio_functions.php');
		html_document_drop($Student['EPFUsername']['value'],'assessment',$entid);
?>
		</div>



		<form id="formtoprocess" name="formtoprocess" method="post" action="upload_file_action.php">

		<div class="listmenu fileupload">
		  <div class="center">
		  <fieldset class="right documentdrop">
<?php

if($_SESSION['worklevel']>-1 and ($CFG->emailguardiancomments=='yes' or ($CFG->emailguardiancomments=='limit' and $perm['x']==1))){
		$checkname='sharewithparents';
		$checkcaption=get_string('sharewithguardian','infobook');
		$checkalert=get_string('sharecommentalert','infobook');
		/* TODO: implement share with parents */
		include('../../scripts/check_yesno.php');
		unset($checkalert);
		}

?>

			<label for="Comment"><?php print_string('description',$book);?></label>
			<textarea id="Comment"
				style="height:80px;" tabindex="<?php print $tab++;?>"  
				name="comment" ></textarea>
		  </fieldset>
		  </div>
		</div>

		<input type="hidden" name="inmust" value="<?php print $inmust; ?>"/>
		<input type="hidden" name="sid" value="<?php print $sid; ?>"/>
	    <input type="hidden" name="bid" value="<?php print $bid; ?>"/>
	    <input type="hidden" name="pid" value="<?php print $pid; ?>"/>
		<input type="hidden" name="eid" value="<?php print $eid; ?>"/>
		<input type="hidden" name="openid" value="<?php print $openid; ?>"/>
		</form>
	</div>


</body>
</html>
