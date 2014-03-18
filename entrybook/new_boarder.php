<?php
/**									new_boarder.php
 */

if(isset($_POST['sid'])){
	/*this means called from new_student_action.php*/
	$choice='new_student.php';
	$action='new_boarder_action.php';
	$sid=$_POST['sid'];
	$Student=fetchStudent_short($sid);
	}
else{
	$choice='new_booking.php';
	$action='new_booking_action.php';
	}

$Stay=fetchStay();

three_buttonmenu();


$Inputs=array();
$fields=array();
$Inputs[]=array_filter_fields($Stay,$fields);

if(isset($sid)){
?>
    <div id="heading">
        <h4><label><?php print_string('stay'); ?></label> <?php print $Student['DisplayFullName']['value'];?></h4>
    </div>
<?php
	}
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

<?php
while(list($index,$Input)=each($Inputs)){
?>
	  <div class="center">
		  <?php $tab=xmlarray_form($Input,'','stay',$tab,'infobook'); ?>
	  </div>
<?php
	}
?>

<?php
if(isset($sid)){
?>
	    <input type="hidden" name="sid" value="<?php print $sid;?>">
<?php
		}
?>
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print '';?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>
