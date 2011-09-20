<?php
/**
 *                                  student_transport.php
 */

$action='student_transport_action.php';
$newcomtype='TUTOR';
$communities=list_communities($newcomtype);
if(isset($_POST['startday'])){$startday=$_POST['startday'];}else{$startday=0;}

$extrabuttons=array();
threeplus_buttonmenu($startday,3,$extrabuttons);

	/*Check user has permission to view*/
	$perm=getFormPerm($Student['RegistrationGroup']['value']);
	include('scripts/perm_action.php');

?>
  <div id="heading">
	<?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center listmenu">
		<legend><?php print get_string('transport',$book);?></legend>
		<div class="center">
		<table>
		<thead>
		  <tr>
			<th colspan="4"> </th>

<?php
	$buses=list_buses();
	$days=getEnumArray('dayofweek');

	$todate=date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));
	$today=date('N',mktime(0,0,0,date('m'),date('d'),date('Y')));
	$dates=array();
	foreach($days as $day => $dayname){
		$daydiff=$startday+$day-$today;
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
			$bookings=(array)list_student_journey_bookings($sid,$dates[$day],$day);
			$divin='';$divout='';
			$openId=$sid.'-'.$day;
			foreach($bookings as $booking){
				$bus=get_bus($booking['bus_id']);
				$stops=list_bus_stops($booking['bus_id']);
				if(array_key_exists($booking['stop_id'],$stops)){$stop=$stops[$booking['stop_id']];}
				else{$stop=array('name'=>'');}

				if($bus['direction']=='I'){$divname='divin';$divclass='midlite';}
				else{$divname='divout';$divclass='gomidlite';}
				if($$divname==''){
					$divaction='onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\''.$booking['id'].'\',\''.$openId.'\');"';
					if($booking['comment']!=''){$$divname='<span title="'.$booking['comment'].'">';}
					$$divname.='<div '.$divaction.' class="'.$divclass.'">'.$bus['name'].' <br /><div style="font-size:7pt;color:#909090;">'.$stop['name'].'</div></div>';
					if($booking['comment']!=''){$$divname.='</span>';}
					}
				}

			//if($divin=='' and $divout==''){$divaction='';}
			if($divin==''){$divin='<div onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\'-1\',\''.$openId.'\');" class="lowlite">'.'ADD BUS'.'</div>';}
			if($divout==''){$divout='<div onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\'-2\',\''.$openId.'\');" class="lowlite">'.'ADD BUS'.'</div>';}
			print '<td class="clicktoaction">'.$divin . $divout.'</td>';
			}
		print '</tr>';
?>
		</table>
		</div>
	  </fieldset>

	  <fieldset class="center listmenu">
		<legend><?php print get_string('current',$book).' '.get_string('club',$book);?></legend>
		<div>
		  <table>
			<tr>
			  <thead>
				<th style="width:40%;"></th>
				<th style="width:20%;"><?php print_string('start',$book);?></th>
				<th style="width:20%;"><?php print_string('end',$book);?></th>
				<th style="width:20%;"><?php //print_string('fee',$book);?></th>
			  </thead>
			</tr>			
<?php

	$feetypes=array('0'=>'0','5'=>'0.5','10'=>'1.0');

	$coms=list_member_communities($sid,array('id'=>'','name'=>'','type'=>'tutor'));
	$excoms=list_member_communities($sid,array('id'=>'','name'=>'','type'=>'tutor'),false);
	foreach($coms as $com){
		if($com['special']==''){$fee='10';}else{$fee=$com['special'];}
		print '<tr><td style="width:40%;"><a target="viewadmin" onclick="parent.viewBook(\'admin\');" href="admin.php?current=community_group_edit.php&cancel=community_group.php&choice=community_group.php&newcomtype='.$com['type'].'&comid='.$com['id'].'">'.$com['name'] .'</a></td><td>'.$com['joiningdate'].'</td><td>&nbsp;'.$com['leavingdate'].'</td>';

		print '<td class="row">';
		foreach($feetypes as $value => $label){
			$checkclass='';
			if($fee!='' and $value==$fee){
				$checkclass='checked';
				}
			print '<div class="'.$checkclass.'">';
			print '<input type="radio" name="'.$com['id'].'fee'.$sid.'"
						tabindex="'.$tab++.'" value="'.$value.'" '.$checkclass;
			print '>'.$label.'</input></div>';
			}
		print '</td></tr>';
		}
?>
<tr>
<td>
<br />
<?php
			$listname='newcomid';$listlabel='new';$required='no';
			include('scripts/set_list_vars.php');
			list_select_list($communities,$listoptions,$book);
?>
</td>
	<td class="rowaction">
				<?php 
				$required='no'; //include('scripts/jsdate-form.php');
				?>
	</td>
<td>
</td>
<td>
<br />
<?php
	$buttons=array();
	$buttons['new']=array('title'=>'new','name'=>'new','value'=>$newcomtype);
	all_extrabuttons($buttons,'infobook','processContent(this)')
?>
</td>
</tr>
		  </table>
		</div>
	  </fieldset>


	  <fieldset class="center listmenu">
		<legend><?php print get_string('previous',$book).' '.get_string('club',$book);?></legend>
		<div>
		  <table>
			<tr>
			  <thead>
				<th style="width:40%;"></th>
				<th style="width:20%;"></th>
				<th style="width:20%;"></th>
				<th style="width:20%;"></th>
			  </thead>
			</tr>
<?php
	foreach($excoms as $com){
		print '<tr class="lowlite"><td>'.$com['name'] .'</td><td>'.$com['joiningdate'].'</td><td>'.$com['leavingdate'].'</td>';
		print '<td class="row">';
		foreach($feetypes as $value => $label){
			$checkclass='';
			if($fee!='' and $value==$fee){
				$checkclass='checked';
				}
			print '<div class="'.$checkclass.'">';
			print '<input type="radio" name="'.$com['id'].'fee'.$sid.'"
						tabindex="'.$tab++.'" value="'.$value.'" '.$checkclass;
			print '>'.$label.'</input></div>';
			}
		print '</td></tr>';
		}
?>
			
		  </table>
		</div>
	  </fieldset>


	    <input type="hidden" name="startday" value="<?php print $startday;?>" />
	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print $cancel;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
</div>
