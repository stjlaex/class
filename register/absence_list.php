<?php
/**									absence_list.php
 *
 *   	Lists students in array sids.
 */

$action='absence_list_action.php';
$choice='absence_list.php';

$students=list_absentStudents();
//trigger_error('Subject'.$bid,E_USER_WARNING);

include('scripts/sub_action.php');

$extrabuttons['message']=array('name'=>'current',
							   'title'=>'message',
							   'value'=>'message_absences.php',
							   'onclick'=>'processContent(this)'
							   );
$extrabuttons['summary']=array('name'=>'current',
							   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/reportbook/',
							   'title'=>'printreportsummary',
							   'value'=>'report_attendance_print.php',
							   'onclick'=>'checksidsAction(this)'
							   );
//threeplus_buttonmenu($startday,2,$extrabuttons);
two_buttonmenu($extrabuttons);
?>
  <div id="heading">
	<label><?php print_string('absencesthissession','register');?></label>
  </div>
  <div id="viewcontent" class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu sidtable">
		<tr>
		  <th colspan="2">
			<label id="checkall">
			  <?php print_string('checkall');?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
			</label>
		  </th>
		  <th colspan="2"><?php print_string('student'); ?></th>
		  <th><?php print_string('attendance',$book);?></th>
		</tr>
<?php
	$rown=1;
	while(list($index,$student)=each($students['Student'])){
		$sid=$student['id_db'];
		$Attendance=(array)$student['Attendance'];
		$Student=fetchStudent_short($sid);
?>
		<tr id="sid-<?php print $sid;?>">
		<td>
		<?php print $rown++;?>
		</td>
		<td>
		<input type="checkbox" name="sids[]" value="<?php print $sid; ?>" />
		</td>
		<td>
			<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">
			<?php print $Student['DisplayFullName']['value']; ?></a>
		  </td>
		  <td>
			<?php print $Student['RegistrationGroup']['value'];?>
		  </td>
			  <td title=""
<?php
			$cell='';
			$des='';
			$attvalue=$Attendance['Status']['value'];
			$attcode=$Attendance['Code']['value'];
			$attlate=$Attendance['Late']['value'];
			$attcomm=$Attendance['Comment']['value'];
			$des=displayEnum($attcode,'absencecode');
			$des=get_string($des,'register');
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
?>
				status="<?php print $attvalue;?>"
				code="<?php print $attcode;?>"
				late="<?php print $attlate;?>"
				comm="<?php print $attcomm;?>"
			<?php print $cell;?>
		  </td>
		</tr>
<?php
			}
?>
		</table>

		<input type="hidden" name="date" value="<?php print $currentevent['date'];?>" />
		<input type="hidden" name="session" value="<?php print $currentevent['session'];?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	    <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  </form>
  </div>

<?php
	$toyear=get_curriculumyear()-1;//TODO: set a proper start of term date
	$today=date('Y-m-d');
?>
  <div id="xml-checked-action" style="display:none;">
	<session>
	  <startdate><?php print $toyear.'-08-01';?></startdate>
	  <enddate><?php print $today;?></enddate>
	</session>
  </div>
