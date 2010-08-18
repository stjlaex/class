<?php
/**                                  transport_list.php
 *
 *
 */

$action='transport_list_action.php';

include('scripts/sub_action.php');

$extrabuttons=array();
if((isset($_POST['busname']) and $_POST['busname']!='')){$busname=$_POST['busname'];}else{$busname='';}
if((isset($_GET['busname']) and $_GET['busname']!='')){$busname=$_GET['busname'];}
if((isset($_POST['fid']) and $_POST['fid']!='')){$fid=$_POST['fid'];}else{$fid='';}
if((isset($_GET['fid']) and $_GET['fid']!='')){$fid=$_GET['fid'];}
$todate=date('Y-m-d');
$today=date('N');

if($busname!=''){
	$com=array('id'=>'','type'=>'transport','name'=>$busname);
	$students=(array)listin_community($com);
	}
elseif($fid!=''){
	$com=array('id'=>'','type'=>'form','name'=>$fid);
	$students=(array)listin_community($com);
	}
else{
	$students=array();
	}
//$Bus=fetchBus();

two_buttonmenu($extrabuttons,$book);

	if($busname!=-1){
?>
  <div id="heading">
	<label><?php print_string('transport',$book);?></label>
<?php	print $busname.' - '.display_date($todate);?>
  </div>
<?php
		}
?>
  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <table class="listmenu sidtable">
		<caption><?php print get_string($com['type'],$book).': '.$com['name'];?></caption>
		<thead>
		  <tr>
			<th colspan="4">&nbsp;</th>
<?php
	$buses=list_buses();
	$days=getEnumArray('dayofweek');
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
	$rown=1;
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
		print '<tr id="sid-'.$sid.'">';
		print '<td>'.'<input type="checkbox" name="sids[]" value="'.$sid.'" />'.$rown++.'</td>';
		print '<td colspan="2" class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_view.php&sid='.$sid.'">'.$student['surname'].', '. $student['forename'].'</a></td>';
		print '<td>'.$student['form_id'].'</td>';
		foreach($days as $day=>$dayname){
			$bookings=array();
			$bookings=(array)list_student_journey_bookings($sid,$dates[$day],$day);
			$divin='';$divout='';
			$openId=$sid.'-'.$day;
			foreach($bookings as $bindex => $booking){
				if($buses[$booking['bus_id']]['direction']=='I'){$divname='divin';$divclass='midlite';}
				else{$divname='divout';$divclass='gomidlite';}
				if($$divname==''){
					$divaction='onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\''.$booking['id'].'\',\''.$openId.'\');"';
					if($booking['comment']!=''){$$divname='<span title="'.$booking['comment'].'">';}
					$$divname.='<div '.$divaction.' class="'.$divclass.'">'.$buses[$booking['bus_id']]['name'].' : '.$booking['stop_id'].'</div>';
					if($booking['comment']!=''){$$divname.='</span>';}
					}
				}

			//if($divin=='' and $divout==''){$divaction='';}
			if($divin==''){$divin='<div onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\'-1\',\''.$openId.'\');" class="lowlite">'.'ADD BUS'.'</div>';}
			if($divout==''){$divout='<div onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\'-2\',\''.$openId.'\');" class="lowlite">'.'ADD BUS'.'</div>';}
			print '<td class="clicktoaction">'.$divin . $divout.'</td>';
			}
		print '</tr>';
		}
?>
	  </table>
	</div>


	<input type="hidden" name="busname" value="<?php print $busname;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>

