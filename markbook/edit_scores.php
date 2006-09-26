<?php 
/**				   								edit_scores.php
 */

$action='edit_scores_action.php';

$viewtable=$_SESSION{'viewtable'};
$umns=$_SESSION{'umns'};
$mid=$_GET{'mid'};
$col=$_GET{'col'};
$scoretype=$_GET{'scoretype'};
$grading_name=$_GET{'grading_name'};

three_buttonmenu();
?>
  <div id="heading">
	<?php print $umns[$col]['topic'];?>
  </div>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 
	  <table class="listmenu" id="editscores">
		<tr><th colspan="3"></th>
<?php
	if($scoretype=='grade'){
		$d_grading=mysql_query("SELECT grades FROM grading WHERE name='$grading_name'");
		$grading_grades=mysql_result($d_grading,0);
		$pairs=explode (';', $grading_grades);
		print '<th style="width:15%;">'.$grading_name.'</th>';
		}
	else{
?>
		  <th style="width:15%;">
			<?php print_string('decimalvalue',$book); ?>
		  </th>
<?php
		}
	if($scoretype=='percentage'){
		$total=$umns[$col]['mark_total'];
?>
		  <th style="width:15%;">
			<?php print_string('total');?><br />
			  (<?php print_string('default',$book);?>=<?php print $total;?>)
		  </th>
		  <th>
			<?php print_string('shortnote',$book);?>
		  </th>
<?php
		}
	else{
?>
		  <th colspan="2"><?php print_string('shortnote',$book);?></th>
<?php
		}
?>
		</tr>
<?php
	for($c=0;$c<sizeof($viewtable);$c++){
		$sid=$viewtable[$c]['sid'];
		$tab=$c+1;
		print '<tr>';
		print '<td>'.$viewtable[$c]['surname'].'</td>';
		print '<td>'.$viewtable[$c]['forename'].$viewtable[$c]['preferredforename'].'</td>';
		print '<td>'.$viewtable[$c]['form_id'].'</td>';
		if($scoretype=='grade'){
?>		
		<td>
		  <select tabindex='<?php print $tab;?>' name='<?php print $sid;?>'>
<?php 
		print '<option value="" ';
		if ($viewtable[$c]["score$mid"]['grade']==''){print 'selected';}	
		print ' ></option>';

		for($c3=0; $c3<sizeof($pairs); $c3++){
			list($level_grade, $level)=split(':',$pairs[$c3]);
			print '<option value="'.$level.'" ';
			if($viewtable[$c]["score$mid"]['grade'] == $level){print 'selected';}	
			print '>'.$level_grade.'</option>';
			}
?>
		  </select>
		</td>
<?php
			}
		else {
			print '<td><input pattern="decimal" type="text" tabindex="'.$tab.'" name="'.$sid.'" maxlength="8" value="'.$viewtable[$c]["score$mid"]['value'].'" /></td>';
			}
		if($scoretype=='percentage'){
			print '<td><input pattern="decimal" type="text" name="total'.$sid.'" maxlength="8" value="'.$viewtable[$c]["score$mid"]['outoftotal'].'" /></td>';
			}
		else{print '<td>&nbsp;</td>';}
		print '<td><input type="text" style="width:80%"
			name="comm'.$sid.'" maxlength="98" value="';
		print $viewtable[$c]["score$mid"]['comment'].'" /></td>';	      
		print '</tr>';
		}
?>
	  </table>
	
	<input type="hidden" name="mid" value="<?php print $mid;?>" />
	<input type="hidden" name="col" value="<?php print $col;?>" />
	<input type="hidden" name="scoretype" value="<?php print $scoretype;?>" />
	<input type="hidden" name="grading_grades" value="<?php print $grading_grades;?>" />
	<input type="hidden" name="total" value="<?php print $total;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
	</form>
  </div>
