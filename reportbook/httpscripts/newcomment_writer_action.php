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
			$incom.=trim($_POST['incom'.$c]);
			}

		/* Separate the subcomments with ::: for splitting 
		 * but last subcomment should not get a separator
		 */
		if($inno>1 and $c<($inno-1)){$incom.=':::';}
		$comment='   ';
		}


	$incom=clean_html($incom);
	$incom=clean_text($incom);


	/* Now do the category radio boxes */
	$incat='';
	/*TODO: categories are only handled by the commentwriter for
	  report summaries. Careful not to overwrite subject ones!! */
	if($addcategory=='yes' and $bid=='summary'){
		/* CARE: we don't know the class stage here so have to get all
		   catdefs and depend on catid being used correctly to
		   identify the post values.
		*/
		$catdefs=get_report_skill_statements($rid,$bid,$pid);
		foreach($catdefs as $catdef){
			$catid=$catdef['id'];
			if(isset($_POST["incat$catid"])){
				$in=$_POST["incat$catid"];
				$incat.=$catid.':'.$in.';';
				mysql_query("INSERT INTO report_skill_log (report_id,student_id, skill_id, rating, comment, teacher_id) 
							VALUES ('$rid','$sid', '$catid', '$in', '$incom', '$tid');");
				}
			}
		}


	if($inmust=='yes' and ($incom!='' or $incat!='')){
		$d_re=mysql_query("SELECT COUNT(*) FROM reportentry WHERE student_id='$sid' AND report_id='$rid'
							AND subject_id='$bid' AND component_id='$pid' AND comment='$incom';");
		if(mysql_result($d_re,0)==0){
			if(mysql_query("INSERT INTO reportentry (comment, category,
							teacher_id, report_id, student_id, 
							subject_id, component_id) VALUES
							('$incom', '$incat', '$tid', '$rid', '$sid',
							'$bid', '$pid')")){
				$entryn=mysql_insert_id();
				}
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
	if($_POST['jsonresponse']){
		echo json_encode(array('inmust'=>isset($entryn)? $entryn: $inmust));
		}
	else{
		$teachername=get_teachername($tid);
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
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.8.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/book.js"></script>
</head>
<body onload="closeHelperWindow(<?php print '\''.$openerId.'\',\''.$entryn.'\',\''.$incom.'\',\''.$teachername.'\'';?>);">
	<div id="bookbox">
	  <div id="heading">
	  </div>
	  <div id="viewcontent" class="content">
	  </div>
	</div>
</body>
</html>
<?php
	}
?>
