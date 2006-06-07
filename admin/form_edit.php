<?php 
/**											   form_edit.php
 */
$action='form_edit_action.php';
$cancel='formgroup_matrix.php';

if(isset($_GET{'newfid'})){$newfid=$_GET{'newfid'};}
if(isset($_GET{'newtid'})){$newtid=$_GET{'newtid'};}else{$newtid='';}
if(isset($_POST{'newfid'})){$newfid=$_POST{'newfid'};}
if(isset($_POST{'newtid'})){$newtid=$_POST{'newtid'};}

/*Check user has permission to edit*/
$d_test=mysql_query("SELECT yeargroup_id FROM form WHERE id='$newfid'");
$formyid=mysql_result($d_form,0);
$perm=getFormPerm($newfid,$respons);
$neededperm='w';
include('scripts/perm_action.php');

$d_form=mysql_query("SELECT * FROM form WHERE id='$newfid'");
$form=mysql_fetch_array($d_form, MYSQL_ASSOC);
$yid=$form['yeargroup_id'];
if($yid==0){$yid='%';}
$d_year=mysql_query("SELECT name FROM yeargroup WHERE id='$yid'");
$year=mysql_result($d_year,0);

$extrabuttons['unassignclass']=array('name'=>'sub','value'=>'Unassign');
three_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <div style="width:35%;float:left;">
		<table class="listmenu">
		  <caption>
			<?php print_string('current');?> 
			<?php print $year;?>
			<?php print_string('formgroup');?>
		  </caption>
		  <tr>
		  <th><?php print $newfid."/".$newtid; ?></th>
			<th><?php print_string('remove');?></th>
		  </tr>
<?php
	$c=0;
	$d_student = mysql_query("SELECT id, surname,
				forename, form_id, yeargroup_id FROM student  
				WHERE form_id='$newfid' ORDER BY surname");
	while ($student = mysql_fetch_array($d_student, MYSQL_ASSOC)){
			$sid = $student{'id'};
		    print "<tr><td>".$student{'forename'}."
		    ".$student{'surname'}." (".$student{'form_id'}.")</td>";
		    print "<td><input type='checkbox' name='".$sid."' /></td>";
		    print "</tr>";
		    $c++;
			}
?>
		</table>
	  </div>
	  <div  style="width:63%;float:right;">
		<fieldset class="center">
		<legend><?php print_string('reflectthesechangesinsubjectclasses',$book);?></legend>
		  <div class="left">
			<label><?php print_string('yes');?></label>
			<input type="radio" name="classestoo"  value="yes" checked />
		  </div>
		  <div class="right">
		  <label><?php print_string('no');?></label>
			<input type="radio" name="classestoo"  value="no" />
		  </div>
		</fieldset>
		<fieldset class="left">
		  <legend><?php print_string('studentsnotinaform',$book);?></legend>
		  <select name="newsid[]" size="20" multiple="multiple">	
<?php
/*	Fetch students for these classes.  */
   	$d_student = mysql_query("SELECT id, surname, forename, form_id FROM
			student WHERE yeargroup_id LIKE '$yid' AND (form_id='' OR
				form_id IS NULL) ORDER BY surname");
	while($student = mysql_fetch_array($d_student,MYSQL_ASSOC)) {
			print "<option ";
			print  " value='".$student{'id'}."'>".$student{'surname'}.", 
				".$student{'forename'}." (".$student{'form_id'}.")</option>";
			}
?>
		  </select>
		</fieldset>

		<fieldset class="right">
		  <legend><?php print_string('studentsalreadyinaform',$book);?></legend>
		  <select name="newsid[]" size="20" multiple="multiple">	
<?php
/*	Select all those assigned already in this subject and yeargroup*/
  	$d_student = mysql_query("SELECT id, forename,
					surname, form_id FROM student WHERE
					yeargroup_id LIKE '$yid' AND form_id!='' ORDER BY form_id, surname"); 
	while($student = mysql_fetch_array($d_student,MYSQL_ASSOC)) {
			print "<option ";
			print	" value='".$student{'id'}."'>".$student{'surname'}.", ".$student{'forename'}." (".$student{'form_id'}.")</option>";
			}
?>		
		  </select>
		</fieldset>
	  </div>
	<input type="hidden" name="newfid" value="<?php print $newfid;?>" /> 
	<input type="hidden" name="newtid" value="<?php print $newtid;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
