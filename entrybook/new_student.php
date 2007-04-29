<?php
/**									new_student.php
 */

$choice='new_student.php';
$action='new_student_action.php';

three_buttonmenu();

$Student=fetchStudent();
$guestfields=array();

$Inputs=array();
$labels=array();
if($enrolyid==200){
	/*special residencial yeargroup*/
	$Student['Boarder']['value']='B';
	$studentfields=array('Forename','Surname','Gender','DOB','Boarder');
	}

$Inputs[]=array_filter_fields($Student,$studentfields);
$labels[]='newstudent';

if($Student['Boarder']['value']!='N' and $Student['Boarder']['value']!=''){
	/*extra fields for residencial students*/
	$Inputs[]=fetchStay();
	$labels[]='stay';
	}
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

<?php
while(list($index,$Input)=each($Inputs)){
?>
	  <div class="center">
		  <?php $tab=xmlarray_form($Input,'',$labels[$index],$tab,'infobook'); ?>
	  </div>
<?php
	}
?>

	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print '';?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>