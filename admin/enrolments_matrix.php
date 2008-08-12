<?php
/**								  		enrolments_matrix.php
 *
 * This produces two tables, first aplications during current year
 * and then enrolments (and re-enrolments) for the actual roll.
 *
 */

$choice='enrolments_matrix.php';
$action='enrolments_matrix_action.php';

require_once('lib/curl_calls.php');

$currentyear=get_curriculumyear();
if(isset($_POST['enrolyear']) and $_POST['enrolyear']!=''){$enrolyear=$_POST['enrolyear'];}
else{$enrolyear=$currentyear;}

$extrabuttons=array();
twoplus_buttonmenu($enrolyear,$currentyear+3,$extrabuttons,$book);

$todate=date('Y-m-d');
$yearstartdate=$currentyear-1;
$enrol_tablerows=array();
$rowcells=array();
$rowcells=list_enrolmentsteps();
if($enrolyear==$currentyear){
	$reenrolsteps=array('reenroled','newenrolments',
						'currentroll','leaverssince','capacity','spaces');
	}
else{
	$reenrolsteps=array('reenroling','newenrolments','projectedroll',
						'currentroll','leavers','capacity','spaces');
	}
$yeargroups=list_yeargroups();
$yeargroup_names=array();


	$feeder_nos=array();
	$postdata['enrolyear']=$enrolyear;
	$postdata['currentyear']=$currentyear;
	while(list($findex,$feeder)=each($CFG->feeders)){
		$Transfers=feeder_fetch('transfer_nos',$feeder,$postdata);
		while(list($findex,$Transfer)=each($Transfers['transfer'])){
			if(!isset($feeder_nos[$Transfer['yeargroup']])){
				$feeder_nos[$Transfer['yeargroup']]=0;
				}
			$feeder_nos[$Transfer['yeargroup']]+=$Transfer['value'];
			}
		}

?>

  <div id="heading">
	<label><?php print_string('academicyear'); ?></label>
	<?php  print display_curriculumyear($enrolyear);?>
  </div>

  <div id="viewcontent" class="content">
 	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	  <table class="listmenu center smalltable">
		<caption><?php print_string('applications',$book);?></caption>
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
		$yid=$year['id'];
		$yearcom=array('id'=>'','type'=>'year', 
					   'name'=>$yid);
		$yearcomid=update_community($yearcom);
		$yeargroup_names[$yid]=$year['name'];
		$yeargroup_comids[$yid]=$yearcomid;
?>
		<tr>
		  <th>
<?php
		$values=array();
		$values[0]=0;
	    print $yeargroup_names[$yid];
?>
		  </th>
