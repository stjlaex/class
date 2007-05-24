<?php
/**								  		accomodation_matrix.php
 */

$choice='accomodation_matrix.php';
$action='accomodation_matrix_action.php';

$currentyear=get_curriculumyear();

if(isset($_POST['enrolyear']) and $_POST['enrolyear']!=''){$enrolyear=$_POST['enrolyear'];}
else{$enrolyear=$currentyear;}
if(isset($_POST['startday']) and $_POST['startday']!=''){$startday=$_POST['startday'];}
else{$startday=0;}

$coms=(array)list_communities('accomodation');
$tomonth=date('m');
$today=date('d');
$toyear=date('y');
if($tomonth<6){$tomonth=6;}
if($tomonth==6 and $today<18){$today=18;}
$overallstart=$toyear.'-'.$tomonth.'-'.$today;
$nodays=7;
$coldates=array();
for($day=0;$day<$nodays;$day++){
	$coldates[]=date('Y-m-d',mktime(0,0,0,$tomonth,$startday+$today+$day,2007));
	}

$capacity=850;
$totdays=74;
$totnos=array('B'=>0,'H'=>0,'GLB'=>0,'GLH'=>0);
for($day=0;$day<$totdays;$day++){
	$date=date('Y-m-d',mktime(0,0,0,6,18+$day,2007));
	reset($totnos);
	while(list($index,$tot)=each($totnos)){
		$com=array('type'=>'accomodation','name'=>'M'.$index);
		$tot+=countin_community($com,$date);
		$com=array('type'=>'accomodation','name'=>'F'.$index);
		$tot+=countin_community($com,$date);
		$totnos[$index]=$tot;
		//trigger_error('tot '.$index.' '.$tot,E_USER_WARNING);
		}
	}

//$extrabuttons['changeyear']=array('name'=>'sub','value'=>'Print');
twoplus_buttonmenu($enrolyear,$currentyear+3,$extrabuttons,$book);
?>
  <div id="viewcontent" class="content">
 	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	  <div class="center">

	  <table class="listmenu">
		<tr>
		  <th><?php print $enrolyear;?></th>
		  <th><?php print_string('overall');?></th>
<?php 
		  $totals=array();
		  while(list($index,$coldate)=each($coldates)){
			  $totals[$index]=0;
?>
			<th><?php print $coldate;?></th>
<?php
			}
?>
		</tr>
<?php
		$totoverall=0;
		reset($coms);
		while(list($index,$com)=each($coms)){
?>
		<tr>
		  <th>
<?php	     print $com['displayname'];?>
		  </th>
		  <td>
<?php
		$overall=countin_community($com,$overallstart,$date);
	    print $overall;
		$totoverall+=$overall;
?>
		  </td>
<?php
			reset($coldates);
			while(list($index,$coldate)=each($coldates)){
				$values[$index]=countin_community($com,$coldate);
				$totals[$index]+=$values[$index];
		  ?>
			<td><?php print $values[$index];?></td>
<?php
				}
?>
		</tr>
<?php
			}
	$nocol=0;
?>
		<tr>
		  <th>
			<?php print get_string('total',$book).' '.get_string('numberofstudents',$book);?>
		  </th>
		  <td><?php print $totoverall;?></td>
<?php
		reset($coldates);
		while(list($index,$coldate)=each($coldates)){
?>
		  <td><?php print $totals[$index];?></td>
<?php
			}
?>
		</tr>
	  </table>
	  </div>


	  <div class="right">
		<table class="listmenu">
		  <tr>
			<th><?php print_string('',$book);?></th>
			<th><?php print get_string('student').' '.get_string('weeks');?></th>
			<th><?php print get_string('groupleader','infobook').' '.get_string('weeks');?></th>
			<th><?php print_string('overall',$book);?></th>
		  </tr>
		  <tr>
			<th><?php print_string('boarder','infobook');?></th>
			<td><?php print round($totnos['B']/7);?></td>
			<td><?php print round($totnos['GLB']/7);?></td>
			<td><?php print round(($totnos['GLB']+$totnos['B'])/7);?></td>
		  </tr>
		  <tr>
			<th><?php print_string('hostfamily','infobook')?></th>
			<td><?php print round($totnos['H']/7);?></td>
			<td><?php print round($totnos['GLH']/7);?></td>
			<td><?php print round(($totnos['GLH']+$totnos['H'])/7);?></td>
		  </tr>
		  <tr>
			<th><?php print_string('total');?></th>
			<td><?php print round(($totnos['H']+$totnos['B'])/7);?></td>
			<td><?php print round(($totnos['GLH']+$totnos['GLB'])/7);?></td>
			<td><?php print round(($totnos['GLH']+$totnos['GLB']+$totnos['H']+$totnos['B'])/7);?></td>
		  </tr>
		  <tr>
			<th><?php print_string('capacity');?></th>
			<td><?php print $capacity;?></td>
			<td><?php print '';?></td>
			<td><?php print '';?></td>
		  </tr>
		  <tr>
			<th><?php print_string('spaces');?></th>
			<td><?php print $capacity-round(($totnos['H']+$totnos['B'])/7);?></td>
			<td><?php print '';?></td>
			<td><?php print '';?></td>
		  </tr>
		</table>
	  </div>



	  <input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" />
	  <input type="hidden" name="startday" value="<?php print $startday;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  </div>

