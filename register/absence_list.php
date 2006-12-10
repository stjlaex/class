<?php
/**									absence_list.php
 *
 *   	Lists students in array sids.
 */

$action='absence_list_action.php';
$choice='absence_list.php';

//$students=(array)listinCommunity($community);
//$AttendanceEvents=fetchAttendanceEvents();

include('scripts/sub_action.php');
three_buttonmenu();
?>

  <div id="viewcontent" class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu sidtable">
		<tr>
		  <th colspan="2">&nbsp</th>
		  <th><?php print_string('student'); ?></th>
		  <th class="edit"><?php print_string('attendance',$book);?></th>
		</tr>
<?php
	$rown=1;
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
		$Student=fetchStudent_short($sid);
		$Attendances=(array)fetchAttendances($sid);
?>
		  <tr id="sid-<?php print $sid;?>">
			<td><?php print $rown++;?></td>
			<td>&nbsp</td>
			<td>
			<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">
			<?php print $Student['DisplayFullName']['value']; ?></a>
<?php
			}
?>
			</td>
		</table>

		<input type="hidden" name="date" value="<?php print $currentevent['date'];?>" />
		<input type="hidden" name="period" value="<?php print $currentevent['period'];?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	    <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  </form>
  </div>
