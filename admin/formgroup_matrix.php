<?php 
/**								  		formgroup_matrix.php
 */

$choice='formgroup_matrix.php';
$action='formgroup_matrix_action.php';

$tids=getTeachingStaff();

three_buttonmenu();
?>
  <div class="topform">
	<form id="formtoprocess" name="formtoprocess" method="post"
	  action="<?php print $host; ?>" >
	  <div class="left">
		<label><?php print_string('teachers',$book);?>
		  <select name="tid" size="1">
<?php
	print '<option value="" selected="selected" ></option>';		
   	while(list($index,$tid)=each($tids)){
   		print '<option ';
  		print	' value="'.$tid.'">'.$tid.'</option>';
   		}
?>		
		  </select>
		</label>
	  </div>
	  <div class="right">
		<label for="Forms" ><?php print_string('unassignedformgroups',$book);?></label>
		<select id="Forms" name="newfid" size="1">
<?php
  	$d_fids=mysql_query("SELECT id FROM form WHERE teacher_id='' OR
					teacher_id IS NULL ORDER BY yeargroup_id"); 
   	while($fids=mysql_fetch_array($d_fids,MYSQL_ASSOC)){
   		print '<option ';
		print	' value="'.$fids['id'].'">'.$fids['id'].'</option>';
		}
?>		
		</select>
	  </div>

	  <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>

  <div class="content">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('formtutor');?></th>
		  <th><?php print_string('formgroup');?></th>
		</tr>
<?php
    for($c=0;$c<sizeof($tids);$c++){
		$tid=$tids[$c];
		$d_form=mysql_query("SELECT id FROM form WHERE teacher_id='$tid'");
	   	print '<tr><td>'.$tid.'</td>';
	   	print '<td>';
		if(mysql_num_rows($d_form)>0){
	   		$fid=mysql_result($d_form,0);
	   		print '<a href="admin.php?current=form_edit.php&cancel='.$choice.'&newtid='.$tid.'&newfid='.$fid.'">'.$fid.'</a>';
	   		}
		print '</td>';
		print '</tr>';
		}
?>
	</table>
  </div>
