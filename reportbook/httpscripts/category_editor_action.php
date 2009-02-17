<?php
/**                    httpscripts/category_editor_action.php
 */

require_once('../../scripts/http_head_options.php');
$sub=$_POST['sub'];
$rid=$_POST['rid'];
$type=$_POST['type'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_POST['pid'])){$pid=$_POST['pid'];}

/*Note: categories are not handled by the commentwriter*/

if($sub=='Cancel'){
	$openerId='-100';
	$incom='';
	}
elseif($sub=='Submit'){
	$openerId=$_POST['openid'];
	
	if($type=='rep'){
		if($pid!='' and $pid!=' '){$bid=$pid;}
		$RepDef=fetchReportDefinition($rid);
		$crid=$RepDef['Course']['value'];
		list($ratingnames,$catdefs)=get_report_categories($rid,$bid,$pid);
		$ratingname=$catdefs[0]['rating_name'];
		}
	else{
		$crid='%';
		$catdefs=array();
		}


	$maxcatn=10;
	for($matn=1;$matn<=$maxcatn;$matn++){
		if(isset($_POST['catid'.$matn])){
			$incatid=$_POST['catid'.$matn];
			$inname='name'. $matn;
			$inval=clean_text($_POST[$inname]);
			if($incatid==-1 and $inval!=''){
				mysql_query("INSERT INTO categorydef SET name='$inval', type='$type', 
					rating_name='$ratingname', subject_id='$bid', course_id='$crid';");
				$catid=mysql_insert_id();
				mysql_query("INSERT INTO ridcatid SET report_id='$rid', categorydef_id='$catid', 
					subject_id='$bid';");
				}
			elseif($incatid>0 and $inval!=''){
				mysql_query("UPDATE categorydef SET name='$inval'
								WHERE id='$incatid';");
				}
			elseif($incatid>0 and $inval==''){
				mysql_query("DELETE FROM ridcatid WHERE categorydef_id='$incatid' 
					AND report_id='$rid';");
				}
			//trigger_error($incatid.': '.$inval,E_USER_WARNING);
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
<meta name="copyright" content="Copyright 2002-2006 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU General Public License version 2" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/commentwriter.css" />
<script language="JavaScript" type="text/javascript" src="../../js/bookfunctions.js"></script>
</head>
<body onload="closeHelperWindow(<?php print '\''.$openerId.'\'';?>);">
	<div id="bookbox">

	  <div id="heading">
			  <label><?php print ''; ?></label>
	  </div>

	  <div id="viewcontent" class="content">
<?php
	  //	  include('../../scripts/results.php');
?>
	  </div>

	</div>
</body>
</html>
