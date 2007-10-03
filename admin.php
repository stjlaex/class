<?php 
/**													admin.php
 *	This is the hostpage for the admin tools.
 */

$host='admin.php';
$book='admin';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');

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
	if($_SESSION['role']=='admin' or $_SESSION['role']=='office' 
	   or $_SESSION['role']=='district'){
		$choices['enrolments_matrix.php']='enrolments';
		//$choices['invoices.php']='invoices';
		//$choices['accomodation_matrix.php']='accomodation';
		}
	if($_SESSION['role']=='admin' or $_SESSION['role']=='teacher' 
	   or $_SESSION['role']=='office'){
		$choices['teacher_matrix.php']='subjectclasses';
		}
	if($_SESSION['role']=='admin' or $_SESSION['role']=='teacher'){
		$choices['responsables.php']='responsibilities';
		$choices['staff_details.php']='staffdetails';
		}
	if($_SESSION['role']=='admin' or $_SESSION['role']=='teacher'
	   or $_SESSION['role']=='district'){
		$choices['class_nos.php']='classnumbers';
		$choices['usage.php']='logcounter';
		}
	selery_stick($choices,$choice,$book);
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
			   ,'portfolio_accounts.php' => 'refreshportfolios'
			   ,'server_test.php' => 'servertest'
				   /*these are all either very experimental or completely useless!!!!!*/
				   //,'statementbank.php' => 'statementbank'
				   //,'demoiser.php' => 'demoiser'
			   );
		selery_stick($choices,$choice,$book);
?>
	  </fieldset>
	</form>
<?php
		} 
?>
  </div>
<?php include('scripts/end_options.php'); ?>
