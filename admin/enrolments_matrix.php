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

$enrol_tablerows=array();
$rowcells=array();
$rowcells=list_enrolmentsteps();
$reenrolsteps=array('reenrolled','newenrolments','currentroll','leavers','capacity','spaces');
$yeargroups=list_yeargroups();
?>
  <div id="viewcontent" class="content">
 	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	  <table class="listmenu center">
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
		  <th><?php print_string('applicationsreceived',$book);?></th>
		</tr>
<?php
	reset($yeargroups);
	while(list($index,$year)=each($yeargroups)){
		$enrol_tablecells=array();

?>
		<tr>
		  <th>
<?php
		$values=array();
		$values[0]=0;
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
			print '<a href="admin.php?current=enrolments_list.php&cancel='.
				 $choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&yid='. $yid.
				  '&comid='.$com['id'].'">' .$values[$index+1].'</a>';
?>
		  </td>
<?php
			$enrol_tablecells[$enrolstatus]['value']=$values[$index+1];
			}
?>
		  <td>
<?php
			print '<a href="admin.php?current=enrolments_list.php&cancel='.
				 $choice.'&choice='. $choice.'&enrolyear='. 
			$enrolyear.'&yid='. $yid.'&comid=-1">' .$values[0].'</a>';
?>
		  </td>
		</tr>
<?php
		$enrol_tablerows[$year['name']]=$enrol_tablecells;
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


<?php
	$tablerows=array();
	reset($yeargroups);
	$todate=date('Y-m-d');
	while(list($yearindex,$yeargroup)=each($yeargroups)){
		$rowcells=array();
		reset($reenrolsteps);
		$comid=update_community(array('id'=>'','type'=>'year','name'=>$yeargroup['id']));
		$yearcommunity=get_community($comid);
		while(list($stepindex,$reenrolstep)=each($reenrolsteps)){
			$cell=array();
			$cell['value']=0;
			$cell['yid']=$yeargroup['id'];
			$cell['comid']=$comid;
			if($reenrolstep=='reenrolled'){
				$reenrol_assdefs=fetch_enrolmentAssessmentDefinitions('','RE');
				$reenrol_eid=$reenrol_assdefs[0]['id_db'];
				$d_nosids=mysql_query("SELECT COUNT(eidsid.student_id) FROM
						eidsid JOIN comidsid ON
					eidsid.student_id=comidsid.student_id WHERE comidsid.community_id='$comid'
					AND (comidsid.leavingdate>'$todate' OR 
					comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
					AND (comidsid.joiningdate<='$todate' OR 
					comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL) 
					AND assessment_id='$reenrol_eid' AND result='C';");
				if(mysql_num_rows($d_nosids)>0){$cell['value']=mysql_result($d_nosids,0);}
				$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
				 $choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&yid='. $cell['yid'].
				  '&comid='.$cell['comid'].'&enrolstage=RE">' .$cell['value'].'</a>';
				}
			elseif($reenrolstep=='newenrolments'){
				$cell['value']=$enrol_tablerows[$yeargroup['name']]['AC']['value'];
				}
			elseif($reenrolstep=='currentroll'){
				if($enrolyear==2008){
					$cell['value']=countin_community($yearcommunity);
					}
				else{
					$cell['value']=$rowcells['newenrolments']['value'] + $rowcells['reenrolled']['value'];
					}
				}
			elseif($reenrolstep=='leavers'){
				//if(mysql_num_rows($d_nosids)>0){$cell['value']=mysql_result($d_nosids,0);}
				}
			elseif($reenrolstep=='capacity'){
				$cell['value']=$yearcommunity['capacity'];
				}
			elseif($reenrolstep=='spaces'){
				$cell['value']=$rowcells['capacity']['value'] - $rowcells['newenrolments']['value'] - $rowcells['reenrolled']['value'];
				}
			if(!isset($cell['display'])){$cell['display']=$cell['value'];}
			$rowcells[$reenrolstep]=$cell;
			}
	    $tablerows[$yeargroup['name']]=$rowcells;
		}
?>
	  <table class="listmenu center">
		<tr>
		  <th><?php print display_curriculumyear($enrolyear);?></th>
<?php
		reset($reenrolsteps);
		while(list($stepindex,$reenrolstep)=each($reenrolsteps)){
?>
			<th><?php print_string($reenrolstep,$book);?></th>
<?php
			}
?>
		</tr>

<?php
	reset($tablerows);
	while(list($rowindex,$rowcells)=each($tablerows)){
?>
		<tr>
		<th><?php print $rowindex;?></th>
<?php
		reset($rowcells);
		while(list($cellindex,$cell)=each($rowcells)){
?>
		  <td><?php print $cell['display'];?></td>
<?php
			}
?>

		</tr>
<?php
		}
?>

	  </table>

	  <input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  </div>
