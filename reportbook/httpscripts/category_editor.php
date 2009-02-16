<?php
/**                    httpscripts/category_editor.php
 *
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['type'])){$type=$_GET['type'];}
elseif(isset($_POST['type'])){$type=$_POST['type'];}
if(isset($_GET['rid'])){$rid=$_GET['rid'];}
elseif(isset($_POST['rid'])){$rid=$_POST['rid'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}
elseif(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}
elseif(isset($_POST['pid'])){$pid=$_POST['pid'];}
if(isset($_GET['openid'])){$openid=$_GET['openid'];}


/* The categories and rating details */
/* TODO: generalise to all catdef types */
if($type=='rep'){
	list($ratingnames,$catdefs)=get_report_categories($rid,$bid);
	}
else{
	$catdefs=array();
	}

$table_ridcatid_rows=array();

reset($catdefs);
while(list($c4,$catdef)=each($catdefs)){
	$catid=$catdefs[$c4]['id'];
	$catname=$catdefs[$c4]['name'];
	$ratings=$ratingnames[$catdefs[$c4]['rating_name']];
	$row='<tbody id="'.$catid.'-'.$rown++.'"><tr>';
	$row.='<td><p>'.$catname.'</p></td>';
	while(list($value,$descriptor)=each($ratings)){
		$row.='<td><label>'.$descriptor.'</label></td>';
		}
	$row.='</tr></tbody>';
	trigger_error('CATDEF:'.$catid.':'.$row,E_USER_WARNING);
	$table_ridcatid_rows[$rown]=$row;
	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS Category Editor</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2006 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU General Public License version 2" />
<link rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link rel="stylesheet" type="text/css" href="../../css/commentwriter.css" />
<script src="../../js/bookfunctions.js" type="text/javascript"></script>
</head>
<body onload="loadRequired();">

	<div id="bookbox">
	  <?php three_buttonmenu(); ?>

	  <div id="heading">
		<label><?php print $bid; ?></label>
	  </div>

	<div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" 
					action="category_editor_action.php">

	<div class="center">
	<table class="listmenu" >
	<thead><th>Category</th><th></th></thead>
<?php
	reset($table_ridcatid_rows);
	while(list($tindex,$row)=each($table_ridcatid_rows)){
		print $row;
		}
?>
	</table>
	</div>

	<input type="hidden" name="type" value="<?php print $type; ?>"/>
	<input type="hidden" name="rid" value="<?php print $rid; ?>"/>
	<input type="hidden" name="bid" value="<?php print $bid; ?>"/>
	<input type="hidden" name="pid" value="<?php print $pid; ?>"/>
	<input type="hidden" name="openid" value="<?php print $openid; ?>"/>
	</form>
	</div>

	</div>
</body>
</html>