<?php
		reset($rowcells);
		while(list($index,$enrolstatus)=each($rowcells)){ 
			if(!isset($totals[$index+1])){$totals[$index+1]=0;}
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

		$newcurrentsids=0;
		if($enrolyear==$currentyear){
			/* Now count applicants who have joined the current roll and
			 * hence are not counted in one of the applied groups
			 */
			$d_nosids=mysql_query("SELECT COUNT(student_id) FROM
						comidsid WHERE community_id='$yearcomid'
					AND (leavingdate>'$todate' OR 
					leavingdate='0000-00-00' OR leavingdate IS NULL) 
					AND joiningdate<='$todate' AND joiningdate>='$yearstartdate-09-01';");
			$newcurrentsids=mysql_result($d_nosids,0);
			$values[0]+=$newcurrentsids;
			}
		$enrol_tablecells['C']['value']=$newcurrentsids;
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
	/* Reenrol */
	$reenrol_assdefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
	if(isset($reenrol_assdefs[0])){
		$reenrol_eid=$reenrol_assdefs[0]['id_db'];
		}
	else{
		$reenrol_eid=-1;
		}
	/* Reenroled */
	$reenroled_assdefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear-1);
	if(isset($reenrol_assdefs[0])){
		$reenroled_eid=$reenroled_assdefs[0]['id_db'];
		}
	else{
		$reenroled_eid=-1;
		}

	while(list($yearindex,$yeargroup)=each($yeargroups)){
		$yid=$yeargroup['id'];
		$rowcells=array();
		reset($reenrolsteps);
		$comid=$yeargroup_comids[$yid];
		$yearcommunity=get_community($comid);
		while(list($stepindex,$reenrolstep)=each($reenrolsteps)){
			$cell=array();
			$cell['value']=0;
			$cell['yid']=$yid;
			$cell['comid']=$comid;
			if($reenrolstep=='reenroling'){
				$cell['confirm']=count_reenrol_no($comid,$reenrol_eid,'C');
				$cell['repeat']=count_reenrol_no($comid,$reenrol_eid,'R');

				if(isset($tablerows[$yid-1])){
					$pre_reenrolcell=$tablerows[$yid-1][$reenrolstep];
					}
				else{
					$pre_reenrolcell=$cell;
					$pre_reenrolcell['confirm']=0;
					}
				$cell['value']=$pre_reenrolcell['confirm']+$cell['repeat'];	
				$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. 
							$enrolyear.'&yid='. $yid.
							'&comid='. $comid.'&enrolstage=RE">' 
							.$cell['value'].'</a>';
				}
			elseif($reenrolstep=='reenroled'){
				$cell['value']=count_reenrol_no($comid,$reenrol_eid,'C','R');
				}
			elseif($reenrolstep=='newenrolments'){
				if($enrolyear!=$currentyear){
					$cell['value']=0;
					/* Accepteds plus any transfers from feeders*/
					if(sizeof($feeder_nos)>0){
						if(isset($feeder_nos[$yid-1])){$cell['value']+=$feeder_nos[$yid-1];}
						}
					$cell['value']+=$enrol_tablerows[$yeargroup['name']]['AC']['value'];
					}
				else{
					/* Accepteds plus the new currents for this year*/
					$cell['value']=$enrol_tablerows[$yeargroup['name']]['C']['value']
										+$enrol_tablerows[$yeargroup['name']]['AC']['value'];
					}
				}
			elseif($reenrolstep=='currentroll'){
				$cell['value']=countin_community($yearcommunity);
				if($enrolyear==$currentyear){
					$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. 
							$enrolyear.'&comname='. $cell['yid'].'&comtype=year'.
							'&comid='. $cell['comid'].'&enrolstage=C">' 
							.$cell['value'].'</a>';
					}
				}
			elseif($reenrolstep=='projectedroll'){
					$cell['value']=$rowcells['newenrolments']['value'] + $rowcells['reenroling']['repeat'] + $pre_reenrolcell['confirm'];
				}
			elseif($reenrolstep=='leaverssince'){
				$leavercomid=update_community(array('id'=>'','type'=>'alumni','name'=>$yid,'year'=>$currentyear));
				$leavercom=get_community($leavercomid);
				$cell['value']=countin_community($leavercom);
				$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. 
							$enrolyear.'&comname='. $cell['yid'].'&comtype=alumni'.
							'&comid='. $leavercomid.'&enrolstage=C">' 
							.$cell['value'].'</a>';
				}
			elseif($reenrolstep=='leavers'){
				$cell['value']=count_reenrol_no($comid,$reenrol_eid,'L','LL');
				if(isset($tablerows[$yid-1])){
					$pre_leavercell=$tablerows[$yid-1][$reenrolstep];
					/*TODO: allow a shortcut to list just leavers*/
					//$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
					//		$choice.'&choice='. $choice.'&enrolyear='. 
					//		$enrolyear.'&yid='. $pre_leavercell['yid'].
					//		'&comid='. $pre_leavercell['comid'].'&enrolstage=RE">' 
					//		.$pre_leavercell['value'].'</a>';

				//$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
				// $choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&yid='. $cell['yid'].
				// '&comid='.$cell['comid'].'&enrolstage=RE">' .$cell['value'].'</a>';
					$cell['display']=$pre_leavercell['value'];
					}
				else{$cell['display']='';}
				}
			elseif($reenrolstep=='capacity'){
				if($enrolyear==$currentyear){
					/* capacity for the current year is in the year community */
					$cell['value']=$yearcommunity['capacity'];
					}
				else{
					/* to allow it to change year to year it is 
						stored in the accepted community */
					$accom=array('id'=>'','type'=>'accepted', 
					   'name'=>'AC:'.$yid,'year'=>$enrolyear);
					$accomid=update_community($accom);
					$accom=get_community($accomid);
					$cell['value']=$accom['capacity'];
					$cell['display']='<a href="admin.php?current=community_capacity.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. 
							$enrolyear.'&comid='. $accomid.'">' 
							.$cell['value'].'</a>';
					}
				}
			elseif($reenrolstep=='spaces'){
				if($enrolyear==$currentyear){
					$cell['value']=$rowcells['capacity']['value'] - $rowcells['currentroll']['value'];
					}
				elseif(isset($pre_reenrolcell)){
					$cell['value']=$rowcells['capacity']['value'] -	$rowcells['newenrolments']['value'] - $rowcells['reenroling']['repeat'] - $pre_reenrolcell['confirm'];
					}
				else{
					$cell['value']=$rowcells['capacity']['value'] - $rowcells['newenrolments']['value'];
					}
				}
			if(!isset($cell['display'])){$cell['display']=$cell['value'];}
			$rowcells[$reenrolstep]=$cell;
			}
	    $tablerows[$yid]=$rowcells;
		}
?>
	  <table class="listmenu center smalltable">
		<caption><?php print_string('enrolments',$book);?></caption>
		<tr>
		  <th><?php print display_curriculumyear($enrolyear);?></th>
<?php
		reset($reenrolsteps);
		while(list($stepindex,$reenrolstep)=each($reenrolsteps)){
?>
			<th><?php print_string($reenrolstep,$book);?></th>
<?php
			$total=0;
			reset($yeargroups);
			while(list($index,$yeargroup)=each($yeargroups)){
				$total+=$tablerows[$yeargroup['id']][$reenrolstep]['value'];
				}
			$column_totals[$stepindex]=$total;
			}
?>
		</tr>

<?php
	reset($tablerows);
	while(list($rowindex,$rowcells)=each($tablerows)){
?>
		<tr>
		<th><?php print $yeargroup_names[$rowindex];?></th>
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
		<tr>
		  <th>
			<?php print get_string('total',$book).' '.get_string('numberofstudents',$book);?>
		  </th>
<?php
		while(list($index,$total)=each($column_totals)){ 
?>
		  <td><?php print $total;?></td>
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
