<?php 
/**													admin.php
 *	This is the hostpage for the admin tools.
 */

$host='admin.php';
$book='admin';
$choice='';
$current='';
$cancel='';

include ('scripts/head_options.php');

if(isset($_SESSION{'admincurrent'})){$current=$_SESSION{'admincurrent'};}
if(isset($_SESSION{'adminchoice'})){$choice=$_SESSION{'adminchoice'};}
if(isset($_GET{'current'})){$current=$_GET{'current'};}
if(isset($_GET{'choice'})){$choice=$_GET{'choice'};}
if(isset($_GET{'cancel'})){$cancel=$_GET{'cancel'};}
if(isset($_POST{'current'})){$current=$_POST{'current'};}
if(isset($_POST{'choice'})){$choice=$_POST{'choice'};}
if(isset($_POST{'cancel'})){$cancel=$_POST{'cancel'};}
$_SESSION{'admincurrent'}=$current;
$_SESSION{'adminchoice'}=$choice;
$rtid=$tid;
$tab=1;
?>
  <div id="bookbox" class="admincolor">
<?php	
	if($current!=''){
		$view = 'admin/'.$current;
		include($view);
		}
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions">
	<fieldset class="admin"><legend><?php print_string('manage');?></legend>
	  <form id="adminchoice" name="adminchoice" method="post" 
		action="admin.php" target="viewadmin">
		<select name="current" size="6" onChange="document.adminchoice.submit();">
		  <option 
			<?php if($choice=='teacher_matrix.php'){print 'selected="selected" ';} ?>
				value='teacher_matrix.php'>
			<?php print_string('subjectclasses');?>
		  </option>
		  <option 
			<?php if($choice=='formgroup_matrix.php'){print 'selected="selected" ';} ?>
				value='formgroup_matrix.php'>
			<?php print_string('formgroups');?>
		  </option>
		  <option 
			<?php if($choice=='yeargroup_matrix.php'){print 'selected="selected" ';} ?>
				value='yeargroup_matrix.php'>
			<?php print_string('yeargroups');?>
		  </option>
		  <option 
			<?php if($choice=='responsables.php'){print 'selected="selected" ';}?>
				value='responsables.php'>
			<?php print_string('responsibilities');?>
		  </option>
		  <option 
			<?php if($choice=='staff_details.php'){print 'selected="selected" ';}?>
				value='staff_details.php'>
			<?php print_string('staffdetails');?>
		  </option>
		  <option <?php if($choice=='counter.php'){print
		'selected="selected" ';}?>value='counter.php'>
		  <?php print_string('logcounter');?></option>

		</select>
	  </form>
	</fieldset>

<?php 
	if($rtid=='administrator'){
?>

	<fieldset class="admin"><legend><?php print_string('configure','admin');?></legend>
	  <form id="configadminchoice" name="configadminchoice" method="post" 
		action="admin.php" target="viewadmin">

		<select name="current" size="10" onChange="document.configadminchoice.submit();">
		<option <?php if($choice=='import_students.php'){print
		'selected="selected" ';}?>value='import_students.php'>
			<?php print_string('newstudents');?></option>
		<option <?php if($choice=='new_teacher.php'){print
		'selected="selected" ';}?>value='new_teacher.php'>
		  <?php print_string('newteachers');?></option>
		<option <?php if($choice=='class_matrix.php'){print
				 'selected="selected" ';}?>value='class_matrix.php'>
		  <?php print_string('classesmatrix');?></option>
		<option <?php if($choice=='cohort_matrix.php'){print 'selected="selected"
			';}?>value='cohort_matrix.php'>
		  <?php print_string('cohortmatrix');?></option>
		  <?php print_string('curriculummatrix');?></option>
		<option <?php if($choice=='update_curriculum.php'){print
				'selected="selected"
		';}?>value='update_curriculum.php'>
			<?php print_string('updatecurriculum');?></option>
		<option <?php if($choice=='year_end.php'){print
				'selected="selected" ';}?>value='year_end.php'>
				<?php print_string('yearend');?></option>
		<option <?php if($choice=='passwords.php'){print
				'selected="selected" ';}?>value='passwords.php'>
				<?php print_string('refreshpasswords',$book);?></option>
		<option <?php if($choice=='fix8.php'){print
				'selected="selected" ';}?>value='fix8.php'>
				<?php print_string('upgradeto0.8');?></option>
<?php
/*these are all very experimental!!!!!

		<option <?php if($choice=='ldap_start.php'){print
				'selected="selected" ';}?>value='ldap_start.php'>
				<?php print_string('ldaptest');?></option>
		<option <?php if($choice=='enrol_student.php'){print
				'selected="selected" ';}?>value='enrol_student.php'>
				<?php print_string('enrolstudents');?></option>
		<option <?php if($choice=='server_test.php'){print
				'selected="selected" ';}?>value='server_test.php'>
				<?php print_string('servertest');?></option>
		<option <?php if($choice=='statementbank.php'){print
				'selected="selected" ';}?>value='statementbank.php'>
				<?php print_string('statementbank');?></option>
		<option <?php if($choice=='demoiser.php'){print
				'selected="selected" ';}?>value='demoiser.php'>
				<?php print_string('demoiser');?></option>
*/
?>
		</select>
	  </form>
	</fieldset>
<?php	} ?>

  </div>
<?php include('scripts/end_options.php'); ?>
