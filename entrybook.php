<?php 
/**												entrybook.php
 *	This is the hostpage for the entrybook.
 *	
 */
$host='entrybook.php';
$book='entrybook';
$current='';
$choice='';
$cancel='';

include('scripts/head_options.php');

if(isset($_POST{'newyid'})){
	if($_SESSION{'entryyid'}!=$_POST{'newyid'}){
		$_SESSION{'entryyid'}=$_POST{'newyid'}; 
		}
	}

$yid=$_SESSION['entryyid'];

if(isset($_POST{'current'})){$current=$_POST{'current'};}
if(isset($_POST{'choice'})){$choice=$_POST{'choice'};}
if(isset($_POST{'cancel'})){$cancel=$_POST{'cancel'};}
if(isset($_GET{'choice'})){$choice=$_GET{'choice'};}
if(isset($_GET{'cancel'})){$cancel=$_GET{'cancel'};}
if(isset($_GET{'current'})){$current=$_GET{'current'};}

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
	<fieldset class="entrybook"><legend><?php print_string('addnew');?></legend>
		<select name="current" size="6" onChange="document.entrybookchoice.submit();">
		  <option 
			<?php if($choice=='new_student.php'){print 'selected="selected" ';} ?>
				value='new_student.php'>
			<?php print_string('student');?>
		  </option>
		  <option 
			<?php if($choice=='new_staff.php'){print 'selected="selected" ';} ?>
				value='new_staff.php'>
			<?php print_string('staff');?>
		  </option>
		</select>

	</fieldset>
<?php 
	if($choice=='new_student.php'){
?>
	<fieldset class="entrybook"><legend><?php print_string('addto');?></legend>
<?php	
		$onchange='yes';
		include('scripts/list_year.php');
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
