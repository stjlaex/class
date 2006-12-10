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
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu sidtable" id="sidtable">
		<tr>
		  <th colspan="2">&nbsp</th>
		  <th><?php print_string('student'); ?></th>
<?php
	$selevent=$currentevent;
	$events=array();
	while(list($index,$Event)=each($AttendanceEvents['Event'])){
		$events[]=$Event['id_db'];
?>
		  <th id="event-<?php print $Event['id_db'];?>" 
			class="<?php if($selevent['id']==$Event['id_db']){ print 'selected';}?>"  >
<?php 
		  $t=strtotime($Event['Date']['value']);
		  print date('D',$t) .'<br />';
		  print date('j S',$t) .'<br />';
		  print date('M',$t) .'<br />';
		  print $Event['Period']['value'];
?>
		  <input type="radio" name="checkeveid" value="<?php print $Event['id_db'];?>" />
		  </th>
<?php
		}
	if($selevent['id']==0){
?>
		  <th id="event-0" class="selected">
<?php 
		  $t=strtotime($selevent['date']);
		  print date('D',$t) .'<br />';
		  print date('j S',$t) .'<br />';
		  print date('M',$t) .'<br />';
		  print $selevent['period'];
?>
			<input type="radio" name="checkeveid" value="0" />
		  </th>
<?php
	 	}
?>
		  <th class="edit"><?php print_string('attendance',$book);?></th>
		</tr>
<?php
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
			  <a onclick="parent.viewBook('infobook');" target="viewinfobook" 
				href='infobook.php?current=student_scores.php&sid=<?php print $sid;?>'>T</a> 
			<span <?php print ' title="'.$comment['body'].'"';?>>
			  <a onclick="parent.viewBook('infobook');" target="viewinfobook"  
				href='infobook.php?current=comments_list.php&sid=<?php print $sid;?>'
				<?php print ' class="'.$comment['class'].'" ';?>>C</a> 
			</span>
			  <a onclick="parent.viewBook('infobook');" target="viewinfobook"  
				href='infobook.php?current=incidents_list.php&sid=<?php print $sid;?>'>I</a>
			</td>
			<td>
			<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">
			<?php print $Student['DisplayFullName']['value']; ?></a>
			</td>
<?php
		reset($events);
		$attodds=array('forstroke','backstroke');
		//$attodds=array('/','\\');
		while(list($index,$eveid)=each($events)){
			if($index%2){$odds=1;}else{$odds=0;}
?>
			<td id="cell-<?php print $eveid.'-'.$sid;?>"  
<?php
			$cell='';
			$des='';
			if(array_key_exists($eveid,$Attendances['evetable'])){
				$Attendance=$Attendances['Attendance'][$Attendances['evetable'][$eveid]];
				$attvalue=$Attendance['Status']['value'];
				$attcode=$Attendance['Code']['value'];
				$attlate=$Attendance['Late']['value'];
				$attcomm=$Attendance['Comment']['value'];
				if($attvalue=='a' and ($attcode==' ' or $attcode=='O')){
					$cell='title="" ><span title="? : <br />'. $attcomm.'" >';
					$cell.='<img src="images/ostroke.png" /></span>';
					}
				else if($attvalue=='a' and $attcode!=' ' and $attcode!='O'){
					$des=displayEnum($attcode,'absencecode');
					$des=get_string($des,'register');
					$cell='title="" ><span title="'.$attcode .': '. $des
							.'<br />'.$attcomm.'" >';
					$cell.=' &nbsp '.$attcode.'</span>';
					}
				else{
					$cell='><img src="images/'.$attodds[$odds].'.png" />';
					}
				}
			else{
				$attvalue='n';
				$attcode='';
				$attlate='';
				$attcomm='';
				}
?>
				status="<?php print $attvalue;?>"
				code="<?php print $attcode;?>"
				late="<?php print $attlate;?>"
				comm="<?php print $attcomm;?>"
				  <?php print $cell;?>
			</td>
<?php
			}

		if($selevent['id']==0){
?>
			  <td id="<?php print 'cell-0-'.$sid;?>" status="n">
				<?php print '&nbsp';?>
			  </td>
<?php
			 }
?>
			  <td id="edit-<?php print $sid;?>" class="edit">
				<select tabindex="<?php print $tab++;?>" 					
					name="status-<?php print $sid;?>" >
				  <option value="n"></option>
				  <option value="p">Present</option>
				  <option value="a">Absent</option>
				</select>
			  </td>
			</tr>
<?php
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

  <div class="hidden" id="extra-p">
	<button type="button" name="late" id="late-butt" value="0" 
	  onclick="parent.seleryGrow(this)"  class="rowaction selery">
	  <img src="images/null.png" />
	</button>
	<input type="hidden" id="late" name="late" value="0" />
  </div>


  <div class="hidden" id="extra-a">
	<select name="code" id="code" style="width:10em;">
<?php
	$enum=getEnumArray('absencecode');
	while(list($inval,$description)=each($enum)){	
		print '<option ';
		print ' value="'.$inval.'">'.$inval.': '.get_string($description,$book).'</option>';
		}
?>
	</select>
	<input type="hidden" name="comm" id="comm" value="" title="" />
  </div>
