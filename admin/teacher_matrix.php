<?php 
/**													teacher_matrix.php
 */

$choice='teacher_matrix.php';
$action='teacher_matrix_action.php';

list($crid,$bid,$error)=checkCurrentRespon($r,$respons);
if($error!=''){include('scripts/results.php');exit;}

$tids=getTeachingStaff($crid,$bid);

three_buttonmenu();
?>
  <div class="topform">
	<form id="formtoprocess" name="formtoprocess" method="post"
	  action="<?php print $host; ?>" >
	  <fieldset class="left">
		<legend><?php print_string('teachers',$book);?></legend>
		<div class="left">
		  <label><?php print_string('subject',$book);?>
			<select name="subtid" size="1">
<?php
   	print '<option value="" selected="selected" ></option>';
    for($c=0;$c<sizeof($tids);$c++){
   		$tid=$tids[$c];
   		print '<option  value="'.$tid.'">'.$tid.'</option>';
   		}
?>		
			</select>
		  </label>
		</div>
		<div class="right">
			<label><?php print_string('unassigned',$book);?>
			<select name="tid" size="1">
<?php
	$othertids=array_diff(getTeachingStaff(),$tids);
   	print '<option value="" selected="selected" ></option>';		
   	while(list($index,$othertid)=each($othertids)){
   		print '<option ';
  		print	' value="'.$othertid.'">'.$othertid.'</option>';
   		}
?>		
			</select>
		  </label>
		</div>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('classes',$book);?></legend>
		<div class="left">
		  <label for="Unassigned"><?php print_string('unassigned',$book);?></label>
		  <select id="Unassigned" name="newcid[]" size="6" multiple="multiple">
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
		  <select id="Assigned" name="newcid[]" size="6" multiple="multiple">
<?php
  	$d_cids=mysql_query("SELECT DISTINCT class.id FROM class LEFT
  	JOIN tidcid ON class.id=tidcid.class_id WHERE tidcid.class_id IS
  	NOT NULL AND class.subject_id LIKE '$bid' AND class.course_id LIKE '$crid' ORDER BY
  	class.subject_id, class.course_id, id"); 
	while($cids=mysql_fetch_array($d_cids,MYSQL_ASSOC)){
		print '<option ';
		print	' value="'.$cids{'id'}.'">'.$cids{'id'}.'</option>';
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
    for($c=0;$c<sizeof($tids);$c++){
		$tid=$tids[$c];
	   	print '<tr><td>'.$tid.'</td>';
		print '<td>';
	   	$d_class=mysql_query("SELECT class_id  FROM tidcid WHERE
					teacher_id='$tid' ORDER BY class_id");   
	   	while($class=mysql_fetch_array($d_class,MYSQL_ASSOC)){
			$cids=$class['class_id'];
			print '<a href="admin.php?current=class_edit.php&cancel='.$choice.'&newtid='.$tid.'&newcid='.$cids.'">'.$cids.'</a>&nbsp&nbsp';
			}
		print '</td></tr>';
		}
?>
	  </table>
  </div>
















