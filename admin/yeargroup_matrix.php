<?php
/**								  		yeargroup_matrix.php
 */

$choice='yeargroup_matrix.php';
$action='yeargroup_matrix_action.php';

three_buttonmenu();
?>
  <div class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	<fieldset class="right">
		  <legend><?php print_string('assignyeartoteacher',$book);?></legend>

		<div class="center">
<?php $liststyle='width:95%;'; $required='yes'; include('scripts/list_teacher.php');?>
		</div>

		<div class="center">
<?php $liststyle='width:95%;'; $required='yes'; include('scripts/list_year.php');?>
		</div>

	</fieldset>

	<div class="left">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('yeargroup');?></th>
		  <th><?php print_string('numberofstudents',$book);?></th>
		  <th><?php print_string('yearresponsible',$book);?></th>
		</tr>
<?php

	$nosidstotal=0;
	$d_year=mysql_query("SELECT * FROM yeargroup ORDER BY section_id, id");
	while($year=mysql_fetch_array($d_year,MYSQL_ASSOC)){
		$yid=$year['id'];
		$d_student=mysql_query("SELECT COUNT(id) FROM student
									WHERE yeargroup_id='$yid'");
		$nosids=mysql_result($d_student,0);
		$nosidstotal=$nosidstotal+$nosids;
	   	print '<tr><td>';
	   		print '<a href="admin.php?current=yeargroup_edit.php&cancel='.$choice.'&choice='.$choice.'&newtid='.$tid.'&newyid='.$yid.'">'.$year['name'].'</a>';
		print '</td>';
	   	print '<td>'.$nosids.'</td><td>';
		$yearperms=array('r'=>1,'w'=>1,'x'=>1);/*head of years only*/
		$users=(array)getPastoralStaff($yid,$yearperms);
		while(list($index,$user)=each($users)){
			if($user['role']!='office' and $user['role']!='admin'){print $user['username'].' ';}
			}
		print '</td></tr>';
		}
?>
		  <tr>
			<th>
			  <?php print get_string('total',$book).' '.get_string('numberofstudents',$book);?>
			</th>
			<td><?php print $nosidstotal;?></td>
			<td>&nbsp;</td>
		  </tr>
	  </table>
	</div>


	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>