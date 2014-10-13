<?php
/**											class_nos.php
 *
 *	Select which subjects are taught for which courses
 */

$action='class_nos.php';
$choice='class_nos.php';
if(isset($_POST['view'])){$view=$_POST['view'];}else{$view='stages';}

two_buttonmenu($extrabuttons);
$curryear=get_curriculumyear();
?>
  <div id="heading">
	<form id="headertoprocess" name="headertoprocess" 
							method="post" action="<?php print $host;?>">
		<label for="view"><?php print get_string('view', $book);?></label>
		<select name="view" onchange="processHeader(this)">
			<option value="stages" <?php if($view=='stages'){echo "selected";} ?>><?php print_string('stages',$book); ?></option>
			<option value="yeargroups" <?php if($view!='stages'){echo "selected";} ?>><?php print_string('yeargroups',$book); ?></option>
		</select>
	 	<input type="hidden" name="current" value="<?php print $current;?>">
	 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
	 	<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>
  <div class="content" id="viewcontent">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
<?php
		if($view=='stages'){
			include('class_nos_course_stages.php');
			}
		else{
			include('class_nos_section_yeargroups.php');
			}
?>
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>
