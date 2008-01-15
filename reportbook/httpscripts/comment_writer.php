<?php
/**                    httpscripts/comment_writer.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sid'])){$sid=$_GET['sid'];}
elseif(isset($_POST['sid'])){$sid=$_POST['sid'];}
if(isset($_GET['rid'])){$rid=$_GET['rid'];}
elseif(isset($_POST['rid'])){$rid=$_POST['rid'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}
elseif(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}
elseif(isset($_POST['pid'])){$pid=$_POST['pid'];}
if(isset($_GET['entryn'])){$entryn=$_GET['entryn'];}
elseif(isset($_POST['entryn'])){$entryn=$_POST['entryn'];}
if(isset($_GET['openid'])){$openid=$_GET['openid'];}

/*Needs to be generalized for rid not being a rid!!!!!
if(){}
else{
*/

$reportdef=fetchReportDefinition($rid);
if($reportdef['report']['commentlength']=='0'){$commentlength='';}
else{$commentlength=' maxlength="'.$reportdef['report']['commentlength'].'"';}

$Student=fetchStudent_short($sid);
$Report['Comments']=fetchReportEntry($reportdef, $sid, $bid, $pid);
if(sizeof($Report['Comments']['Comment'])==0 or $entryn==sizeof($Report['Comments']['Comment'])){
	$Comment=array('Text'=>array('value'=>''),
					   'Teacher'=>array('value'=>'ADD NEW ENTRY'));
	$inmust='yes';
	if($reportdef['report']['profile_name']!=''){
		$profile_name=$reportdef['report']['profile_name'];
		$profilepids=(array)list_subject_components($pid,'FS');
		$profilepids[]=array('id'=>$pid,'name'=>'');
		while(list($pidindex,$component)=each($profilepids)){
			$profilepid=$component['id'];
			$d_eidsid=mysql_query("SELECT 
				assessment.description, assessment.id FROM eidsid JOIN assessment ON
				assessment.id=eidsid.assessment_id WHERE
				eidsid.student_id='$sid' AND eidsid.subject_id='$bid'
				AND eidsid.component_id='$profilepid' AND
				assessment.profile_name='$profile_name' AND
				eidsid.date > '2007-09-01'");
			$stats=array();
			while($eidsid=mysql_fetch_array($d_eidsid,MYSQL_ASSOC)){
				$topic=$eidsid['description'];
				$d_mark=mysql_query("SELECT comment
			    FROM mark JOIN eidmid ON
				mark.id=eidmid.mark_id WHERE
				mark.component_id='$profilepid' AND
				mark.def_name='$profile_name' AND
				topic='$topic';");
				$Comment['Text']['value'].=' '.mysql_result($d_mark,0);
				trigger_error($reportdef['report']['profile_name'].' '.$topic.' '.$profilepid,E_USER_WARNING);
				}
			}
		}
	}
else{
	$Comment=$Report['Comments']['Comment'][$entryn];
	$inmust=$Comment['id_db'];
	}

$dbstat=connect_statementbank();
if($dbstat!=''){
	$stage='';
	$StatementBank=fetchStatementBank($reportdef['report']['course_id'],$bid,$pid,$stage,$dbstat);
	}
if(isset($StatementBank) and sizeof($StatementBank['Area'])>0){
	$commentheight=180;
	}
else{
	$commentheight=400;
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
<link rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link rel="stylesheet" type="text/css" href="../../css/commentwriter.css" />
<script src="../../lib/spell_checker/cpaint/cpaint2.inc.js" type="text/javascript"></script>
<script src="../../lib/spell_checker/js/spell_checker.js" type="text/javascript"></script>
<script src="../../js/bookfunctions.js" type="text/javascript"></script>
<script src="../../js/qtip.js" type="text/javascript"></script>
<script src="../../js/statementbank.js" type="text/javascript"></script>
</head>
<body onload="setupSpellCheckers(); loadRequired();">

	<div id="bookbox">
	  <?php three_buttonmenu(); ?>

	  <div id="heading">
		<label><?php print_string('student'); ?></label>
			<?php print $Student['DisplayFullName']['value'];?>
	  </div>

	  <div class="content" style="height:<?php print $commentheight+60;?>px;">
		<form id="formtoprocess" name="formtoprocess" method="post" 
									action="comment_writer_action.php">
		  <div class="center">
			<textarea title="spellcheck" id="Comment"
			  style="height:<?php print $commentheight-20;?>px;" 
			  accesskey="../../lib/spell_checker/spell_checker.php" 
			  <?php print $commentlength;?> tabindex="0"  
				name="incom" ><?php print $Comment['Text']['value'];?></textarea>
		  </div>
		<input type="hidden" name="inmust" value="<?php print $inmust;?>"/>
		<input type="hidden" name="sid" value="<?php print $sid; ?>"/>
		<input type="hidden" name="rid" value="<?php print $rid; ?>"/>
	    <input type="hidden" name="bid" value="<?php print $bid; ?>"/>
		<input type="hidden" name="pid" value="<?php print $pid; ?>"/>
		<input type="hidden" name="openid" value="<?php print $openid; ?>"/>
		</form>
	</div>
<?php
			if($commentheight<300){
?>
	<div class="content" id="statementbank">
		<div class="tinytabs" id="area">
			<ul>
<?php
				   $n=0;
				   while(list($index,$Area)=each($StatementBank['Area'])){
?>
		<li id="<?php print 'tinytab-area-'.$Area['Name'];?>"><p 
		<?php if($n==0){ print ' id="current-tinytab" ';}?>
		class="<?php print $Area['Name'];?>"
		onclick="tinyTabs(this)"><?php print $Area['Name'];?></p></li>

			<div class="hidden" id="tinytab-xml-area-<?php print $Area['Name'];?>">
				<div style="float:left;width:10%;" class="statementlevels">
				<table class="listmenu">
<?php
				   print '<tr><td abilityoption="*" onclick="filterbyAbility(this)" class="vspecial">All</td></tr>';
				   $Levels=(array)$Area['Levels'];
				   while(list($index,$Level)=each($Levels)){
					   print '<tr><td abilityoption="'.$Level['Value'].'" onclick="filterbyAbility(this)">'.$Level['Name'].'</td></tr>';
					   }
?>
				</table>
				</div>
				<table style="float:left;width:88%;" class="listmenu">
<?php
				   $Statements=(array)$Area['Statements'];
				   while(list($index,$Statement)=each($Statements)){
					   $Statement=personaliseStatement($Statement,$Student);
					   print '<tr><td onclick="chooseStatement(this)" ';
					   print ' ability="'.$Statement['Ability'].'" '; 
					   print ' author="'.$Statement['Author'].'" '; 
					   print ' count="'.$Statement['Counter'].'" '; 
					   print '>'.$Statement['Value'].'</td></tr>';
					   }
?>
				</table>
			</div>
<?php
				  $n++;
				  }
?>
				</ul>
			</div>
			<div id="tinytab-display-area" class="tinytab-display">
			</div>
		</div>
<?php
			  }
?>
	</div>
</body>
</html>