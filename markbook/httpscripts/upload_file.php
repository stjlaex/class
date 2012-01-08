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
if(isset($_GET['cid'])){$cid=$_GET['cid'];}else{$cid='';}
if(isset($_POST['cid'])){$cid=$_POST['cid'];}
if(isset($_GET['mid'])){$mid=$_GET['mid'];}else{$mid='';}
if(isset($_POST['mid'])){$mid=$_POST['mid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}else{$bid='';}
if(isset($_POST['pid'])){$pid=$_POST['pid'];}
if(isset($_GET['openid'])){$openid=$_GET['openid'];}

	if($sid==''){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{
		$Student=fetchStudent_short($sid);
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Upload File Helper</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU Affero General Public License version 3" />
<link rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link rel="stylesheet" type="text/css" href="../../css/markbook.css" />
<script src="../../js/editor.js" type="text/javascript"></script>
<script src="../../js/book.js?version=1013" type="text/javascript"></script>
<script src="../../js/qtip.js" type="text/javascript"></script>
</head>
<body onload="loadRequired();">

	<div class="markcolor" id="bookbox">

	  <?php three_buttonmenu(); ?>

	  <div id="heading">
		<label><?php print_string('student'); ?></label>
			<?php print $Student['DisplayFullName']['value'];?>
	  </div>

	  <div class="content">
		<form id="formtoprocess" name="formtoprocess" method="post" 
				enctype="multipart/form-data" action="upload_file_action.php">


		  <fieldset class="center">
			<legend><?php print_string('selectfile',$book);?></legend>
			<label for="Filename"><?php print_string('filename',$book);?></label>
			<input class="required"  tabindex="<?php print $tab++;?>" 
			  type="file" id="Filename" name="importfile" />
		  </fieldset>

		  <fieldset class="center">
			<legend><?php print_string('details',$book);?></legend>
			<div class="center">
			<label for="Title"><?php print_string('title',$book);?></label>
			  <input maxlength="240" 
				type="text" id="Title" tabindex="<?php print $tab++;?>" name="title" />
			</div>

			<div class="center">
			<label for="Comment"><?php print_string('description',$book);?></label>
			  <textarea id="Comment"
				style="height:80px;" tabindex="<?php print $tab++;?>"  
				name="comment" ></textarea>
			</div>

			<div class="center">
			<label for="news"><?php print_string('newsworthy',$book);?></label>
			  <input type="checkbox" id="News" tabindex="<?php print $tab++;?>" 
				name="news" />
			</div>
		  </fieldset>

		<input type="hidden" name="inmust" value="<?php print $inmust; ?>"/>
		<input type="hidden" name="sid" value="<?php print $sid; ?>"/>
		<input type="hidden" name="mid" value="<?php print $mid; ?>"/>
	    <input type="hidden" name="cid" value="<?php print $cid; ?>"/>
		<input type="hidden" name="pid" value="<?php print $pid; ?>"/>
		<input type="hidden" name="openid" value="<?php print $openid; ?>"/>
		</form>
	</div>

</body>
</html>
