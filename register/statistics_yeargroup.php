<?php
/**									statistics_yeargroup.php
 *
 */

$action='statistics_yeargroup_action.php';
$choice='statistics.php';

$toyear=get_curriculumyear() - 1;

if(isset($_GET['yid'])){$yid=$_GET['yid'];}else{$yid='';}
if(isset($_GET['startdate'])){$startdate=$_GET['startdate'];}else{$startdate='';}
if(isset($_GET['enddate'])){$enddate=$_GET['enddate'];}else{$enddate='';}

if($startdate==''){
	$startdate=$toyear.'-08-01';
	$enddate=date('Y-m-d');
	}

include ('scripts/sub_action.php');

$Attendance=fetchYeargroupAttendanceTotals($yid,$startdate,$enddate);
$average=$Attendance['AttendanceAveragePercent']['value'];

$yname=get_yeargroupname($yid);

two_buttonmenu($extrabuttons);
?>
<div id="viewcontent" class="content">
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>">

	<label><?php print_string('yeargroup', $book); ?></label>
	<?php print ': '.$yname; ?>
	<table class="listmenu smalltable">
		<tr>
		  <th><?php print_string('average', $book); ?></th>
		  <th><?php print $Attendance['AttendanceAveragePercent']['value']."%"; ?></th>
		  <th><?php print_string('present', $book); ?></th>
		  <th><?php print $Attendance['Summary']['Attended']['value']; ?></th>
		  <th><?php print_string('late', $book); ?></th>
		  <th><?php print $Attendance['Summary']['Late']['value']; ?></th>
		  <th><?php print_string('absent', $book); ?></th>
		  <th><?php print $Attendance['Summary']['Absent']['value']; ?></th>
		  <th><?php print_string('numberofdays', $book); ?></th>
		  <th><?php print $Attendance['TotalDays']; ?></th>
		</tr>
	</table>

	<br/>
	<br/>

	<table class="listmenu sidtable">
		<tr>
			<th style="width:60%;"><?php print_string('student', $book); ?></th>
			<th style="width:40%;"><?php print_string('average', $book); ?></th>
		</tr>
<?php
	$com=array('id'=>'','type'=>'year','name'=>$yid);
	$students=(array)listin_community($com);
	foreach($students as $student){
		$sid=$student['id'];
		$Student=fetchStudent_short($sid);
		$Attendance=(array)fetchAttendanceSummary($sid,$startdate,$enddate);
		$noattended=$Attendance['Summary']['Attended']['value'];
		$nolate=$Attendance['Summary']['Lateunauthorised']['value'] + $Attendance['Summary']['Latetoregister']['value'];
		$noabsent_authorised=$Attendance['Summary']['Absentauthorised']['value'];
		$noabsent_unauthorised=$Attendance['Summary']['Absentunauthorised']['value'];
		$noabsent=$noabsent_authorised+$noabsent_unauthorised;
		$nosession=$noattended+$noabsent;
		$average=round(($noattended / $nosession)*100);
?>

		<tr>
			<td style="width:60%;"><a onclick="parent.viewBook('infobook');" target="viewinfobook" href="infobook.php?current=student_view.php&sid=<?php print $sid; ?>"><?php print $Student['DisplayFullName']['value']." (".$Student['RegistrationGroup']['value'].")"; ?></a></td>
			<td style="width:40%;"><?php print $average."%"; ?></td>
		</tr>
<?php
		}
?>
	</table>

	<input type="hidden" name="current" value="<?php print $action; ?>" />
	<input type="hidden" name="cancel" value="<?php print $choice; ?>" />
	<input type="hidden" name="choice" value="<?php print $choice; ?>" />
  </form>
</div>
