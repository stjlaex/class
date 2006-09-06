<?php 
/**											   form_edit.php
 */
$action='form_edit_action.php';
$cancel='formgroup_matrix.php';

if(isset($_GET{'newfid'})){$fid=$_GET{'newfid'};}
if(isset($_GET{'newtid'})){$newtid=$_GET{'newtid'};}else{$newtid='';}
if(isset($_POST{'fid'})){$fid=$_POST{'fid'};}
if(isset($_POST{'newtid'})){$newtid=$_POST{'newtid'};}

	/*Check user has permission to edit*/
	$perm=getFormPerm($fid,$respons);
	$neededperm='w';
	include('scripts/perm_action.php');

	$d_form=mysql_query("SELECT * FROM form WHERE id='$fid'");
	$form=mysql_fetch_array($d_form, MYSQL_ASSOC);
	$yid=$form['yeargroup_id'];
	if($yid==0){$yid='%';}
	$d_year=mysql_query("SELECT name FROM yeargroup WHERE id='$yid'");
	$year=mysql_result($d_year,0);

	$extrabuttons['renamegroup']=array('name'=>'current','value'=>'form_edit_rename.php');
	three_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <div style="width:33%;float:left;">
		<table class="listmenu">
		  <caption>
			<?php print_string('current');?> 
			<?php print $year;?>
			<?php print_string('formgroup');?>
		  </caption>
		  <tr>
		  <th><?php print $fid.'/'.$newtid; ?></th>
			<th><?php print_string('remove');?></th>
		  </tr>
<?php
	$d_student=mysql_query("SELECT id, surname,
				forename, form_id, yeargroup_id FROM student  
				WHERE form_id='$fid' ORDER BY surname");
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
			$sid=$student['id'];
		    print '<tr><td>'.$student['surname']. 
					', '.$student['forename']. ' ('.$student['form_id'].')</td>';
		    print '<td><input type="checkbox" name="oldsids[]" value="'.$sid.'" /></td>';
		    print '</tr>';
			}
?>
		</table>
	  </div>

	  <div style="width:67%;float:right;">
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

		<fieldset class="center">
		<legend><?php print_string('choosestudentstoadd',$book);?></legend>
		<div class="left">
		  <label><?php print_string('studentsnotinaform',$book);?></label>
		  <select name="newsids[]" size="24" multiple="multiple" style="width:98%;">
<?php
   	$d_student=mysql_query("SELECT id, surname, forename, form_id FROM
			student WHERE yeargroup_id LIKE '$yid' AND (form_id='' OR
				form_id IS NULL) ORDER BY surname");
	while($student=mysql_fetch_array($d_student,MYSQL_ASSOC)) {
			print '<option ';
			print  ' value="'.$student['id'].'">'.$student['surname'].', 
				'.$student['forename'].' ('.$student['form_id'].')</option>';
			}
?>
		  </select>
		</div>

		<div class="right">
		  <label><?php print_string('studentsalreadyinaform',$book);?></label>
		  <select name="newsids[]" size="24" multiple="multiple" style="width:98%;">	
<?php
  	$d_student=mysql_query("SELECT id, forename,
					surname, form_id FROM student WHERE
					yeargroup_id LIKE '$yid' AND form_id!='' ORDER BY form_id, surname"); 
	while($student=mysql_fetch_array($d_student,MYSQL_ASSOC)){
			print '<option ';
			print	' value="'.$student['id'].'">'.$student['surname']. 
					', '.$student['forename'].' ('.$student['form_id'].')</option>';
			}
?>		
		  </select>
		</div>
		</fieldset>
	  </div>
	<input type="hidden" name="fid" value="<?php print $fid;?>" /> 
	<input type="hidden" name="name" value="<?php print $fid;?>" /> 
	<input type="hidden" name="newtid" value="<?php print $newtid;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
