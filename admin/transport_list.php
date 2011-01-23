<?php
/**                                  transport_list.php
 *
 *
 */

$action='transport_list_action.php';

include('scripts/sub_action.php');

if((isset($_POST['busname']) and $_POST['busname']!='')){$busname=$_POST['busname'];}else{$busname='';}
if((isset($_GET['busname']) and $_GET['busname']!='')){$busname=$_GET['busname'];}
if((isset($_POST['fid']) and $_POST['fid']!='')){$fid=$_POST['fid'];}else{$fid='';}
if((isset($_GET['fid']) and $_GET['fid']!='')){$fid=$_GET['fid'];}
if((isset($_POST['date0']) and $_POST['date0']!='')){$todate=$_POST['date0'];}else{$todate=date('Y-m-d');}
if((isset($_GET['date0']) and $_GET['date0']!='')){$todate=$_GET['date0'];}

$today=date('N',strtotime($todate));

if($busname!=''){
	$listtype='b';
	$com=array('id'=>'','type'=>'transport','name'=>$busname);
	$students=(array)list_bus_journey_students($busname,$todate,5);
	}
elseif($fid!=''){
	$listtype='f';
	$com=array('id'=>'','type'=>'form','name'=>$fid);
	$students=(array)listin_community($com);
	}
else{
	$students=array();
	}

$extrabuttons=array();
$extrabuttons['morning']=array('name'=>'current',
							   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							   'value'=>'transport_print.php',
							   'xmlcontainerid'=>'listin',
							   'onclick'=>'checksidsAction(this)');
$extrabuttons['afternoon']=array('name'=>'current',
								 'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
								 'value'=>'transport_print.php',
								 'xmlcontainerid'=>'listout',
								 'onclick'=>'checksidsAction(this)');
$extrabuttons['changes']=array('name'=>'current',
							   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							   'value'=>'transport_print.php',
							   'xmlcontainerid'=>'changes',
							   'onclick'=>'checksidsAction(this)');
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
		print '<th '.$colclass.'>'.get_string($dayname,$book).'<br />'.$date;
		print '<input type="radio" name="date0" value="'.$date.'" /></th>';
		}
?>
		  </tr>
		</thead>
<?php
	$rown=1;
	foreach($students as $student){
		$sid=$student['id'];
		print '<tr id="sid-'.$sid.'">';
		print '<td>'.'<input type="checkbox" name="sids[]" value="'.$sid.'" />'.$rown++.'</td><td></td>';
		print '<td class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_transport.php&sid='.$sid.'">'.$student['surname'].', '. $student['forename'].'</a></td>';
		print '<td>'.$student['form_id'].'</td>';
		foreach($days as $day=>$dayname){
			$bookings=(array)list_student_journey_bookings($sid,$dates[$day],$day);
			$divin='';$divout='';
			$openId=$sid.'-'.$day;
			foreach($bookings as $booking){
				if($buses[$booking['bus_id']]['direction']=='I'){$divname='divin';$divclass='midlite';}
				else{$divname='divout';$divclass='gomidlite';}
				if($$divname==''){
					$divaction='onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\''.$booking['id'].'\',\''.$openId.'\');"';
					if($booking['comment']!=''){$$divname='<span title="'.$booking['comment'].'">';}
					$$divname.='<div '.$divaction.' class="'.$divclass.'">'.$buses[$booking['bus_id']]['name'].'</div>';
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
	<div id="xml-listin" style="display:none;">
	  <params>
		<selectname>date0</selectname>
		<sids><?php print $listtype.'-'.$busname;?></sids>
		<length>full</length>
		<transform>transport_list_in</transform>
		<paper>landscape</paper>
	  </params>
	</div>
	<div id="xml-listout" style="display:none;">
	  <params>
		<selectname>date0</selectname>
		<sids><?php print $listtype.'-'.$busname;?></sids>
		<length>full</length>
		<transform>transport_list_out</transform>
		<paper>landscape</paper>
	  </params>
	</div>
	<div id="xml-changes" style="display:none;">
	  <params>
		<selectname>date0</selectname>
		<sids><?php print $listtype.'-'.$busname;?></sids>
		<length>short</length>
		<transform>transport_list_changes</transform>
		<paper>landscape</paper>
	  </params>
	</div>
