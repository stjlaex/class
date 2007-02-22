<?php
/**									register_list.php
 *
 *   	Lists students in array sids.
 */

$action='register_list_action.php';
$choice='register_list.php';

include('scripts/sub_action.php');


	$students=(array)listin_community($community);
	$AttendanceEvents=fetchAttendanceEvents($startday);
	$evetable=$AttendanceEvents['evetable'];
	/*make sure an event is selected which is part of the current window*/
	if(!array_key_exists($checkeveid,$evetable)){
		if($startday>-7){
				$checkeveid=0;
				}
		else{
				end($evetable);
				$checkeveid=key($evetable);
				reset($evetable);
				}
		}
	if($checkeveid=='' or $checkeveid=='0'){
		$seleveid=$currentevent['id'];
		if($currentevent['id']==0 and $startday==''){
			$Event=fetchAttendanceEvent();
			$Event['id_db']=0;
			$Event['Date']['value']=$currentevent['date'];
			$Event['Period']['value']=$currentevent['period'];
			$AttendanceEvents['Event'][]=$Event;
			}
		}
	else{$seleveid=$checkeveid;}


	threeplus_buttonmenu($startday,2);
?>
  <div id="heading">
	<label><?php print_string('formgroup');?></label>
	<?php print $newfid;?>
  </div>
  <div id="viewcontent" class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu sidtable" id="sidtable">
		<tr>
		  <th colspan="2">&nbsp</th>
		  <th><?php print_string('student'); ?></th>
<?php
	$events=array();
	while(list($index,$Event)=each($AttendanceEvents['Event'])){
		$events[]=$Event['id_db'];
?>
		  <th id="event-<?php print $Event['id_db'];?>" 
			class="<?php if($seleveid==$Event['id_db']){ print 'selected';}?>"  >
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

?>
		  <th class="edit"><?php print_string('attendance',$book);?></th>
		</tr>
<?php
	$rown=1;
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
		$Student=fetchStudent_short($sid);
		$Attendances=(array)fetchAttendances($sid,$startday);
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

   		if(sizeof($AttendanceEvents['Event'])>0){
?>
			  <td id="edit-<?php print $sid;?>" class="edit">
				<select tabindex="<?php print $tab++;?>" 					
					name="status-<?php print $sid;?>" >
				  <option value="n"></option>
				  <option value="p">Present</option>
				  <option value="a">Absent</option>
				</select>
			  </td>
<?php
			}
		else{
?>
		  <td></td>
<?php
   		   }
		}
?>
		</tr>
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
