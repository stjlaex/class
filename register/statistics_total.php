	<table class="listmenu smalltable">
		<tr>
		  <th><a name="Month"><?php print_string('month', $book); ?></a></th>
		  <th><?php print_string('present', $book); ?></th>
		  <th><?php print_string('late', $book); ?></th>
		  <th><?php print_string('absent', $book); ?></th>
		  <th><?php print_string('educationalvisitortrip', $book); ?></th>
		  <th><?php print_string('numberofdays', $book); ?></th>
		  <th><?php print get_string('dailyaverage', $book) . '<br />' . get_string('attendance', $book); ?></th>
		  <th><?php print get_string('dailyaverage', $book) . '<br />' . get_string('absent', $book); ?></th>
		</tr>
<?php
	$rown=1;
	$months=array('August'=>'08','September'=>'09','October'=>'10','November'=>'11','December'=>'12','January'=>'01','February'=>'02','March'=>'03','April'=>'04','May'=>'05','June'=>'06','July'=>'07');
	while(list($month,$monthno)=each($months)){
		if($monthno=='01'){$toyear++;}
		$sdate=$toyear.'-'.$monthno.'-'.'00';
		$edate=$toyear.'-'.$monthno.'-'.'31';
		$events=(array)list_events($sdate,$edate,'AM');
		$daysno=sizeof($events);
		$p=count_overall_attendance($sdate,$edate);
		$a=count_overall_attendance($sdate,$edate,'%');
		$v=count_overall_attendance($sdate,$edate,'V');
		$late=count_overall_late($sdate,$edate);
		?>
		<tr>
		<td>
			<a href="#<?php print $month; ?>"><?php print $month; ?></a>
		</td>
		<td>
			<?php print $p + $late; ?>
		</td>
		<td>
			<?php print $late; ?>
		</td>
		<td>
			<?php print $a - $late; ?>
		</td>
		<td>
			<?php print $v; ?>
		</td>
		<td>
			<?php print $daysno; ?>
		</td>
		<td>
		<?php print round(($p + $late) / $daysno); ?>
		</td>
		<td>
		<?php print round(($a - $late - $v) / $daysno); ?>
		</td>
		</tr>
<?php
}
?>
		</table>

<?php

	$toyear=get_curriculumyear()-1;//TODO: set a proper start of term date

	$twelve_months=array('August'=>'08','September'=>'09','October'=>'10','November'=>'11','December'=>'12','January'=>'01','February'=>'02','March'=>'03','April'=>'04','May'=>'05','June'=>'06','July'=>'07');

	foreach($twelve_months as $month_key => $month_val){
?>

		<br/>
		<br/>

		<table class="listmenu sidtable">
		<tr>
		<th style="width:40%;"><?php print_string('month', $book); ?></th>
		  <th style="width:20%;"><?php print_string('present', $book); ?></th>
		  <th style="width:20%;"><?php print_string('late', $book); ?></th>
		  <th style="width:20%;"><?php print_string('absent', $book); ?></th>
		</tr>
<?php
	$rown=1;
	$months=array($month_key=>$month_val);

	while(list($month,$monthno)=each($months)){
		if($monthno=='01'){$toyear++;}
		$sdate=$toyear.'-'.$monthno.'-'.'00';
		$edate=$toyear.'-'.$monthno.'-'.'31';
		$events=(array)list_events($sdate,$edate,'AM');
		$daysno=sizeof($events);
		$p=count_overall_attendance($sdate,$edate);
		$a=count_overall_attendance($sdate,$edate,'%');
		$late=count_overall_late($sdate,$edate);
?>
		<tr>
		<th>
			<a name="<?php print $month; ?>"><?php print $month; ?></a>
		</th>
		<th>
			<?php print $p + $late; ?>
		</th>
		<th>
			<?php print $late; ?>
		</th>
		<th>
			<?php print $a - $late; ?>
		</th>
		</tr>
<?php
}

$rown=1;
$months=array($month_key=>$month_val);
$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $months[$month_key], $toyear);
while(list($month,$monthno)=each($months)){
for($day=1; $day<=$daysinmonth; $day++){
if($day<10){
$sdate=$toyear.'-'.$monthno.'-'.'0'.$day;
}
else{
$sdate=$toyear.'-'.$monthno.'-'.$day;
}
$edate=$sdate;
$p=count_overall_attendance($sdate,$edate);
$a=count_overall_attendance($sdate,$edate,'%');
$late=count_overall_late($sdate,$edate);
				?>
				<tr>
				<td>
					<?php print $day; ?>
				</td>
				<td>
					<?php print $p + $late; ?>
				</td>
				<td>
					<?php print $late; ?>
				</td>
				<td>
					<?php print $a - $late; ?>
				</td>
				</tr>
				<?php
                }
                }
            ?>
				<tr>
				<th colspan="3">
				</th>
				<th>
					<a href="#Month"><?php print_string('cancel', $book); ?></a>
				</th>
				</tr>

	</table>
<?php
}
?>
