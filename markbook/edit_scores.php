<?php 
/**				   								edit_scores.php
 */

$action='edit_scores_action.php';


$viewtable=$_SESSION['viewtable'];
$umns=$_SESSION['umns'];
$mid=$_GET['mid'];
$col=$_GET['col'];
$scoretype=$_GET['scoretype'];
$grading_name=$_GET['grading_name'];
$total='';
$grading_grades='';
$marktype=$umns[$col]['marktype'];
if($marktype=='hw'){
	$d_m=mysql_query("SELECT entrydate, comment FROM mark WHERE id='$mid';");
	$setdate=mysql_result($d_m,0,1);
	$duedate=mysql_result($d_m,0,0);
	$setevent=get_event($setdate,'AM');
	$dueevent=get_event($duedate);
	}

three_buttonmenu();
?>
  <div id="heading">
<?php 
	print '<label>'.$umns[$col]['topic'].': '.$umns[$col]['comment'].'</label>';
?>
  </div>

  <div  id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" novalidate method="post" action="<?php print $host;?>"> 
	  <table class="listmenu sidtable" id="editscores">
		<tr>
		  <th colspan="4"></th>
<?php
	if($marktype=='hw'){
?>
		  <th style="width:3em;">
			<p><?php print get_string('dateset',$book).'<br />'. display_date($setdate);?></p>
		  </th>
		  <th style="width:3em;">
			<p><?php print get_string('datedue',$book).'<br />'. display_date($duedate);?></p>
		  </th>
		  <th style="width:3em;">
			<p><?php print get_string('returnedlate',$book).'<br />';?></p>
		  </th>
<?php
		}

	if($scoretype=='grade'){
		$d_grading=mysql_query("SELECT grades FROM grading WHERE name='$grading_name'");
		$grading_grades=mysql_result($d_grading,0);
		$pairs=explode (';', $grading_grades);
		print '<th>'.$grading_name.'</th>';
		}
	elseif($scoretype=='value'  or $scoretype=='percentage'){
		print '<th>'.get_string('decimalvalue',$book).'</th>';
		}
?>
		  <th style="background-color:#444;border-color:#444;width:10px;">&nbsp;</th>
<?php
	if($scoretype=='percentage'){
		$total=$umns[$col]['mark_total'];
?>
		  <th>
			<?php print_string('total');?><br />
			  (<?php print_string('default',$book);?>=<?php print $total;?>)
		  </th>
<?php
		}
?>

		  <th>
			<?php print_string('shortnote',$book);?>
		  </th>
		  <th></th>
		</tr>
<?php
	for($c=0;$c<sizeof($viewtable);$c++){
		$sid=$viewtable[$c]['sid'];
		$tab=$c+1;
?>
		<tr id="sid-<?php print $sid;?>">
		  <td><?php print $tab;?></td>
		  <td>&nbsp</td>
		  <td>
			<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">
			<?php if($viewtable[$c]['preferredforename']!=''){$preferredforename='&nbsp;('.$viewtable[$c]['preferredforename'].')';}else{$preferredforename='';}?>
			<?php print $viewtable[$c]['surname'];?>,&nbsp;<?php print $viewtable[$c]['forename'].'&nbsp;'.$viewtable[$c]['middlenames'].$preferredforename;?></a>
		  </td>
		  <td><?php print $viewtable[$c]['form_id'];?>&nbsp;</td>
<?php
	if($marktype=='hw'){
		$set_Attendance=fetchcurrentAttendance($sid,$setevent['id']);
		$due_Attendance=fetchcurrentAttendance($sid,$dueevent['id']);
		xmlattendance_display($set_Attendance);
		xmlattendance_display($due_Attendance);
?>

		  <td>
			<input type="checkbox" 
				   <?php if($viewtable[$c]["score$mid"]['extra']=='1'){
						 print 'checked="checked" ';} ?> 
				   name="extra<?php print $sid;?>" />
		  </td>
<?php
		}

	if($scoretype=='grade'){
?>		
		<td>
		  <select tabindex='<?php print $tab;?>' name='<?php print $sid;?>'>
<?php 
		print '<option value="" ';
		if($viewtable[$c]["score$mid"]['grade']==''){print 'selected';}	
		print ' ></option>';

		for($c3=0; $c3<sizeof($pairs); $c3++){
			list($level_grade, $level)=explode(':',$pairs[$c3]);
			print '<option value="'.$level.'" ';
			if($viewtable[$c]["score$mid"]['grade']==$level){print 'selected';}	
			print '>'.$level_grade.'</option>';
			}
?>
		  </select>
		</td>
<?php
			}
		elseif($scoretype=='value' or $scoretype=='percentage'){
			print '<td><input pattern="decimal" type="text" tabindex="'.$tab.'" name="'.$sid.'" maxlength="8" value="'.$viewtable[$c]["score$mid"]['value'].'" /></td>';
			}
?>
			<td style="background-color:#444;border-color:#444;width:10px;"></td>

<?php
		if($scoretype=='percentage'){
			print '<td><input pattern="decimal" style="background-color:#ddd;" type="text" name="total'.$sid.'" maxlength="8" value="'.$viewtable[$c]["score$mid"]['outoftotal'].'" /></td>';
			}
?>

			<td>
			  <input type="text" style="width:80%;background-color:#ddd;"
				 name="<?php print 'comm'.$sid;?>" maxlength="600" 
				value="<?php print $viewtable[$c]["score$mid"]['comment'];?>"  />
			</td>
<?php
			/* TODO: implement this for assessments only... need get eid and bid... 
			$imagebuttons=array();
			$imagebuttons['clicktoload']=array('name'=>'Attachment',
													 'onclick'=>"clickToAttachFile($sid,'$eid','$bid','$pid','$sid')", 
													 'class'=>'clicktoload',
													 'value'=>'category_editor.php',
													 'title'=>'clicktoattachfile');
			*/
			print '<td id="icon'.$sid.'" class="" style="width:18px;" >';
			//rowaction_buttonmenu($imagebuttons,array(),$book);
			print '</td></tr>';
?>
		</tr>
<?php
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
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
