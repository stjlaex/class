<?php 
/**												entrybook.php
 *	This is the hostpage for the entrybook.
 */

$host='entrybook.php';
$book='entrybook';
$current='';
$choice='';
$action='';
$cancel='';

include('scripts/head_options.php');

if(!isset($_SESSION['enrolstatus'])){$_SESSION['enrolstatus']='';}
if(!isset($_SESSION['entryyid'])){$_SESSION['entryyid']='';}

if(isset($_POST['newenrolstatus'])){
	if($_SESSION['enrolstatus']!=$_POST['newenrolstatus']){
		$_SESSION['enrolstatus']=$_POST['newenrolstatus']; 
		$_POST['newyid']='';
		}
	}
if(isset($_POST['newyid'])){
	if($_SESSION['entryyid']!=$_POST['newyid']){
		$_SESSION['entryyid']=$_POST['newyid']; 
		}
	}

$enrolstatus=$_SESSION['enrolstatus'];
$yid=$_SESSION['entryyid'];

if(isset($_POST['current'])){$current=$_POST['current'];}
if(isset($_POST['choice'])){$choice=$_POST['choice'];}
if(isset($_POST['cancel'])){$cancel=$_POST['cancel'];}
if(isset($_GET['choice'])){$choice=$_GET['choice'];}
if(isset($_GET['cancel'])){$cancel=$_GET['cancel'];}
if(isset($_GET['current'])){$current=$_GET['current'];}
?>

  <div id="bookbox" class="entrybookcolor">
<?php
	if($current!=''){
		$view = 'entrybook/'.$current;
		include($view);
		}
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">
	<form id="entrybookchoice" name="entrybookchoice" method="post" 
		action="entrybook.php" target="viewentrybook">
	  <fieldset class="entrybook selery">
		<legend><?php print_string('addnew');?></legend>
<?php
	$choices=array('new_student.php' => 'student'
			   ,'new_staff.php' => 'staff'
			   );
	selery_stick($choices,$choice,$book);
?>
	  </fieldset>
<?php 
	if($choice=='new_student.php'){
?>
	<fieldset class="entrybook">
		<legend><?php print_string('addto');?></legend>
<?php
		$onsidechange='yes';
		include('scripts/list_enrolstatus.php');
		if($enrolstatus=='C' or $enrolstatus=='G' or $enrolstatus=='S'
					or $enrolstatus=='M'){
			$onsidechange='yes';
			include('scripts/list_year.php');
			}
?>
	</fieldset>
<?php
		}
?>
	</form>
  </div>

<?php
include('scripts/end_options.php');
?>
