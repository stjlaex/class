<?php 
/**									edit_absence.php
 *
 */

$action='edit_absence_action.php';

if(isset($_GET['eveid'])){$eveid=$_GET['eveid'];}else{$eveid='';}
if(isset($_GET['sid'])){$sid=$_GET['sid'];}else{$sid='';}
if(isset($_GET['colid'])){$columnid=$_GET['colid'];}else{$columnid='';}
if(isset($_GET['date'])){$date=$_GET['date'];}else{$date='';}
if(isset($_GET['session'])){$session=$_GET['session'];}else{$session='';}
if(isset($_GET['period'])){$period=$_GET['period'];}else{$period='';}

if($eveid!='' and $eveid>0){
	$Event=fetchAttendanceEvent($eveid);
	$date=$Event['Date']['value'];
	}
$Student=fetchStudent_short($sid);
$displayname=$Student['DisplayFullName']['value'];
if($period>0){$displayperiod=' Period '.$period;}
elseif($period=='lunch'){
	$displayperiod=get_string('lunch','admin');
	$bookings=get_student_booking($sid,$date,date('w', strtotime($date)));
	if(count($bookings)>0 and count($bookings[0])>0){$mealname=$bookings[0]['name'];}
	}
else{$displayperiod='';}

$extrabuttons='';
submit_update($action,$extrabuttons,$book);
?>
	<div id="heading">
		<label>
<?php
		print $displayname.' - '.display_date($date)." ".$displayperiod; 
		if($period!='lunch'){print ' Session: '.$session;}
		else{print ' '.$mealname;}
?>
		</label>
	</div>
	<div  id="viewcontent" class="content">
		<label><?php print_string('attendance');?></label>
		<form id="formtoprocess" name="formtoprocess" method="post"> 

			<div id="edit-<?php echo $sid;?>">
				<select tabindex="<?php print $tab++;?>" name="status" onchange="processAttendance(this);">
					  <option value="n"></option>
					  <option value="p"><?php print_string('present',$book);?></option>
					  <option value="a"><?php print_string('absent',$book);?></option>
				</select>
			</div>

			<input type="hidden" id="colid" name="colid" value="<?php print $columnid;?>" />
			<input type="hidden" name="date" value="<?php print $date;?>" />
			<input type="hidden" name="session" value="<?php print $session;?>" />
			<input type="hidden" name="period" value="<?php print $period;?>" />

			<input type="hidden" name="sid" value="<?php print $sid;?>" />
			<input type="hidden" id="current" name="current" value="<?php print $action;?>" />
			<input type="hidden" name="choice" value="<?php print $choice;?>" />
			<input type="hidden" name="cancel" value="<?php print $cancel;?>" />

			<input type="hidden" id="editsingleattendance" value="<?php print $sid;?>" />
		</form>
	</div>

<?php
	if($period!='lunch'){
?>
	<div class="hidden" id="add-extra-ppp">
		<button type="button" name="late" id="late-butt" value="0" onclick="parent.seleryGrow(this,4)"  class="rowaction selerydot">
			<img src="images/null.png" />
		</button>
		<input type="hidden" id="late" name="late" value="0" />
	</div>

	<div class="hidden" id="add-extra-p">
		<select style="width:100px;" name="late" id="late">
<?php
	$enum=getEnumArray('latecode');
	foreach($enum as $inval =>$description){
		print '<option ';
		print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
		}
?>
		</select>
	</div>
<?php
		}
?>
	<div class="hidden" id="add-extra-a">
<?php
	if($period!='lunch'){
?>
		<select style="width:100px;" name="code" id="code">
<?php
	$enum=getEnumArray('absencecode');
	while(list($inval,$description)=each($enum)){
		print '<option ';
		print ' value="'.$inval.'">'.$inval.': '.get_string($description,$book).'</option>';
		}
?>
		</select>
<?php
		}
?>
		<input style="width:100px;" name="comm" id="comm" value="" type="text" />
	</div>
