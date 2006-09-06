<?php 
/**													teacher_matrix.php
 */

$choice='teacher_matrix.php';
$action='teacher_matrix_action.php';

list($crid,$bid,$error)=checkCurrentRespon($r,$respons);
if($error!=''){include('scripts/results.php');exit;}

$teachers=getTeachingStaff($crid,$bid);

three_buttonmenu();
?>
  <div class="topform">
	<form id="formtoprocess" name="formtoprocess" method="post"
	  action="<?php print $host; ?>" >
	  <fieldset class="left">
		<legend><?php print_string('teachers',$book);?></legend>

		<div class="center">
		  <label><?php print_string('subject',$book);?></label>
			<select tabindex="<?php print $tab++;?>" name="subtid" size="1">
<?php
   	print '<option value="" selected="selected" ></option>';
   	while(list($tid,$user)=each($teachers)){
   		print '<option  value="'.$tid.'">'.$tid.' ('.$user['surname'].')</option>';
   		}
?>		
			</select>
		</div>

		<div class="center">
		  <label><?php print_string('unassigned',$book);?></label>
			<select name="tid"  tabindex="<?php print $tab++;?>" size="1">
<?php
	$allteachers=getTeachingStaff();
   	print '<option value="" selected="selected" ></option>';		
   	while(list($tid,$user)=each($allteachers)){
		if(!array_key_exists($tid,$teachers)){
			print '<option  value="'.$tid.'">'.$tid.' ('.$user['surname'].')</option>';
			}
   		}
?>		
			</select>
		</div>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('classes',$book);?></legend>
		<div class="left">
		  <label for="Unassigned"><?php print_string('unassigned',$book);?></label>
		  <select id="Unassigned" name="newcid[]"  
			tabindex="<?php print $tab++;?>" size="6" multiple="multiple">
<?php		
  	$d_cids=mysql_query("SELECT DISTINCT class.id FROM class LEFT JOIN tidcid
	  	ON class.id=tidcid.class_id WHERE tidcid.class_id IS NULL AND class.subject_id
		LIKE '$bid' AND class.course_id LIKE '$crid' 
		ORDER BY class.subject_id, class.course_id, id");
   	while($cids=mysql_fetch_array($d_cids,MYSQL_ASSOC)){
   		print '<option ';
		print	' value="'.$cids{'id'}.'">'.$cids{'id'}.'</option>';
	   	}
?>		
		  </select>
		</div>
		<div class="right">
		  <label for="Assigned"><?php print_string('assigned',$book);?></label>
		  <select id="Assigned" name="newcid[]" 
			tabindex="<?php print $tab++;?>" size="6" multiple="multiple">
<?php
  	$d_cids=mysql_query("SELECT DISTINCT class.id FROM class LEFT
  	JOIN tidcid ON class.id=tidcid.class_id WHERE tidcid.class_id IS
  	NOT NULL AND class.subject_id LIKE '$bid' AND class.course_id LIKE '$crid' ORDER BY
  	class.subject_id, class.course_id, id"); 
	while($cids=mysql_fetch_array($d_cids,MYSQL_ASSOC)){
		print '<option ';
		print	' value="'.$cids['id'].'">'.$cids['id'].'</option>';
		}
?>		
		  </select>
		</div>
	  </fieldset>

	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>

  <div class="content">
	  <table style="top:35%;" class="listmenu">
		<tr>
		  <th><?php print_string('teacher',$book);?></th>
		  <th><?php print_string('classesalreadyassigned',$book);?></th>
		</tr>
<?php
   	reset($teachers);
   	while(list($tid,$user)=each($teachers)){
		print '<tr><td>'.$tid.' ('.$user['surname'].')</td>';
		print '<td>';
	   	$d_class=mysql_query("SELECT class_id  FROM tidcid 
					JOIN class ON class.id=tidcid.class_id WHERE 
					tidcid.teacher_id='$tid' AND class.course_id LIKE '$crid' AND
					class.subject_id LIKE '$bid' ORDER BY tidcid.class_id");   
	   	while($class=mysql_fetch_array($d_class,MYSQL_ASSOC)){
			$cids=$class['class_id'];
			print '<a href="admin.php?current=class_edit.php&cancel='.$choice.'&newtid='.$tid.'&newcid='.$cids.'">'.$cids.'</a>&nbsp&nbsp';
			}
		print '</td></tr>';
		}
?>
	  </table>
  </div>
















