<?php
/**			       		report_attendance.php
 */

$action='report_attendance_action.php';
$choice='report_attendance.php';

if(isset($_POST['yid'])){$yid=$_POST['yid'];$selyid=$yid;}else{$yid='';}
if(isset($_POST['formid'])){$formid=$_POST['formid'];}else{$formid='';}
if(isset($_POST['houseid'])){$houseid=$_POST['houseid'];}else{$houseid='';}
if(isset($_POST['reporttype'])){$reporttype=$_POST['reporttype'];}else{$reporttype='S';}

/* Search across last four weeks by default */
$todate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-28,date('Y')));

three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('search',$book);?></label>
<?php	print_string('attendance',$book);?>
  </div>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <fieldset class="center">
		<legend><?php print_string('collateforstudentsfrom',$book);?></legend>
		  <?php  $onchange='yes'; $required='yes'; include('scripts/'.$listgroup);?>
	  </fieldset>

	  <fieldset class="left">
		<legend><?php print_string('collatesince',$book);?></legend>
<?php 
		include('scripts/jsdate-form.php'); 
?>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('collateuntil',$book);?></legend>
<?php 
		unset($todate);
		include('scripts/jsdate-form.php'); 
?>
	  </fieldset>



	  <fieldset class="right">
		<legend><?php print get_string('attendance','register').' '.get_string('type',$book);?></legend>
	  <table class="listmenu">
<?php
	$types=array('P'=>'classes','S'=>'registrationsession');
	foreach($types as $value => $type){
		if($value==$reporttype){$checked='checked="checked"';}
		else{$checked='';}
		print '<tr><th><input type="radio" name="reporttype" '.$checked .'value="'.$value.'">'.get_string($type,'register').'</input></th></tr>';
		}
?>
	  </table>
	  </fieldset>


	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	  <input type="hidden" name="cancel" value="<?php print ''; ?>">
	</form>
  </div

