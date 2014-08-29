<?php
/**                                  student_attendance.php
 *
 *
 * TODO: complete so it uses school start date to offer all previous years.
 */

$cancel='student_view.php';

include('scripts/sub_action.php');

two_buttonmenu();
$date1=date('Y-m-d');
$currentyear=get_curriculumyear();
$yearstart=$currentyear-1;
$date0=$yearstart.'-08-20';

$script='report_attendance_print.php';

$reports=array();
$params=array(
			  'uniqueid'=>$sid,
			  'sids[]'=>$sid,
			  'startdate'=>$date0,
			  'enddate'=>$date1
			  );
$url=url_construct($params,$script);
$reports[]=array('title'=>'Current academic year','url'=>$url);



$date1=$yearstart.'-08-01';
$yearstart--;
$date0=$yearstart.'-08-20';
$params=array(
			  'uniqueid'=>$sid,
			  'sids[]'=>$sid,
			  'startdate'=>$date0,
			  'enddate'=>$date1
			  );
$url=url_construct($params,$script);
$reports[]=array('title'=>'Previous academic year','url'=>$url);

$oldest_date=date('Y-m-d');
$d_a=mysql_query("SELECT * FROM attendance WHERE student_id='$sid';");
while($attendance=mysql_fetch_array($d_a,MYSQL_ASSOC)){
	$eventid=$attendance['event_id'];
	$d_e=mysql_query("SELECT date FROM event WHERE id='$eventid';");
	$date=mysql_result($d_e,0,'date');
	if($date<$oldest_date){
		$date_elements=explode("-",$date);
		$oldest_year=$date_elements[0];
		$oldest_date=$date;
		}
	}

$yearend=$yearstart;
if($oldest_date>=($oldest_year.'-08-20')){$yearstart=$oldest_year;}
else{$yearstart=$oldest_year-1;}

for($end=$yearend;$end>$yearstart;$end--){
	$date1=$end.'-08-01';
	$date0=($end-1).'-08-20';

	$params=array(
			  'uniqueid'=>$sid,
			  'sids[]'=>$sid,
			  'startdate'=>$date0,
			  'enddate'=>$date1
			  );
	$url=url_construct($params,$script);
	$reports[]=array('title'=>'Year '.$end.'-'.($end-1),'url'=>$url);
	}
?>
    <div id="heading">
        <h4><label><?php print_string('attendance'); ?></label> <?php print $Student['DisplayFullName']['value'];?></h4>
    </div>
    <div class="content">
	  <fieldset class="divgroup listmenu">
		<h5><?php print get_string('absent','register');?></h5>
		<table>
		<thead>
		  <tr>
			<th colspan="4"> </th>

<?php
	$days=getEnumArray('dayofweek');
	$todate=date('Y-m-d');
	$today=date('N');
	$dates=array();
	foreach($days as $day => $dayname){
		$daydiff=$day-$today;
		$date=date('Y-m-d',strtotime($daydiff.' day'));
		$dates[$day]=$date;
		if($todate==$date){$colclass='style="background-color:#cfcfcf;"';}
		else{$colclass='';}
		print '<th '.$colclass.'>'.get_string($dayname,$book).'<br />'.$date.'</th>';
		}
?>
		  </tr>
		</thead>
<?php
	print '<tr id="sid-'.$sid.'">';
   	print '<td>'.'<input type="checkbox" name="sids[]" value="'.$sid.'" />'.$rown++.'</td>';
   	print '<td colspan="2" class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_view.php&sid='.$sid.'">'.$Student['Surname']['value'].', '. $Student['Forename']['value'].'</a></td>';
   	print '<td>'.$Student['RegistrationGroup']['value'].'</td>';
		foreach($days as $day=>$dayname){
			$bookings=array();
			$ambookings=(array)list_student_attendance_bookings($sid,$dates[$day],$day,'AM');
			$pmbookings=(array)list_student_attendance_bookings($sid,$dates[$day],$day,'PM');
			$bookings=array_merge($ambookings,$pmbookings);
			$divam='';$divpm='';
			$openId=$sid.'-'.$day;
			foreach($bookings as $b){
				if($b['session']=='AM'){$divname='divam';$divclass='pauselite';}
				else{$divname='divpm';$divclass='pauselite';}
				if($$divname==''){
					$divaction='onClick="clickToEditAttendance('.$sid.',\''.$dates[$day].'\',\''.$b['id'].'\',\''.$openId.'\',\'infobook\');"';
					if($b['comment']!=''){$$divname='<span title="'.$b['comment'].'">';}
					$$divname.='<div '.$divaction.' class="'.$divclass.' center" style="text-align:center;font-weight:600;">'.$b['code'].'</div>';
					if($b['comment']!=''){$$divname.='</span>';}
					}
				}

			if($divam==''){$divam='<div onClick="clickToEditAttendance('.$sid.',\''.$dates[$day].'\',\'-1\',\''.$openId.'\',\'infobook\');" class="lowlite">'.'ADD'.'</div>';}
			if($divpm==''){$divpm='<div onClick="clickToEditAttendance('.$sid.',\''.$dates[$day].'\',\'-2\',\''.$openId.'\',\'infobook\');" class="lowlite">'.'ADD'.'</div>';}
			print '<td class="clicktoaction">'.$divam . $divpm.'</td>';
			}
		print '</tr>';
?>
		</table>
	  </fieldset>

	<fieldset class="divgroup">
	  <h5>
			<?php print get_string('attendance','reportbook'). ' '.get_string('reports',$book);?>
	  </h5>
<?php
	foreach($reports as $report){
?>
			<div class="center" style="margin-bottom: 15px;">
			<h6> <?php print $report['title'];?>
    			<a  title="<?php print_string('print');?>" onclick="clickToPresent('reportbook','<?php print $report['url'];?>','attendance_summary')" >
    			     <span class="clicktoprint"></span>
    			</a>
			</h6>
			</div>
<?php
		}
?>
	</fieldset>

	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
	  <input type="hidden" name="choice" value="<?php print $choice;?>"/>
	</form>
</div>
