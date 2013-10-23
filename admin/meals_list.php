<?php
/**                                  transport_list.php
 *
 *
 */

$action='meals.php';
$choice='meals.php';

include('scripts/sub_action.php');

/*Variables*/
if((isset($_POST['meal']) and $_POST['meal']!='')){$meal=$_POST['meal'];}else{$meal='';}
if((isset($_GET['meal']) and $_GET['meal']!='')){$meal=$_GET['meal'];}
if((isset($_POST['mealid']) and $_POST['mealid']!='')){$mealid=$_POST['mealid'];}else{$mealid='';}
if((isset($_GET['mealid']) and $_GET['mealid']!='')){$mealid=$_GET['mealid'];}
if((isset($_POST['comid']) and $_POST['comid']!='')){$comid=$_POST['comid'];}else{$comid='';}
if((isset($_GET['comid']) and $_GET['comid']!='')){$comid=$_GET['comid'];}
if((isset($_POST['date0']) and $_POST['date0']!='')){$todate=$_POST['date0'];}else{$todate=date('Y-m-d');}
if((isset($_GET['date0']) and $_GET['date0']!='')){$todate=$_GET['date0'];}

$today=date('N',strtotime($todate));
/* calculate difference in days from now for past attendance */
$d=explode('-',$todate);
$diff=mktime(0,0,0,date('m'),date('d'),date('Y'))-mktime(0,0,0,$d[1],$d[2],$d[0]);
$attday=-round($diff/(60*60*24));

$extrabuttons=array();

/*Displays a meal with a list of students*/
if($meal!=''){
	$listtype='b';
	$com=array('id'=>'','type'=>'meal','name'=>$meal);
	$students=(array)list_meals_students($meal,$todate,5);
	}
/*Displays a formgroup*/
elseif($comid!=''){
	$listtype='f';
	$com=get_community($comid);
	$fid=$com['name'];
	$students=(array)listin_community($com);
	}
else{
	$students=array();
	}


