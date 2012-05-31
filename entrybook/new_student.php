<?php
/**									new_student.php
 */

$choice='new_student.php';
$action='new_student_action.php';

three_buttonmenu();

$Student=fetchStudent();
$Enrolment=fetchEnrolment(-1);
$Student[]=$Enrolment['Siblings'];
$Student[]=$Enrolment['StaffChild'];

$Inputs=array();
//$studentfields=array('Forename','Surname','Gender','DOB','Boarder');
/* Use studentfields to limit entry for the subset of fields listed. */
$studentfields=array();
$Inputs[]=array_filter_fields($Student,$studentfields);
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

<?php
foreach($Inputs as $Input){
?>
	  <div class="center">
		  <?php $tab=xmlarray_form($Input,'','newstudent',$tab,'infobook'); ?>
	  </div>
<?php
	}
?>

	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print '';?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>