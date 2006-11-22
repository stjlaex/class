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
$action='';
$cancel='';

include ('scripts/head_options.php');

if(isset($_SESSION['aboutcurrent'])){$current=$_SESSION['aboutcurrent'];}
if(isset($_SESSION['aboutchoice'])){$choice=$_SESSION['aboutchoice'];}
if(isset($_GET['current'])){$current=$_GET['current'];}
if(isset($_GET['choice'])){$choice=$_GET['choice'];}
if(isset($_GET['cancel'])){$choice=$_GET['cancel'];}
if(isset($_POST['current'])){$current=$_POST['current'];}
if(isset($_POST['choice'])){$choice=$_POST['choice'];}
if(isset($_POST['cancel'])){$cancel=$_POST['cancel'];}
$_SESSION['aboutcurrent']=$current;
$_SESSION['aboutchoice']=$choice;
?>
  <div id="bookbox" class="aboutcolor">
<?php
	$view = 'aboutbook/'.$current;
	include($view);
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions">	
	<form id="aboutchoice" name="aboutchoice" method="post" 
	  action="aboutbook.php" target="viewaboutbook">
	  <fieldset class="aboutbook selery">
		<legend><?php print_string('helpandsupport');?></legend>
<?php
	$choices=array('report_bug.php' => 'reportbug'
			   ,'request_feature.php' => 'requestfeature'
			   ,'support.php' => 'contactsupport'
			   );

	selery_stick($choices,$choice,$book);
?>
	  </fieldset>
	</form>
  </div>

<?php include('scripts/end_options.php');?>
