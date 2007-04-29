<?php
/**								  		enrolments_matrix.php
 */

$choice='enrolments_matrix.php';
$action='enrolments_matrix_action.php';

$currentyear=get_curriculumyear();
if(!isset($enrolyear)){$enrolyear=$currentyear;}
else{$enrolyear='2008';}
$extrabuttons['changeyear']=array('name'=>'year',
						'value'=>$enrolyear
						);
two_buttonmenu($extrabuttons,$book);
?>
  <div class="content">

	<div id="viewcontent">
	  <table class="listmenu">
		<tr>
		  <th><?php print $enrolyear;?></th>
		  <th><?php print_string('capacity',$book);?></th>
		  <th><?php print_string('reenrolled',$book);?></th>
		  <th><?php print_string('newenrolments',$book);?></th>
		  <th><?php print_string('leavers',$book);?></th>
		  <th><?php print_string('currentroll',$book);?></th>
		  <th><?php print_string('spaces',$book);?></th>
		  <th><?php print_string('applications',$book);?></th>
		  <th><?php print_string('waitinglist',$book);?></th>
		</tr>
<?php
	$totals=array();
	$d_year=mysql_query("SELECT * FROM yeargroup ORDER BY section_id, id");
	while($year=mysql_fetch_array($d_year,MYSQL_ASSOC)){
		$nocol=0;
		$values=array();
		$yid=$year['id'];
		if($enrolyear==$currentyear){
			$comid=update_community(array('type'=>'year','name'=>$yid));
			}
		else{
			$comid=update_community(array('type'=>'accepted','name'=>'AC:'.$yid,'year'=>$enrolyear));
			}
		/*
		/*cell 1 is capacity*/
		$com=get_community($comid);
		$values[$nocol]=$com['capacity'];
		$totals[$nocol]+=$values[$nocol];
		$nocol++;

		/*cell 2 is current*/
		$values[$nocol]=countin_community(array('id'=>$comid));
		$totals[$nocol]+=$values[$nocol];
		$nocol++;

		/*cell 3 is spaces*/
		$values[$nocol]=$com['capacity']-countin_community($comid);
		$totals[$nocol]+=$values[$nocol];
		$nocol++;

		/*cell 4 is */
		$values[$nocol]=$nosids;
		$totals[$nocol]=$nosids+$totals[$nocol];
		$nocol++;

		/*cell 5 is spaces*/
		$values[$nocol]=$nosids;
		$totals[$nocol]=$nosids+$totals[$nocol];
		$nocol++;

		/*cell 6 is spaces*/
		$values[$nocol]=$nosids;
		$totals[$nocol]=$nosids+$totals[$nocol];
		$nocol++;

		/*cell 7 is spaces*/
		$values[$nocol]=$nosids;
		$totals[$nocol]=$nosids+$totals[$nocol];
		$nocol++;

		/*cell 8 is spaces*/
		$values[$nocol]=$comid;
		$totals[$nocol]+=$nosids;
		$nocol++;

?>
		<tr>
		  <td>
<?php
		$nocol=0;
	    print '<a href="admin.php?current=yeargroup_edit.php&cancel='.
				 $choice.'&choice='. $choice.'&enrolyear='. $enrolyear.
				  '&comid='.$com['id'].'">' .$year['name'].'</a>';
?>
		  </td>
		  <td><?php print $values[$nocol++];?></td>
		  <td><?php print $values[$nocol++];?></td>
		  <td><?php print $values[$nocol++];?></td>
		  <td><?php print $values[$nocol++];?></td>
		  <td><?php print $values[$nocol++];?></td>
		  <td><?php print $values[$nocol++];?></td>
		  <td><?php print $values[$nocol++];?></td>
		  <td><?php print $values[$nocol++];?></td>
		</tr>
<?php
		}
	$nocol=0;
?>
		<tr>
		  <th>
			<?php print get_string('total',$book).' '.get_string('numberofstudents',$book);?>
		  </th>
		  <td><?php print $totals[$nocol++];?></td>
		  <td><?php print $totals[$nocol++];?></td>
		  <td><?php print $totals[$nocol++];?></td>
		  <td><?php print $totals[$nocol++];?></td>
		  <td><?php print $totals[$nocol++];?></td>
		  <td><?php print $totals[$nocol++];?></td>
		  <td><?php print $totals[$nocol++];?></td>
		  <td><?php print $totals[$nocol++];?></td>
		</tr>
	  </table>
	</div>

  </div>