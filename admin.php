<?php 
/**													admin.php
 *	This is the hostpage for the admin tools.
 */

$host='admin.php';
$book='admin';

include('scripts/head_options.php');
include('scripts/book_variables.php');

$rtid=$tid;
?>
  <div id="bookbox" class="admincolor">
<?php	
	if($current!=''){
		include($book.'/'.$current);
		}
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions">
	<form id="adminchoice" name="adminchoice" method="post" 
							action="admin.php" target="viewadmin">
	  <fieldset class="admin selery">
		<legend><?php print_string('manage');?></legend>
<?php
	$choices=array('formgroup_matrix.php' => 'formgroups'
			   ,'yeargroup_matrix.php' => 'yeargroups'
			   ,'community_group.php' => 'communitygroups'
			   );
	selery_stick($choices,$choice,$book);
	if($_SESSION['role']=='admin' or $_SESSION['role']=='teacher'){
		$choices=array('teacher_matrix.php' => 'subjectclasses'
			   ,'responsables.php' => 'responsibilities'
			   ,'staff_details.php' => 'staffdetails'
			   ,'counter.php' => 'logcounter'
			   );
		selery_stick($choices,$choice,$book);
		}
?>
	</fieldset>
  </form>

<?php 
	if($rtid=='administrator'){
?>

	<form id="configadminchoice" name="configadminchoice" method="post" 
	  action="admin.php" target="viewadmin">
	  <fieldset class="admin selery">
		<legend><?php print_string('configure','admin');?></legend>
<?php
	$choices=array('import_students.php' => 'newstudents'
			   ,'new_teacher.php' => 'newteachers'
			   ,'passwords.php' => 'refreshpasswords'
			   ,'class_matrix.php' => 'classesmatrix'
			   ,'cohort_matrix.php' => 'cohortmatrix'
			   ,'update_curriculum.php' => 'updatecurriculum'
			   ,'year_end.php' => 'yearend'
			   ,'server_test.php' => 'servertest'
				   /*these are all either very experimental or completely useless!!!!!*/
				   //,'ldap_start.php' => 'ldaptest'
				   //,'enrol_student.php' => 'enrolstudents'
				   //,'statementbank.php' => 'statementbank'
				   //,'demoiser.php' => 'demoiser'
			   );
	selery_stick($choices,$choice,$book);
?>
	  </fieldset>
	</form>
<?php	} ?>

  </div>
<?php include('scripts/end_options.php'); ?>
