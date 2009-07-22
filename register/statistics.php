<?php
/**									statistics.php
 *
 */

$action='statistics_action.php';
$choice='statistics.php';

$toyear=get_curriculumyear()-1;//TODO: set a proper start of term date

include('scripts/sub_action.php');

//threeplus_buttonmenu($startday,2,$extrabuttons);
two_buttonmenu($extrabuttons);
?>
  <div id="heading">
	<label><?php print_string('absences','register');?></label>
  </div>
  <div id="viewcontent" class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu sidtable">
		<tr>
		  <th>Month</th>
		  <th><?php print_string('present',$book);?></th>
		  <th><?php print_string('absent',$book);?></th>
		  <th>No. of days</th>
		  <th>Average daily attendance</th>
		</tr>
<?php

	$rown=1;
	$months=array('August'=>'08','September'=>'09','October'=>'10','November'=>'11','December'=>'12','January'=>'01','Febuary'=>'02','March'=>'03','April'=>'04','May'=>'05','June'=>'06','July'=>'07');
	while(list($month,$monthno)=each($months)){
		if($monthno=='01'){$toyear++;}
		$sdate=$toyear.'-'.$monthno.'-'.'00';
		$edate=$toyear.'-'.$monthno.'-'.'31';
		$events=(array)list_events($sdate,$edate,'AM');
		$daysno=sizeof($events);
		$p=count_overall_attendance($sdate,$edate);
		$a=count_overall_attendance($sdate,$edate,'%');
?>
		<tr>
		<th>
			<?php print $month; ?>
		</th>
		<td>
			<?php print $p; ?>
		</td>
		<td>
			<?php print $a; ?>
		</td>
		<td>
		<?php print $daysno; ?>
		</td>
		<td>
		<?php print round($p/$daysno); ?>
		</td>
		</tr>
<?php
			}
?>
		</table>

		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	    <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  </form>
  </div>

<?php
	$today=date('Y-m-d');
?>
