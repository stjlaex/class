<?php
/**									new_contact.php
 */

if(isset($_POST['sid'])){
	$choice='new_student.php';
	$sid=$_POST['sid'];
	$Contact=fetchContact(array('guardian_id'=>'-1','student_id'=>'-1'));
	$Student=fetchStudent_short($sid);
	}
else{
	$choice='new_contact.php';
	$Contact=fetchContact(array('guardian_id'=>'-1'));
	}
$action='new_contact_action.php';

three_buttonmenu();

$Address=fetchAddress(array('guardian_id'=>'-1'));
$Phone=fetchPhone(array('guardian_id'=>'-1'));

if(isset($sid)){
?>
  <div id="heading">
	<label><?php print_string('contactfor'); ?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>
<?php
	}
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		  <?php $tab=xmlarray_form($Contact,'','newcontact',$tab,'infobook'); ?>
	  </div>

	  <div class="left">
		  <?php $tab=xmlarray_form($Address,'','contactaddress',$tab,'infobook'); ?>
	  </div>

	  <div class="right">
		  <?php $tab=xmlarray_form($Phone,'','contactphones',$tab,'infobook'); ?>
	  </div>
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