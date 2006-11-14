<?php
/**									register_list.php
 *
 *   	Lists students in array sids.
 */

$action='register_list_action.php';
$choice='register_list.php';

$students=(array)listinCommunity($community);
$AttendanceEvents=fetchAttendanceEvents();

include('scripts/sub_action.php');

?>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
	<div class="center">
	<table class="listmenu">
	  <th colspan="2">&nbsp</th>
	  <th colspan="2"><?php print_string('student'); ?></th>
<?php
	while(list($index,$Event)=each($AttendanceEvents['Event'])){
		$events[]=$Event['id_db'];
?>
	  <th>
		<?php print $Event['Date']['value'] .'<br />'. $Event['Period']['value'];?>
	  </th>
<?php
		}

	$tomonth=date('n')-1;/*highlights comments for past month, needs sohpisticating!!!*/
	$commentdate=date('Y').'-'.$tomonth.'-'.date('j');
	$c=1;
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
		$Student=fetchStudent_short($sid);
		$Attendances=(array)fetchAttendances($sid);
		$comment=commentDisplay($sid,$commentdate);
?>
	  <tr>
		<td><?php print $c++;?></td>
		<td>
			&nbsp
			<a onclick="parent.viewBook('infobook');" target="viewinfobook" 
			  href='infobook.php?current=student_scores.php&sid=<?php print $sid;?>'>T</a> 
			<a onclick="parent.viewBook('infobook');" target="viewinfobook"  
			  href='infobook.php?current=comments_list.php&sid=<?php print $sid;?>'
			  <?php print ' class="'.$comment['class'].'" title="'.$comment['body'].'"';?>>C</a> 
			<a onclick="parent.viewBook('infobook');" target="viewinfobook"  
			  href='infobook.php?current=incidents_list.php&sid=<?php print $sid;?>'>I</a>
		</td>
		<td>
		  <?php print $Student['DisplayFullName']['value']; ?>
		</td>
		<td>
		  <?php print $Student['DOB']['value'];?>
		</td>
<?php
		$col=0;
		while(list($index,$Attendance)=each($Attendances)){
			if($events[$col]==$Attendance['id_db']){;
?>
		<td>
		  <?php print $Attendance['Code']['value'];?>
		</td>
<?php
				  $col++;
				  }
			}
?>
	  </tr>
<?php
		}
?>
	  </table>
	</div>
  </div>