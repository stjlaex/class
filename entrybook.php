<?php 
/**												entrybook.php
 *
 *	This is the hostpage for the entrybook.
 */

$host='entrybook.php';
$book='entrybook';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');
$session_vars=array('enrolstatus','enrolyid','enrolyear');
include('scripts/set_book_session_vars.php');

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
		if($enrolstatus==''){$enrolstatus='EN';$_SESSION[$book.'enrolstatus']=$enrolstatus;}
		$onsidechange='yes';
		include('scripts/list_enrolstatus.php');
		$onsidechange='yes';
		if($enrolstatus=='C' or $enrolstatus=='G' or $enrolstatus=='S'
					or $enrolstatus=='M'){
			/*on current roll so can only be this academic year*/
			$enrolyear=get_curriculumyear();$_SESSION[$book.'enrolyear']=$enrolyear;
			}
		else{
			$listname='enrolyear';$listlabel='academicyear';
			if($enrolyear==''){$enrolyear=get_curriculumyear();$_SESSION[$book.'enrolyear']=$enrolyear;}
			include('scripts/list_calendar_year.php');
			}
		$onsidechange='yes';
		if($enrolyid==''){$enrolyid='1';$_SESSION[$book.'enrolyid']=$enrolyid;}
		$selenrolyid=$enrolyid;$listname='enrolyid';
		include('scripts/list_year.php');
?>
		<div id="switchenrol">
		</div>

	</fieldset>
<?php
		}
?>
	</form>
  </div>

<?php
include('scripts/end_options.php');
?>
