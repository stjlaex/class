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
		  <thead>
		  <th colspan="2">&nbsp</th>
		  <th><?php print_string('student'); ?></th>
<?php
	$selevent=$currentevent;
	$events=array();
	while(list($index,$Event)=each($AttendanceEvents['Event'])){
		$cssclass='';
		$checked='';
		$events[]=$Event['id_db'];
		if($selevent['id']==$Event['id_db']){
			$cssclass='selected';
			$checked=' checked="checked" ';
			}
?>
		  <th id="event-<?php print $Event['id_db'];?>"
			   class="<?php print $cssclass;?>" 
			   onClick="selectColumn(this,1);" >
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
		  <th id="event-0" 
			  class="selected" onClick="selectColumn(this,1);">
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
			<th class="blank">&nbsp</th>
			<th>&nbsp</th>
		  </thead>
		  <tbody>
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
		$attodds=array('p'=>array('forstroke','backstroke'),'a'=>array('ostroke','ostroke'),'n'=>array('null','null'));
		while(list($index,$eveid)=each($events)){
			if($index%2){$odds=0;}else{$odds=1;}
?>

			<td id="cell-<?php print $eveid.'-'.$sid;?>" 
				<?php if($selevent['id']==$eveid){print 'class="selected" ';}?> 
<?php
			if(array_key_exists($eveid,$Attendances['evetable'])){
				$Attendance=$Attendances['Attendance'][$Attendances['evetable'][$eveid]];
				$attvalue=$Attendance['Status']['value'];
				}
			else{$attvalue='n';}
?>
				status="<?php print $attvalue;?>"
				>
				<img src="images/<?php print $attodds[$attvalue][$odds];?>.png" />
			</td>
<?php
			}

		if($selevent['id']==0){
?>
			<td id="<?php print 'cell-0-'.$sid;?>" 
				status="n" class="selected">
			  <?php print '&nbsp';?>
			</td>
<?php
			}
?>
			  <td class="blank">&nbsp</td>
			  <td id="edit-<?php print $sid;?>"
<?php				if($rown<3){print ' class="edit selected" ';}
					else{print ' class="edit" ';} 
?>
				>
				<select tabindex="<?php print $tab++;?>" 					
					name="status-<?php print $sid;?>"
				    onFocus="checkAttendance(this);" 
				    onBlur="processAttendance(this);" 
				  >
				  <option value="n"></option>
				  <option value="p">Present</option>
				  <option value="a">Absent</option>
				</select>
			  </td>
			</tr>
<?php
		}
?>
		  </tbody>
		</table>

		<input type="hidden" name="date" value="<?php print $currentevent['date'];?>" />
		<input type="hidden" name="period" value="<?php print $currentevent['period'];?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	    <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  </form>
	</div>
  </div>
<?php
if($selevent['id']!=0){
?>
        <script>updateEditColumn(<?php print $selevent['id'];?>);</script>
<?php
		}
?>