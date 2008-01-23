<?php
/**								  		enrolments_matrix.php
 */

$choice='enrolments_matrix.php';
$action='enrolments_matrix_action.php';

$currentyear=get_curriculumyear();

if(isset($_POST['enrolyear']) and $_POST['enrolyear']!=''){$enrolyear=$_POST['enrolyear'];}
else{$enrolyear=$currentyear;}

$extrabuttons=array();
twoplus_buttonmenu($enrolyear,$currentyear+3,$extrabuttons,$book);

$rowcells=array();
$rowcells=list_enrolmentsteps();

?>
  <div id="viewcontent" class="content">
 	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	  <table class="listmenu">
		<tr>
		  <th><?php print display_curriculumyear($enrolyear);?></th>
<?php
		  reset($rowcells);
		  $totals=array();
		  $totals[0]=0;
		  while(list($index,$enrolstatus)=each($rowcells)){ 
			  $totals[$index+1]=0;
?>
		  <th><?php print_string(displayEnum($enrolstatus,'enrolstatus'),$book);?></th>
<?php
			}
?>
		  <th><?php print get_string('total',$book).' '.get_string('applied',$book);?></th>
		</tr>
<?php
	$d_year=mysql_query("SELECT * FROM yeargroup ORDER BY section_id, id");
	while($year=mysql_fetch_array($d_year,MYSQL_ASSOC)){
?>
		<tr>
		  <th>
<?php
		$values=array();
	    print $year['name'];
?>
		  </th>
<?php
		reset($rowcells);
		while(list($index,$enrolstatus)=each($rowcells)){ 
			if(!isset($totals[$index+1])){$totals[$index+1]=0;}
			$yid=$year['id'];
			if($enrolstatus=='EN'){$comtype='enquired';}
			elseif($enrolstatus=='AC'){$comtype='accepted';}
			else{$comtype='applied';}
			$com=array('id'=>'','type'=>$comtype, 
					   'name'=>$enrolstatus.':'.$yid,'year'=>$enrolyear);
			$comid=update_community($com);
			$com['id']=$comid;
			$values[$index+1]=countin_community($com);
			$values[0]+=$values[$index+1];
			$totals[$index+1]+=$values[$index+1];
?>
		  <td>
<?php
			print '<a href="admin.php?current=community_list.php&cancel='.
				 $choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&yid='. $yid.
				  '&comid='.$com['id'].'">' .$values[$index+1].'</a>';
?>
		  </td>
<?php
			}
?>
		  <td>
<?php
			print '<a href="admin.php?current=community_list.php&cancel='.
				 $choice.'&choice='. $choice.'&enrolyear='. 
			$enrolyear.'&yid='. $yid.'&comid=-1">' .$values[0].'</a>';
?>
		  </td>
		</tr>
<?php
		}
?>
		<tr>
		  <th>
			<?php print get_string('total',$book).' '.get_string('numberofstudents',$book);?>
		  </th>
<?php
		reset($rowcells);
		while(list($index,$enrolstatus)=each($rowcells)){ 
			$totals[0]+=$totals[$index+1];
?>
		  <td><?php print $totals[$index+1];?></td>
<?php
			}
?>
		  <td><?php print $totals[0];?></td>

		</tr>
	  </table>

	  <input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  </div>

