<?php 
/**								  		formgroup_matrix.php
 */

$choice='formgroup_matrix.php';
$action='formgroup_matrix_action.php';

three_buttonmenu();
?>
  <div class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	<fieldset class="right">
		  <legend><?php print_string('assignformtoteacher',$book);?></legend>

		<div class="center">
<?php include('scripts/list_teacher.php');?>
		</div>

		<div class="center">
		  <label for="Forms" ><?php print_string('unassignedformgroups',$book);?></label>
		  <select id="Forms" name="newfid" size="1" style="width:95%;">
<?php
  	$d_form=mysql_query("SELECT id FROM form WHERE teacher_id='' OR
					teacher_id IS NULL ORDER BY yeargroup_id"); 
   	while($form=mysql_fetch_array($d_form,MYSQL_ASSOC)){
   		print '<option ';
		print	' value="'.$form['id'].'">'.$form['id'].'</option>';
		}
?>		
		  </select>
		</div>

	</fieldset>

	<div class="left">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('formgroup');?></th>
		  <th><?php print_string('numberofstudents');?></th>
		  <th><?php print_string('formtutor');?></th>
		</tr>
<?php
	$d_form=mysql_query("SELECT * FROM form ORDER BY yeargroup_id");
	while($form=mysql_fetch_array($d_form,MYSQL_ASSOC)){
		$fid=$form['id'];
		$d_student=mysql_query("SELECT COUNT(id) FROM student
									WHERE form_id='$fid'");
		$nosids=mysql_result($d_student,0);
		$tid=$form['teacher_id'];
	   	print '<tr><td>';
	   		print '<a href="admin.php?current=form_edit.php&cancel='.$choice.'&newtid='.$tid.'&newfid='.$fid.'">'.$fid.'</a>';
		print '</td>';
	   	print '<td>'.$nosids.'</td>';
	   	print '<td>'.$tid.'</td>';
		print '</tr>';
		}
?>
	  </table>
	</div>


	  <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>