<?php 
/**										 community_list.php
 */

$action='community_list_action.php';

if(isset($_GET['comid'])){$comid=$_GET['comid'];}
if(isset($_GET['date'])){$date=$_GET['date'];}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}
if(isset($_POST['date'])){$date=$_POST['date'];}

	$com=get_community($comid);
	$comtype=$com['type'];
	if($comtype=='applied' or $comtype=='enquired' or 
	   $comtype=='accepted'){
		$students=listin_community($com);
		$enrolyear=$com['year'];
		list($enrolstatus,$yid)=split(':',$com['name']);
		$description=$yid.' ('.$enrolyear.')';
		$infobookcurrent='student_view_enrolment.php';
		}
	elseif($comtype=='accomodation'){
		$boarder=$com['name'];
		$infobookcurrent='student_view_boarder.php';
		if($date!=''){
			$students=(array)listin_community($com,$date);
			$description=' '.$boarder.' ('.$date.')';
			}
		else{
			$startdate='2000-01-01';
			$startdate='2010-01-01';
			$students=(array)listin_community($com,$enddate,$startdate);
			$description=' '.$boarder.' (overall)';
			}
		}

	three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <div class="center" id="viewcontent">
		<table class="listmenu" id="sidtable">
		  <caption>
			<?php print_string($comtype,$book);?>
		  </caption>
		  <tr>
			<th style="width:60%;"><?php print $description;?></th>
			<th>
<?php
			if($comtype!='accomodation'){
				$required='no';$multi='1';
				include('scripts/list_enrolstatus.php');
				}
?>
			</th>
		  </tr>
<?php
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
?>
		  <tr id="sid-<?php print $sid;?>">
			<td>
			  <a href="infobook.php?current=<?php print $infobookcurrent;?>&cancel=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>" target="viewinfobook" onClick="parent.viewBook('infobook');"><?php print $student['surname']. ', '.$student['forename']. 
				' '.$student['preferredforename']. ' ('.$student['dob'].')';?></a>
			</td>
		  <td>
			<input type="checkbox"  
				name="sids[]" value="<?php print $sid;?>" />
		  </td>
		</tr>
<?php
		}
?>
		</table>
	  </div>

	<input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" /> 
	<input type="hidden" name="date" value="<?php print $date;?>" /> 
	<input type="hidden" name="comid" value="<?php print $comid;?>" /> 
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
