<?php
/**                    httpscripts/comment_writer.php
 */

require_once('common.php');

if(isset($_GET{'sid'})){$sid=$_GET{'sid'};}
elseif(isset($_POST{'sid'})){$sid=$_POST{'sid'};}
if(isset($_GET{'rid'})){$rid=$_GET{'rid'};}
elseif(isset($_POST{'rid'})){$rid=$_POST{'rid'};}
if(isset($_GET{'bid'})){$bid=$_GET{'bid'};}
elseif(isset($_POST{'bid'})){$bid=$_POST{'bid'};}
if(isset($_GET{'pid'})){$pid=$_GET{'pid'};}
elseif(isset($_POST{'pid'})){$pid=$_POST{'pid'};}

$reportdef=fetchReportDefinition($rid);
$Student=fetchshortStudent($sid);
$Report['Comments']=fetchReportEntry($reportdef, $sid, $bid, $pid);
if(sizeof($Report['Comments']['Comment'])==0){
		$Comment=array('Text'=>array('value'=>''),
					   'Teacher'=>array('value'=>'ADD NEW ENTRY'));
		$inmust='yes';
		}
else{	
	$Comment=$Report['Comments']['Comment'][0]; 
	$inmust=$Comment['id_db'];
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS Comment Writer</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2006 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU General Public License version 2" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../stylesheets/viewstyle.css" />
<link rel="stylesheet" type="text/css" href="../../stylesheets/commentwriter.css" />
<script src="../../javascripts/formfunctions.js" type="text/javascript"></script>
<script src="../../lib/spell_checker/cpaint/cpaint2.inc.js" type="text/javascript"></script>
<script src="../../lib/spell_checker/js/spell_checker.js" type="text/javascript"></script>
</head>
<body>

	<div id="bookbox">
	  <?php three_buttonmenu(); ?>

	  <div id="heading">
		<label><?php print_string('student'); ?></label>
			<?php print $Student['Forename']['value'].' '. $Student['Surname']['value'].' '.
			  $Student['MiddleNames']['value'];?>
	  </div>

	  <div id="viewcontent" class="content">
		<form id="formtoprocess" name="formtoprocess" method="post" action="comment_writer_action.php">
		  <div class="center">
			<textarea title="spellcheck" id="Comment" style="width:98%; height:180px;"
			  accesskey="../../lib/spell_checker/spell_checker.php" 
			  maxlength="1000" tabindex="0" 
			  name="incom" ><?php print $Comment['Text']['value'];?></textarea>
		  </div>

		<input type="hidden" name="inmust" value="<?php print $inmust; ?>">
		<input type="hidden" name="sid" value="<?php print $sid; ?>">
		<input type="hidden" name="rid" value="<?php print $rid; ?>">
	    <input type="hidden" name="bid" value="<?php print $bid; ?>">
		<input type="hidden" name="pid" value="<?php print $pid; ?>">
		</form>
	  </div>
	</div>
</body>
</html>
