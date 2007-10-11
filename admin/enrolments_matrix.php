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

$rowcells=array('EN','AP','AT','ATD','RE','ACP','CA','WL','AC');
?>
  <div id="viewcontent" class="content">
 	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	  <table class="listmenu">
		<tr>
		  <th><?php print display_curriculumyear($enrolyear);?></th>
<?php
		  while(list($index,$enrolstatus)=each($rowcells)){ 
			  $totals[$index]=0;
?>
		  <th><?php print_string(displayEnum($enrolstatus,'enrolstatus'),$book);?></th>
<?php
			}
?>
		</tr>
<?php
	$totals=array();
	$d_year=mysql_query("SELECT * FROM yeargroup ORDER BY section_id, id");
	while($year=mysql_fetch_array($d_year,MYSQL_ASSOC)){
?>
		<tr>
		  <th>
<?php
		$nocol=0;
	    print $year['name'];
?>
		  </th>
<?php
		$values=array();
		reset($rowcells);
		while(list($index,$enrolstatus)=each($rowcells)){ 
			if(!isset($totals[$index])){$totals[$index]=0;}
			$yid=$year['id'];
			if($enrolstatus=='EN'){$comtype='enquired';}
			elseif($enrolstatus=='AC'){$comtype='accepted';}
			else{$comtype='applied';}
			$com=array('id'=>'','type'=>$comtype, 
					   'name'=>$enrolstatus.':'.$yid,'year'=>$enrolyear);
			$comid=update_community($com);
			$com['id']=$comid;
			$values[$index]=countin_community($com);
			$totals[$index]+=$values[$index];
?>
		  <td>
<?php
			print '<a href="admin.php?current=community_list.php&cancel='.
				 $choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&type='.$comtype.
				  '&comid='.$com['id'].'">' .$values[$nocol++].'</a>';
?>
		  </td>
<?php
			}
?>
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
?>
		  <td><?php print $totals[$index];?></td>
<?php
			}
?>

		</tr>
	  </table>

	  <input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  </div>

