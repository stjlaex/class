<?php 
/**												entrybook.php
 *	This is the hostpage for the entrybook.
 */

$host='entrybook.php';
$book='entrybook';

include('scripts/head_options.php');
include('scripts/book_variables.php');

if(!isset($_SESSION['enrolstatus'])){$_SESSION['enrolstatus']='EN';}
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

?>
  <div id="bookbox" class="entrybookcolor">
<?php
	if($current!=''){
		include($book.'/'.$current);
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
				   ,'new_contact.php' => 'contact'
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
