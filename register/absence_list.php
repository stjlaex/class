<?php
/**									absence_list.php
 *
 *   	Lists students in array sids.
 */

$action='absence_list_action.php';
$choice='absence_list.php';
//if(isset($_POST['newsecid'])){$secid=$_POST['newsecid'];}


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


if($secid!='' and $secid>1){
	/* Limit list to just the year groups for this section. */
	$ygs=(array)list_yeargroups($secid);
	$sectionname=get_sectionname($secid);
	}
else{
	/* Give the whole school when no section is selected. */
	$ygs=(array)list_yeargroups();
	$sectionname=get_sectionname(1);
	}

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
		  <th colspan="2" style="text-align:center;"><?php print $sectionname;?></th>
		  <th><?php print_string('attendance',$book);?></th>
		</tr>
<?php

$rown=1;
foreach($ygs as $yg){
	if($currentevent['id']>0){
		$students=(array)list_absentStudents($currentevent['id'],$yg['id']);
		}
	else{
		$students=(array)list_absentStudents('',$yg['id']);
		}
	if(sizeof($students['Student'])>0){
		print '<tr><th colspan="2"></th><th colspan="2" style="text-align:center;">'.$yg['name'].'</th><th></th></tr>';
		}

	foreach($students['Student'] as $student){
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
			elseif($attvalue=='a' and $attcode!=' ' and $attcode!='O'){
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
