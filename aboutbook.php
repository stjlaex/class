<?php 
/**														aboutbook.php
 *	This is the hostpage for the aboutbook
 *	The page to be included is set by $current
 *	A preselected menu option is set by $choice
 */

$host='aboutbook.php';
$book='aboutbook';
$current='about.php';
$choice='';
$cancel='';

include ('scripts/head_options.php');

if(isset($_SESSION{'aboutcurrent'})){$current=$_SESSION{'aboutcurrent'};}
if(isset($_SESSION{'aboutchoice'})){$choice=$_SESSION{'aboutchoice'};}
if(isset($_GET{'current'})){$current=$_GET{'current'};}
if(isset($_GET{'choice'})){$choice=$_GET{'choice'};}
if(isset($_GET{'cancel'})){$choice=$_GET{'cancel'};}
if(isset($_POST{'current'})){$current=$_POST{'current'};}
if(isset($_POST{'choice'})){$choice=$_POST{'choice'};}
if(isset($_POST{'cancel'})){$cancel=$_POST{'cancel'};}
$_SESSION{'aboutcurrent'}=$current;
$_SESSION{'aboutchoice'}=$choice;
?>
  <div id="bookbox" class="aboutcolor">
<?php
	$view = 'aboutbook/'.$current;
	include($view);
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions">	
	<fieldset class="aboutbook">
	  <legend><?php print_string('helpandsupport');?></legend>
	  <form id="aboutchoice" name="aboutchoice" method="post" 
				action="aboutbook.php" target="viewaboutbook">

		<select name="current" size="4" onChange="document.aboutchoice.submit();">
		  <option 
<?php 
if($choice=='report_bug.php'){print "selected='selected' ";} 
?>
		value="report_bug.php"><?php print_string('reportbug',$book);?></option>
		  <option 
<?php 
		if($choice=='request_feature.php'){print "selected='selected' ";} 
?>
		value="request_feature.php"><?php print_string('requestfeature',$book);?></option>
<?php
		if(isset($CFG->clientid)){
?>
	  <option 
<?php 
		if($choice=='support.php'){print "selected='selected' ";} 
?>
		value="support.php"><?php print_string('contactsupport',$book);?></option>
<?php
			}
?>
		</select>
	  </form>
	</fieldset>
  </div>

<?php include('scripts/end_options.php');?>
