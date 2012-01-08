<?php
/**                    httpscripts/attendance_editor.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sid'])){$sid=$_GET['sid'];}
elseif(isset($_POST['sid'])){$sid=$_POST['sid'];}
if(isset($_GET['date'])){$date=$_GET['date'];}
elseif(isset($_POST['date'])){$date=$_POST['date'];}
if(isset($_GET['bookingid'])){$bookid=$_GET['bookingid'];}
elseif(isset($_POST['bookingid'])){$bookid=$_POST['bookingid'];}
if(isset($_GET['openid'])){$openid=$_GET['openid'];}

$day=date('N',strtotime($date));
$Student=fetchStudent_short($sid);

if($bookid>0){
	$b=(array)get_attendance_booking($bookid);
	$attsession=$b['session'];
	}
elseif($bookid==-1){$attsession='AM';$booking=array();}
elseif($bookid==-2){$attsession='PM';$booking=array();}


$book='register';
$tab=1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Attendance Editor</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU Affero General Public License version 3" />
<link rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link rel="stylesheet" type="text/css" href="../../css/register.css" />
<script src="../../js/editor.js" type="text/javascript"></script>
<script src="../../js/book.js?version=1013" type="text/javascript"></script>
<script src="../../js/qtip.js" type="text/javascript"></script>
</head>
<body onload="loadRequired();">

	<div id="bookbox" class="registercolor">
	<?php 
$extrabuttons=array();
$extrabuttons['delete']=array('name'=>'sub','value'=>'Delete');
three_buttonmenu($extrabuttons,$book);
?>

	<div id="heading">
	  <label><?php print_string('attendance','register'); ?></label>
			<?php print $Student['DisplayFullName']['value'];?>
	</div>

	<div id="viewcontent" class="content">

		<form id="formtoprocess" name="formtoprocess" method="post" 
									action="attendance_editor_action.php">

		  <fieldset class="divgroup center">
			<legend>
			  <?php print get_string(displayEnum($day,'dayofweek'),$book). '  '.display_date($date);?>
			</legend>

			<div class="left" style="width:80%;">
<?php 
	$listlabel='reason'; $listname='code'; $listid='code';$selcode=$b['code'];$required='yes';
   	include('../../scripts/set_list_vars.php');
   	list_select_enum('absencecode',$listoptions,$book);
?>
			</div>

			<label for="comment">
			<?php print_string('note','admin');?>
			</label>
			<div class="center">
			  <textarea name="comment" id="Comment" 
				tabindex="<?php print $tab++;?>" rows="3" cols="20"><?php print $b['comment'];?></textarea>
			</div>
		  </fieldset>


		  <fieldset class="divgroup center">
			<legend>
			<?php print_string('applyto','admin');?>
			</legend>
			<div>
			  <div>
				<label><?php print_string('todayonly','admin'); ?></label>
				<input type="radio" tabindex="<?php print $tab++;?>" checked="checked" value="once" name="dayrepeat" />
			  </div>
			  <div>
				<label><?php print_string('repeat','admin'); ?></label>
				<input type="radio" tabindex="<?php print $tab++;?>" value="weekly" name="dayrepeat" />
			  </div>
			  <div>
				<label><?php print_string('everyday','admin'); ?></label>
				<input type="radio" tabindex="<?php print $tab++;?>" value="every" name="dayrepeat" />
			  </div>
			</div>
		  </fieldset>



	  <fieldset class="center divgroup"> 
		<p>Force both AM and PM the same.</p>
		<div class="right">
			<?php $checkcaption=''; include('../../scripts/check_yesno.php');?>
		</div>
	  </fieldset>

		<input type="hidden" name="sid" value="<?php print $sid; ?>"/>
		<input type="hidden" name="attsession" value="<?php print $attsession; ?>"/>
	    <input type="hidden" name="date" value="<?php print $date; ?>"/>
		<input type="hidden" name="bookid" value="<?php print $bookid; ?>"/>
		<input type="hidden" name="openid" value="<?php print $openid; ?>"/>
	  </form>


  </div>
</body>
</html>
