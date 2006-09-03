<?php 
/**								  		yeargroup_matrix.php
 */

$choice='yeargroup_matrix.php';
$action='yeargroup_matrix_action.php';

$tids=getTeachingStaff();

three_buttonmenu();
?>
  <div class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	<fieldset class="right">
		  <legend><?php print_string('assignyeartoteacher',$book);?></legend>

		<div class="center">
		  <label for="Teachers"><?php print_string('teachers',$book);?></label>
			<select id="Teachers" name="tid" size="1" style="width:95%;">
<?php
	print '<option value="" selected="selected" ></option>';		
   	while(list($index,$tid)=each($tids)){
   		print '<option ';
  		print	' value="'.$tid.'">'.$tid.'</option>';
   		}
?>		
			</select>
		</div>

		<div class="center">
		  <label for="Forms" ><?php print_string('unassignedyeargroups',$book);?></label>
		  <select id="Forms" name="newfid" size="1" style="width:95%;">
<?php
  	$d_year=mysql_query("SELECT id, name FROM yeargroup ORDER BY id"); 
   	while($year=mysql_fetch_array($d_year,MYSQL_ASSOC)){
   		print '<option ';
		print	' value="'.$year['id'].'">'.$year['name'].'</option>';
		}
?>		
		  </select>
		</div>

	</fieldset>

	<div class="left">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('yeargroup');?></th>
		  <th><?php print_string('numberofstudents');?></th>
		  <th><?php print_string('yearresponsible');?></th>
		</tr>
<?php
	$d_year=mysql_query("SELECT * FROM yeargroup ORDER BY section_id,id");
	while($year=mysql_fetch_array($d_year,MYSQL_ASSOC)){
		$yid=$year['id'];
		$d_student=mysql_query("SELECT COUNT(id) FROM student
									WHERE yeargroup_id='$yid'");
		$nosids=mysql_result($d_student,0);
	   	print '<tr><td>';
	   		print '<a href="admin.php?current=yeargroup_edit.php&cancel='.$choice.'&newtid='.$tid.'&newyid='.$yid.'">'.$year['name'].'</a>';
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