<?php
	$months=array('August'=>'08','September'=>'09','October'=>'10','November'=>'11','December'=>'12','January'=>'01','February'=>'02','March'=>'03','April'=>'04','May'=>'05','June'=>'06','July'=>'07');
	$yeargroups=list_yeargroups();
?>
<table class="listmenu smalltable">
		<tr>
		  <th><?php print_string('yeargroup', $book); ?></th>
<?php
	foreach($months as $month=>$monthno){
?>
		  <th><?php print $month; ?></th>
<?php
		}
?>
		</tr>
<?php
	$rown=1;
	foreach($yeargroups as $yeargroup){
		$yid=$yeargroup['id'];
		$yname=get_yeargroupname($yid);
?>
		<tr>
			<th>
				<a href="register.php?current=statistics_yeargroup.php&yid=<?php print $yid; ?>">
					<?php print $yname;?>
				</a>
			</th>
<?php
		$toyear=get_curriculumyear()-1;
		foreach($months as $month=>$monthno){
			if($monthno=='01'){$toyear++;}
			$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $monthno, $toyear);
			$startdate=$toyear.'-'.$monthno.'-01';
			$enddate=$toyear.'-'.$monthno.'-'.$daysinmonth;
			$Attendance=(array)fetchYeargroupAttendanceSummary($yid,$startdate,$enddate);
			$attended=$Attendance['Summary']['Attended']['value'];
			$absent=$Attendance['Summary']['Absent']['value'];
			$average=round(($attended / ($attended + $absent))*100);
?>
			<th>
				<a href="register.php?current=statistics_yeargroup.php&yid=<?php print $yid; ?>&startdate=<?php print $startdate;?>&enddate=<?php print $enddate; ?>">
					<?php print $average."%";?>
				</a>
			</th>
<?php
			}
?>
		</tr>
<?php
		}
?>
</table>

