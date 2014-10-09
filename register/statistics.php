<?php
/**									statistics.php
 *
 */

$action = 'statistics_action.php';
$choice = 'statistics.php';
if(isset($_POST['view'])){$view=$_POST['view'];}else{$view='total';}

$toyear = get_curriculumyear() - 1;
//TODO: set a proper start of term date

include ('scripts/sub_action.php');

//threeplus_buttonmenu($startday,2,$extrabuttons);
two_buttonmenu($extrabuttons);
?>
  <div id="heading">
	<form id="headertoprocess" name="headertoprocess" 
							method="post" action="<?php print $host;?>">
		<label for="view"><?php print $CFG -> schoolname . ':  ' . get_string('attendance', 'register') . ' ' . get_string('statistics', 'register');?></label>
		<select name="view" onchange="processHeader(this)">
			<option value="total" <?php if($view=='total'){echo "selected";} ?>><?php print_string('total',$book); ?></option>
			<option value="yeargroups" <?php if($view!='total'){echo "selected";} ?>><?php print_string('yeargroups',$book); ?></option>
		</select>
	 	<input type="hidden" name="current" value="<?php print $current;?>">
	 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
	 	<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>
  <div id="viewcontent" class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>">
<?php
		if($view=='total'){
			include('statistics_total.php');
			}
		else{
			include('statistics_yeargroups.php');
			}
?>
	  </form>
	</div>
<?php
$today = date('Y-m-d');
?>
		<input type="hidden" name="current" value="<?php print $action; ?>" />
		<input type="hidden" name="cancel" value="<?php print ''; ?>" />
		<input type="hidden" name="choice" value="<?php print $choice; ?>" />
