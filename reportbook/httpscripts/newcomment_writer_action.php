<?php
/**                    httpscripts/newcomment_writer_action.php
 */

require_once('../../scripts/http_head_options.php');
$sub=$_POST['sub'];
$sid=$_POST['sid'];
$rid=$_POST['rid'];
$inno=$_POST['inno'];/*the number of textareas to expect*/
$incom='';
$tid=$_SESSION['username'];

//trigger_error('INNO: '.$inno,E_USER_WARNING);

if($sub=='Cancel'){
	$openerId='-100';
	}
elseif($sub=='Submit'){
	$openerId=$_POST['openid'];
	$Student=fetchStudent_short($sid);
	if(isset($_POST['bid'])){$bid=$_POST['bid'];}
	if(isset($_POST['pid'])){$pid=$_POST['pid'];}
	if(isset($_POST['inmust'])){$inmust=$_POST['inmust'];}
	if(isset($_POST['addcategory'])){$addcategory=$_POST['addcategory'];}


	for($c=0;$c<$inno;$c++){
		if(isset($_POST['incom'.$c])){
			$incom.=clean_text($_POST['incom'.$c]);
			//$incom.=$_POST['incom'.$c];
			}
		/* Separate the subcomments with ::: for splitting 
		 * but last subcomment should not get a separator
		 */
		if($inno>1 and $c<($inno-1)){$incom.=':::';}
		$comment='   ';
		}

	//trigger_error($c.' '.$incom,E_USER_WARNING);


	/* Now do the category radio boxes */
	$incat='';
	/*TODO: categories are only handled by the commentwriter for
	  report summaries. Careful not to overwrite subject ones!! */
	if($addcategory=='yes' and $bid=='summary'){
		/* CARE: we don't know the class stage here so have to get all
		   catdefs and depend on catid being used correctly to
		   identify the post values.
		*/
		$catdefs=get_report_categories($rid,$bid,$pid,'cat');
		foreach($catdefs as $catdef){
			$catid=$catdef['id'];
			if(isset($_POST["incat$catid"])){
				$in=$_POST["incat$catid"];
				$incat.=$catid.':'.$in.';';
				}
			}
		}


	if($inmust=='yes' and ($incom!='' or $incat!='')){
		if(mysql_query("INSERT INTO reportentry (comment, category,
						teacher_id, report_id, student_id, 
						subject_id, component_id) VALUES
						('$incom', '$incat', '$tid', '$rid', '$sid',
						'$bid', '$pid')")){
			$entryn=mysql_insert_id();
			}
		}
	elseif($inmust!='yes'){
		$entryn=$inmust;
		mysql_query("UPDATE reportentry SET
						comment='$incom' WHERE report_id='$rid' AND
						student_id='$sid' AND subject_id='$bid' AND
						component_id='$pid' AND entryn='$entryn';");

		if($incat!=''){
			mysql_query("UPDATE reportentry SET
						category='$incat' WHERE report_id='$rid' AND
						student_id='$sid' AND subject_id='$bid' AND
						component_id='$pid' AND entryn='$entryn';");
			}
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS Comment Writer</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU Affero General Public License version 3" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/commentwriter.css" />
<script language="JavaScript" type="text/javascript" src="../../js/book.js"></script>
</head>
<body onload="closeHelperWindow(<?php print '\''.$openerId.'\',\''.$entryn.'\',\''.$comment.'\'';?>);">
	<div id="bookbox">
	  <div id="heading">
	  </div>
	  <div id="viewcontent" class="content">
	  </div>
	</div>
</body>
</html>
