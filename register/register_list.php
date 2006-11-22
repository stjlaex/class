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
three_buttonmenu();
?>

  <div id="viewcontent" class="content">
	<div class="center">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu">
		  <th colspan="2">&nbsp</th>
		  <th><?php print_string('student'); ?></th>
<?php
	$selevent=$currentevent;
	$newevent='yes';
	$events=array();
	while(list($index,$Event)=each($AttendanceEvents['Event'])){
		$cssclass='';
		$checked='';
		$events[]=$Event['id_db'];
		if($selevent['id']==$Event['id_db']){
			$newevent='no';
			$cssclass='selected';
			$checked=' checked="checked" ';
			}
?>
		  <th class="<?php print $cssclass;?>"  onClick="selectColumn(this,1);">
<?php 
		  $t=strtotime($Event['Date']['value']);
		  print date('D',$t) .'<br />';
		  print date('j S',$t) .'<br />';
		  print date('M',$t) .'<br />';
		  print $Event['Period']['value'];
?>
		  <input type="radio" <?php print $checked;?>
				name="checkeveid" value="<?php print $Event['id_db'];?>" />
		  </th>
<?php
		}
	if($newevent=='yes'){
?>
		  <th class="selected"  onClick="selectColumn(this,1);">
<?php 
		  $t=strtotime($selevent['date']);
		  print date('D',$t) .'<br />';
		  print date('j S',$t) .'<br />';
		  print date('M',$t) .'<br />';
		  print $selevent['period'];
?>
			<input type="radio" name="checkeveid" value="0" checked="checked" />
		  </th>
<?php
	 	}
?>
		  <th colspan="2" class="blank"></th>
<?php
//trigger_error('',E_USER_WARNING);

	$rown=1;
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
		$Student=fetchStudent_short($sid);
		$Attendances=(array)fetchAttendances($sid);
		$comment=commentDisplay($sid);
?>
		  <tr id="sid-<?php print $sid;?>">
			<td><?php print $rown++;?></td>
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
			<td <?php if($rown<3){print ' id="selected-row" class="selected"';}?>>
			  <?php print $Student['DisplayFullName']['value']; ?>
			</td>
<?php
		reset($events);
		while(list($index,$eveid)=each($events)){
?>
			<td id="cell-<?php print $eveid.'-'.$sid;?>" 
				<?php if($selevent['id']==$eveid){print 'class="selected" ';}?> >
<?php
			if(array_key_exists($eveid,$Attendances['evetable'])){
				$Attendance=$Attendances['Attendance'][$Attendances['evetable'][$eveid]];
				print $Attendance['Status']['value'];
				}
			else{print '';}
?>
			</td>
<?php
			}

		if($newevent=='yes'){
			$eveid='0';
?>
			<td id="<?php print 'cell-'.$eveid.'-'.$sid;?>" class="selected">
			  <?php print '&nbsp';?>
			</td>
<?php
			}
?>
			<td class="blank">&nbsp</td>
			<td class="edit">

				  <select
					<?php print ' tabindex="'.$tab++.'" ';?>
					<?php if($tab>2){ print 'onFocus="checkAttendance(this);"'; }?>
					onBlur="processAttendance(this);" 
					id="<?php print 'edit-'.$eveid.'-'.$sid;?>" 
					name="<?php print 'edit-'.$sid;?>" >
					<option value=""></option>
					<option value="p">Present</option>
					<option value="a">Absent</option>
				  </select>
			  
			</td>
		  </tr>
<?php
					 /*
					  */
		}
?>
		</table>

		<input type="hidden" name="date" value="<?php print $currentevent['date'];?>" />
		<input type="hidden" name="period" value="<?php print $currentevent['period'];?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	    <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  </form>
	</div>
  </div>