<?php
/**                    httpscripts/transport_editor.php
 */

require_once('../../scripts/http_head_options.php');
include('../../lib/fetch_transport.php');

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
	$booking=get_journey_booking($bookid);
	trigger_error($booking['id'].' : '.$booking['bus_id'].' ',E_USER_WARNING);
	$journey=get_journey($booking['journey_id']);
	$direction=$journey['direction'];
	$stopid=$journey['stop_id'];
	}
elseif($bookid==-1){$direction='I';$journey=array();$booking=array();}
elseif($bookid==-2){$direction='O';$journey=array();$booking=array();}


$book='admin';
$tab=0;
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
<link rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link rel="stylesheet" type="text/css" href="../../css/admin.css" />
<script type="text/javascript">
function selerySwitch(servantclass,fieldvalue){
	switchedId="switch"+servantclass;
	newfielddivId="switch"+servantclass+fieldvalue;
	if(document.getElementById(newfielddivId)){	
		document.getElementById(switchedId).innerHTML=document.getElementById(newfielddivId).innerHTML;
		}
	}
</script>
<script src="../../js/bookfunctions.js" type="text/javascript"></script>
<script src="../../js/qtip.js" type="text/javascript"></script>
</head>
<body onload="loadRequired();">

	<div id="bookbox" class="admincolor">
	<?php 
$extrabuttons=array();
$extrabuttons['delete']=array('name'=>'sub','value'=>'Delete');
three_buttonmenu($extrabuttons,$book);
 ?>

	<div id="heading">
	  <label><?php print_string('transport','admin'); ?></label>
			<?php print $Student['DisplayFullName']['value'];?>
	</div>



	<div id="viewcontent" class="content">

		<form id="formtoprocess" name="formtoprocess" method="post" 
									action="transport_editor_action.php">

		  <fieldset class="divgroup center">
			<legend>
			  <?php print get_string(displayEnum($day,'dayofweek'),$book). '  '.display_date($date);?>
			</legend>

			<div class="left">
<?php 
		$listlabel='bus'; $listname='busid'; $listid='bus';$selbusid=$journey['bus_id'];
		$listswitch='yes';
		$buses=(array)list_buses($direction,$day);
		$required='yes';
		include('../../scripts/set_list_vars.php');
		list_select_list($buses,$listoptions,$book);
?>
			</div>

			<div id="switchBus" class="right">
			</div>
		  </fieldset>

		  <fieldset class="divgroup left">
			<legend>
			<?php print_string('applyto',$book);?>
			</legend>
			<div class="center">
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


		  <fieldset class="divgroup right">
			<legend>
			<?php print_string('note',$book);?>
			</legend>
			<div class="center">
			  <textarea name="comment" id="Comment"   
				tabindex="<?php print $tab++;?>" rows="3" cols="30" 
				  ><?php print $booking['comment'];?></textarea>
		  </fieldset>

		<input type="hidden" name="sid" value="<?php print $sid; ?>"/>
	    <input type="hidden" name="date" value="<?php print $date; ?>"/>
		<input type="hidden" name="bookid" value="<?php print $bookid; ?>"/>
		<input type="hidden" name="openid" value="<?php print $openid; ?>"/>
	  </form>


  </div>
<?php
/* These will be switched in an out depending on selected bus */
			foreach($buses as $busid => $bus){
				print '<div id="switchBus'.$busid.'"  class="hidden">';
				$listlabel='stop'; $required='yes'; $listid='stopid'.$busid; $listname=$listid; $$listid=$stopid;
				$stops=list_bus_stops($busid);
				include('../../scripts/set_list_vars.php');
				list_select_list($stops,$listoptions,$book);
				print '</div>';
				}
?>
 

</body>
</html>
