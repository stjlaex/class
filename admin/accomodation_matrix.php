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

//$todate=date("Y-m-d");
$nodays=14;
$coldates=array();
for($day=0;$day<$nodays;$day++){
	//$coldates[]=date('Y-m-d',mktime(0,0,0,date('m')+$offset,date('d')+$startday+$day,date('Y')));
	$coldates[]=date('Y-m-d',mktime(0,0,0,6,18+$startday+$day,2007));
	}

//$extrabuttons['changeyear']=array('name'=>'sub','value'=>'Print');
twoplus_buttonmenu($enrolyear,$currentyear+3,$extrabuttons,$book);
?>
  <div id="viewcontent" class="content">
 	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >
	  <table class="listmenu">
		<tr>
		  <th><?php print $enrolyear;?></th>
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
		$coms=(array)list_communities('accomodation');
		while(list($index,$com)=each($coms)){
?>
		<tr>
		  <td style="font-size:large;">
<?php
		    print $com['displayname'];
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

	  <input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" />
	  <input type="hidden" name="startday" value="<?php print $startday;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  </div>

