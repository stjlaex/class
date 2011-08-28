<?php
/**									register_list.php
 *
 *   	Lists students in array sids.
 */

$action='register_list_action.php';
$choice='register_list.php';

$notice='';

include('scripts/sub_action.php');

if(isset($CFG->registration[$secid]) and $CFG->registration[$secid]!='single'){$session='%';}
else{$session='AM';}


	if($community['type']=='class'){
		$students=(array)listin_class($community['name'],true);
		$AttendanceEvents=fetchAttendanceEvents($startday,1,$session);

		/* If the currentevent is not yet in the db event table then must
		 * add a blank to get started.
		 */
		$Event=fetchAttendanceEvent();
		if($currentevent['id']==0 and $startday==''){
			$Event['id_db']=0;
			$Event['Date']['value']=$currentevent['date'];
			$Event['Session']['value']=$currentevent['session'];
			$Event['Period']['value']=$currentevent['period'];
			$AttendanceEvents['Event'][]=$Event;
			}

		$perindex=$AttendanceEvents['perindex'];

		$classperiods=get_class_periods($currentevent,$secid);
		foreach($classperiods as $classperiod => $periodtime){
			if(!array_key_exists($classperiod,$perindex)){
				/* This must be negative to indicate a class period!!! 
				 * Its 0 for a fresh session and a positive value would be 
				 * an existing event id.
				 */
				$Event['id_db']=-$classperiod;
				$Event['Date']['value']=$currentevent['date'];
				$Event['Session']['value']=$session;
				$Event['Period']['value']=$classperiod;
				$AttendanceEvents['Event'][]=$Event;
				}
			}
		}
	else{
		$community['yeargroup_id']=$yid;
		$students=(array)listin_community($community);
		$tutor_users=(array)list_community_users($community);
		$AttendanceEvents=fetchAttendanceEvents($startday,$nodays,$session);

		/* If the currentevent is not yet in the db event table then must
		 * add a blank to get started.
		 */
		if($currentevent['id']==0 and $startday==''){
			$Event=fetchAttendanceEvent();
			$Event['id_db']=0;
			$Event['Date']['value']=$currentevent['date'];
			$Event['Session']['value']=$currentevent['session'];
			$Event['Period']['value']=$currentevent['period'];
			$AttendanceEvents['Event'][]=$Event;
			}

		$sess=$currentevent['session'];
		$dat=$currentevent['date'];
		$comid=$community['id'];
		$d_n=mysql_query("SELECT comment FROM event_notice 
							JOIN event_notidcomid ON event_notidcomid.notice_id=event_notice.id 
							WHERE event_notidcomid.community_id='$comid' 
							AND event_notice.session='$sess' AND event_notice.date='$dat';");
		while($n=mysql_fetch_array($d_n)){
			$notice.=$n['comment'].'<br />';
			}
		}


	/* 
	 *  Make sure an event is selected which is part of the current window
	 */
	$eveindex=$AttendanceEvents['eveindex'];
	if(!array_key_exists($checkeveid,$eveindex)){
		if($startday>-7){
			$checkeveid=0;
			}
		else{
			end($eveindex);
			$checkeveid=key($eveindex);
			reset($eveindex);
			}
		}


	/* If no event column has yet been checked by the user then select the column for 
	 * the current event.
	 */
	if($checkeveid=='' or $checkeveid=='0'){

		$seleveid=$currentevent['id'];

		}
	else{$seleveid=$checkeveid;}


	$extrabuttons['summary']=array('name'=>'current',
								   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/reportbook/',
								   'title'=>'printreportsummary',
								   'value'=>'report_attendance_print.php',
								   'onclick'=>'checksidsAction(this)'
								   );
	threeplus_buttonmenu($startday,2,$extrabuttons);


if($community['type']=='form'){
?>
  <div id="heading">
	<div>
	  <label><?php print_string('formgroup');?></label>
	  <?php print $community['name'];?>
	</div>
<?php
	if(isset($tutor_users)){
		foreach($tutor_users as $uid => $tutor_user){
?>
	<div>
	  <label><?php print_string('formtutor');?>
	  </label>
	  <?php print $tutor_user['forename'][0].' '. $tutor_user['surname'];?>
		<?php emaillink_display($tutor_user['email']);?>
	  &nbsp;
	</div>
<?php
			}
		}
?>
	<div>
	</div>
  </div>
<?php
	}
?>


  <div id="viewcontent" class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu sidtable" id="sidtable">
		<tr>
		  <th colspan="3">&nbsp;</th>
<?php
	/* This events array will determine which events are displayed */
	$events=array();
	$tallys=array();
	foreach($AttendanceEvents['Event'] as $index=>$Event){
		/* Chekcing that only periods relevant to the current section are included. */
		if($Event['Period']['value']=='0' or array_key_exists($Event['Period']['value'],$classperiods)){
			$events[]=$Event['id_db'];
			$tallys[$Event['id_db']]=0;
			$eventsessions[]=$Event['Session']['value'];
?>
		  <th id="event-<?php print $Event['id_db'];?>" 
			class="<?php if($seleveid==$Event['id_db']){ print 'selected';}?>"  >
<?php 
			if($Event['Period']['value']=='0'){
				$t=strtotime($Event['Date']['value']);
				print date('D',$t) .'<br />';
				print date('j S',$t) .'<br />';
				print date('M',$t) .'<br />';
				print $Event['Session']['value'];
				if($Event['id_db']>0 and $index!=(sizeof($AttendanceEvents['Event'])-1)){
					$lasteveid=$Event['id_db'];
					}
				}
			else{
				print 'Period <br />';
				print $Event['Period']['value'].'<br />';	
				}

			if($_SESSION['worklevel']>-1 or $seleveid==$Event['id_db']){

?>
			<input type="radio" name="checkeveid" value="<?php print $Event['id_db'];?>" />
<?php
				}
?>
			</th>
<?php
			}
		}
?>
		  <th class="edit">
<?php
	if($nodays==1 or $_SESSION['role']=='office' or $_SESSION['role']=='admin'){
?>
<?php print_string('checkall',$book);?>

				<select name="setall" >
				  <option value="n"></option>
				  <option value="l" onclick="setAll('<?php print $lasteveid;?>')"><?php print_string('last',$book);?></option>
				  <option value="p" onclick="setAll('p')"><?php print_string('present',$book);?></option>
				  <option value="a" onclick="setAll('a')"><?php print_string('absent',$book);?></option>
				</select>
<?php
		}
?>
		  </th>
		</tr>
<?php
	$rown=1;
	while(list($sindex,$student)=each($students)){
		$sid=$student['id'];
		$Student=fetchStudent_short($sid);
		$Attendances=(array)fetchAttendances($sid,$startday,$nodays);
		$comment=comment_display($sid);
?>
		<tr id="sid-<?php print $sid;?>">
		  <td>
			<input type="checkbox" name="sids[]" value="<?php print $sid; ?>" />
			<?php print $rown++;?>
		  </td>
		  <td>
<?php
		if($_SESSION['role']!='support'){
			/* Consider support staff to be not priviliged to access. */
				$comment=comment_display($sid);
?>
			<span title="<?php print $comment['body'];?>">
			  <a onclick="parent.viewBook('infobook');" target="viewinfobook"  
				href='infobook.php?current=comments_list.php&sid=<?php print $sid;?>'
				<?php print ' class="'.$comment['class'].'" ';?>>C</a> 
			</span>
			<a onclick="parent.viewBook('infobook');" target="viewinfobook"  
			  href='infobook.php?current=incidents_list.php&sid=<?php print $sid;?>'>I</a>
<?php		if($Student['SENFlag']['value']=='Y'){ ?>
			<a href="infobook.php?current=student_view_sen.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">S</a>
<?php			} ?>
<?php		if($Student['MedicalFlag']['value']=='Y'){ ?>
			<a href="infobook.php?current=student_view_medical.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">M</a>
<?php			} ?>

<?php			
			}
		else{
			print '&nbsp';
			}
?>
<div style="font-size:7pt;color:#909090;float:right;width:30px;">
<?php
	   		$t=display_student_transport($sid);
			if($t!=' '){print '<span title="'.$t.'"><img class="clicktotransport" /></span>';}
?>
</div>
		  </td>
		  <td class="student">
			<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">
			  <?php print $Student['DisplayFullName']['value']; ?></a>
			<div id="merit-<?php print $sid;?>"></div>
		  </td>
<?php
		$attodds=array('AM'=>'forstroke','PM'=>'backstroke');
		foreach($events as $index=>$eveid){
?>
			<td id="cell-<?php print $eveid.'-'.$sid;?>"  
<?php
			$cell='';
			$des='';
			unset($Attendance);
			if(array_key_exists($eveid,$Attendances['eveindex'])){
				$Attendance=$Attendances['Attendance'][$Attendances['eveindex'][$eveid]];
				}
			else{
				$BookedAttendance=fetchbookedAttendance($sid);
				if($BookedAttendance['id_db']!=-1){$Attendance=$BookedAttendance;}
				}
			if(isset($Attendance)){
				$attvalue=$Attendance['Status']['value'];
				$attcode=$Attendance['Code']['value'];
				$attlate=$Attendance['Late']['value'];
				$attcomm=$Attendance['Comment']['value'];
				if($Attendance['Logtime']['value']!=''){$atttime=date('H:i',$Attendance['Logtime']['value']);}
				else{$atttime='';}
				if($attvalue=='a' and ($attcode==' ' or $attcode=='O')){
					$cell='title="" ><span title="? : <br />'. 
							$atttime.' '.$attcomm.'" >';
					$cell.='<img src="images/ostroke.png" /></span>';
					}
				elseif($attvalue=='a' and $attcode!=' ' and $attcode!='O'){
					$des=displayEnum($attcode,'absencecode');
					$des=get_string($des,'register');
					$cell='title="" ><span title="'.$attcode .': '. $des
							.'<br />'.$atttime.' '.$attcomm.'" >';
					$cell.=' &nbsp '.$attcode.'</span>';
					if($attcode=='U' or $attcode=='L' or $attcode=='UB' or $attcode=='UA'){$tallys[$eveid]++;}
					}
				else{
					$tallys[$eveid]++;
					$cell='><img src="images/'.$attodds[$eventsessions[$index]].'.png" />';
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
				  <option value="p"><?php print_string('present',$book);?></option>
				  <option value="a"><?php print_string('absent',$book);?></option>
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
		<tr>
	<th colspan="3"><?php print get_string('inschool',$book);?></th>
<?php
		foreach($events as $index=>$eveid){
?>
		  <th>&nbsp;<?php print $tallys[$eveid];?></th>
<?php
			}
?>
		  <th class="edit"></th>
		</tr>
		</table>

		<input type="hidden" name="date" value="<?php print $currentevent['date'];?>" />
		<input type="hidden" name="session" value="<?php print $currentevent['session'];?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	    <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  </form>

  </div>

  <div class="hidden" id="add-extra-ppp">
	<button type="button" name="late" id="late-butt" value="0" 
	  onclick="parent.seleryGrow(this)"  class="rowaction selery">
	  <img src="images/null.png" />
	</button>
	<input type="hidden" id="late" name="late" value="0" />
  </div>

  <div class="hidden" id="add-extra-p">
	<select style="width:10em;" name="late" id="late">
<?php
	$enum=getEnumArray('latecode');
	while(list($inval,$description)=each($enum)){	
		print '<option ';
		print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
		}
?>
	</select>
  </div>


  <div class="hidden" id="add-extra-a">
	<select style="width:10em;" name="code" id="code">
<?php
	$enum=getEnumArray('absencecode');
	while(list($inval,$description)=each($enum)){	
		print '<option ';
		print ' value="'.$inval.'">'.$inval.': '.get_string($description,$book).'</option>';
		}
?>
	</select>
	<input style="width:10em;" name="comm" id="comm" value="" />
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

<?php
include('scripts/studentlist_extra.php');
?> 