two_buttonmenu($extrabuttons,$book);

	if($meal!=''){
?>
  <div id="heading">
	<label><?php print_string('meals',$book);?></label>
<?php	print $meal.' - '.display_date($todate);?>
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
			<th colspan="3">&nbsp;</th>
<?php
	$days=getEnumArray('dayofweek');
	if($meal!=''){print '<th>'; print_string('everyday',$book); print '</th>';}
	if($comid!=''){
		$meals_list=list_meals();
		print '<th>'; 
		print_string('everyday',$book); 
		print '<div>';
		print '<label for="selectforall" >Check all</label><select id="selectforall" onChange="enableMealForAll();" ">'; 
		print '<option value="-1"></option>'; 
		foreach($meals_list as $meal_list){
			$opt='<option value="'.$meal_list['id'].'">'.$meal_list['name'].'</option>'; 
			if($meal_list['name']!='NOT LUNCHING'){print $opt;}
			}
		print '</select>';
		print '</div>';
		print '</th>';
		}
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
		$attendances[$sid]=(array)fetchAttendances($sid);
		$meals_list=list_meals();
		print '<tr id="sid-'.$sid.'">';
		print '<td>'.'<input type="checkbox" id="sids['.$rown.']" name="sids[]" value="'.$sid.'" />'.$rown++.'</td><td></td>';
		if($comid!=''){
			print '<td class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_view.php&sid='.$sid.'">'.$student['surname'].', '. $student['forename'].'</a></td>';
			$bs=get_student_booking($sid,'0000-00-00');
			print '<td><select id="everydayselect_'.$sid.'" onChange="enableMealEveryday(\'mealselect\',\''.$sid.'\',\''.$dates[$today].'\',\''.$mealid.'\');">'; 
			print '<option value="-1"></option>'; 
			foreach($meals_list as $meal_list){
				$opt='<option value="'.$meal_list['id'].'">'.$meal_list['name'].'</option>'; 
				foreach($bs as $booking){
					if($booking['name']==$meal_list['name'] and $booking['enddate']=='0000-00-00'){$opt='<option selected value="'.$meal_list['id'].'">'.$meal_list['name'].'</option>';}
					}
				if($meal_list['name']!='NOT LUNCHING'){print $opt;}
				}
			print '</select><br>';
			$comments=get_booking_comments($sid);
			print '<div id="everydaycomment_container_'.$sid.'" style="float:left">';
			foreach($comments as $key=>$comment){
				if($comment['enddate']=='0000-00-00' and $comment['day']=='%' and $comment['meal_id']!='0'){print '<label for="everydaycomment_'.$sid.'">Comment</label><input id="everydaycomment_'.$sid.'" name="everydaycomment_'.$sid.'" type="text" value="'.$comment['comment'].'" onchange="addMealComment('.$sid.','.$comment['id'].');">';}
				}
			print '</div>';
			print '</td>';
			}
		if($meal!=''){print '<td class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_view.php&sid='.$sid.'">'.$student['surname'].', '. $student['forename'].' ('.$student['form_id'].')</a></td>';}
		$html='';
		$everycheck='';
		foreach($days as $day=>$dayname){
			$absence=' ';
			$bookings=get_student_booking($sid,$dates[$day]);
			/*Meal's checkboxes for every day*/
			if($meal!=''){
				
				$divmeal='<div class="lowlite">';
				$divmealoption='<input type="checkbox" id="mealcheckbox_'.$sid.'_'.$day.'" onClick="clickToEditMeal('.$sid.',\''.$dates[$day].'\',\''.$mealid.'\',\''.$day.'\');" ';
				foreach($bookings as $booking){
					/*Checked if it's booked*/
					if($booking['name']==$meal and $booking['startdate']<=$dates[$day] and ($booking['enddate']>$dates[$day] or $booking['enddate']=='0000-00-00')){$divmealoption.=' checked="true" ';}
					/*Disabled if another meal it's booked on the same day*/
					if($booking['name']!=$meal and $booking['startdate']<=$dates[$day] and ($booking['enddate']>$dates[$day] or $booking['enddate']=='0000-00-00')){$divmealoption.=' disabled ';}
					/*Disabled the past days*/
					if($dates[$day]<$dates[$today]){$divmealoption.=' disabled ';}
					/*If it's booked with everyday option then the checkbox is checked'*/
					if($booking['enddate']=='0000-00-00' and $booking['meal_id']==$mealid){$everycheck=' checked="true" ';}
					}
				$divmealoption.='>';
				foreach($attendances as $stdid=>$std){
					foreach($std as $attendance){
						foreach($attendance as $att){
							/*Displays an A for the absent (not late) students*/
							if(($att['Status']['value']!='p' and ($att['Status']['value']=='a' and ($att['Code']['value']!='L' and $att['Code']['value']!='UA' and $att['Code']['value']!='UB' and $att['Code']['value']!='U'))) and $att['Date']['value']==$dates[$day] and $stdid==$sid){$absence=' A';}
							}
						}
					}
				$divmeal.=$divmealoption;
				$divmeal.=$absence.'</div>';
				$html.='<td class="clicktoaction">'.$divmeal.'</td>';
				$html.='<input type="hidden" id="form_choice" value="meal">';
				}
			/*Meals drop down menu for a form group*/
			if($comid!=''){
				if($dates[$day]<$dates[$today]){$divmeal2='<div class="lowlite"><select disabled id=\'meals_select_'.$sid.'_'.$dates[$day].'\'>';}
				else{$divmeal2='<div class="lowlite"><select onchange="clickToEditMeal('.$sid.',\''.$dates[$day].'\',\'\',\''.$day.'\');" id=\'meals_select_'.$sid.'_'.$dates[$day].'\'>';}
				/*Delete/blank option*/
				$divmeal2.='<option value="-1"></option>';
				/*All meals*/
				foreach($meals_list as $meal_list){
					$divmealoption='<option value="'.$meal_list['id'].'">'.$meal_list['name'].'</option>';
					foreach($bookings as $booking){
						/*Selected the booked meal*/
						if($booking['name']==$meal_list['name'] and $booking['startdate']<=$dates[$day] and ($booking['enddate']>$dates[$day] or $booking['enddate']=='0000-00-00')){$divmealoption='<option selected value="'.$meal_list['id'].'">'.$meal_list['name'].'</option>';$selected[$sid.'_'.$dates[$day]]=$meal_list['id'];}
						}
						$divmeal2.=$divmealoption;
					}
				foreach($attendances as $stdid=>$std){
					foreach($std as $attendance){
						foreach($attendance as $att){
							/*Displays an A for the absent (not late) student*/
							if(($att['Status']['value']!='p' and ($att['Status']['value']=='a' and ($att['Code']['value']!='L' and $att['Code']['value']!='UA' and $att['Code']['value']!='UB' and $att['Code']['value']!='U'))) and $att['Date']['value']==$dates[$day] and $stdid==$sid){$absence=' A';}
							}
						}
					}
				$divmeal2.='</select>'.$absence;
				$comments=get_booking_comments($sid);
				$divmeal2.='<br><div id="daycomment_container_'.$sid.'_'.$day.'" style="float:left">';
				foreach($comments as $key=>$comment){
					if($comment['startdate']==$dates[$day] and $comment['day']==$day and $comment['meal_id']!='0' and $comment['comment']!='falsus'){$divmeal2.='<label for="daycomment_'.$sid.'_'.$day.'">Comment</label><input id="daycomment_'.$sid.'_'.$day.'" name="daycomment_'.$sid.'_'.$day.'" type="text" value="'.$comment['comment'].'" onchange="addMealComment('.$sid.','.$comment['id'].','.$day.');">';}
					}
				$divmeal2.='</div>';
				$divmeal2.='</div>';
				$html.='<td class="clicktoaction">'.$divmeal2.'</td>';
				$html.='<input type="hidden" id="selected_'.$sid.'_'.$dates[$day].'" value="'.$selected[$sid.'_'.$dates[$day]].'">';
				$html.='<input type="hidden" id="form_choice" value="student">';
				}
			/*Everyday checkbox: books all days from today*/
			if($meal!=''){$inputevery='<td><input type="checkbox" '.$everycheck.' id="everyday_'.$sid.'" style="float:none" onClick="enableMealEveryday(\'mealcheckbox\',\''.$sid.'\',\''.$dates[$today].'\',\''.$mealid.'\');"></td>';}
			else{$inputevery='';}
			}
		print $inputevery;
		print $html;
		print '</tr>';
		}
?>
	  </table>
	  *A: Absent
	</div>

	<input type="hidden" id="dayno" value="<?php print $today;?>" />
	<input type="hidden" name="date" value="<?php print $todate;?>" />
	<input type="hidden" id="sno" value="<?php print $rown;?>" />
	<input type="hidden" name="meal" value="<?php print $meal;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>
