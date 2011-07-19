<?php
	/*****************************
	 *	This is the enrolments table.
	 *  The column headers are defined in enrolcols
	 */

    if($enrolyear>$currentyear){
		$enrolcols_value=array('reenroling','pending','transfersin','transfersout','newenrolments',
							   'projectedroll','budgetroll','targetroll','leavers','capacity','spaces');
		$enrolcols_display=array('reenroling','pending','transfersin','transfersout','newenrolments',
							   'projectedroll','budgetroll','targetroll','leavers','capacity','spaces');
		}
	else{
		/*		$enrolcols_value=array('reenroled','newenrolments','leaverssince',,'leaversprevious',
							'currentroll','budgetroll','capacity','spaces');
		*/
		$enrolcols_value=array('reenroled','newenrolmentsprevious','newnewenrolments','leaverssince',
							'currentroll','budgetroll','leaverstotal','notinvoiced','capacity','spaces');
		$enrolcols_display=array('reenroled','newenrolmentsprevious','newnewenrolments','leaverssince',
							'currentroll','budgetroll','leaverstotal','notinvoiced','capacity','spaces');
		}

	$enrol_tablerows=array();
	$enrol_cols=array();
	foreach($enrolcols_value as $colindex => $enrolcol){
	  if(in_array($enrolcol,$enrolcols_display)){
		if($enrolcol=='capacity' or $enrolcol=='budgetroll' or $enrolcol=='targetroll'){
			$enrolcols[$colindex]['class']='static';
			$enrolcols[$colindex]['display']='<a href="admin.php?current=enrolments_edit.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='.$enrolyear. 
				'&enrolstatus='.$enrolcol.'">'.get_string($enrolcol,$book).'</a>';
			if($enrolcol=='targetroll'){
				$disyear=$enrolyear-1;
				$enrolcols[$colindex]['display'].='<br />('. display_date($targetdate) .')';
				}
			}
		else{
			$enrolcols[$colindex]['display']=get_string($enrolcol,$book);
			if($enrolcol=='currentroll' or $enrolcol=='projectedroll'  or $enrolcol=='spaces'){
				$enrolcols[$colindex]['class']='other';
				if($enrolcol=='projectedroll'){
					$enrolcols[$colindex]['display'].='<br />('. display_date($todate).')';
					}
				}
			elseif($enrolcol=='leavers' or $enrolcol=='leaversprevious' 
									or $enrolcol=='leaverssince' or $enrolcol=='transfersout' or $enrolcol=='leaverstotal'){
				$enrolcols[$colindex]['class']='blank';
				}
			else{
				$enrolcols[$colindex]['class']='live';
				}
			}
		$enrolcols[$colindex]['value']=$enrolcol;
		  }
		}

	/* For reenrolment status */
	$reenrol_assdefs=(array)fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
	if(isset($reenrol_assdefs[0])){
		$reenrol_eid=$reenrol_assdefs[0]['id_db'];
		$pairs=explode (';', $reenrol_assdefs[0]['GradingScheme']['grades']);
		$transfer_codes=array();
		foreach($pairs as $pair){
			list($grade['result'], $grade['value'])=explode(':',$pair);
			if($grade['result']!='C' and $grade['result']!='L' and $grade['result']!='P' 
				and $grade['result']!='LL' and $grade['result']!='R'){
				/* Need just the reenrolment codes used for transfering to other schools. */
				$transfer_codes[]=$grade;
				}
			}
		}
	else{
		$reenrol_eid=-1;
		}




	foreach($yeargroups as $yidindex => $yeargroup){
		$yid=$yeargroup['id'];
		$previous_yid=$yid-1;
		$enrol_tablecells=array();
		$comid=$yeargroup_comids[$yid];
		if($comid!==''){
			$yearcommunity=get_community($comid);
			}
		else{
			$comid=-1000;
			}

		foreach($enrolcols_value as $colindex => $enrolcol){
			$cell=array();
			$cell['value']=0;
			$cell['yid']=$yid;
			$cell['comid']=$comid;
			$cell['name']=$enrolcol.':'.$yid;
			$cell['name_boarder']=$enrolcol.':boarder';


			/* Each enrol column has its own calculation */
			if($enrolcol=='reenroling'){
				$cell['confirm']=count_reenrol_no($comid,$reenrol_eid,'C','');
				$cell['repeat']=count_reenrol_no($comid,$reenrol_eid,'R','');
				$cell['pending']=count_reenrol_no($comid,$reenrol_eid,'P','');
				$cell['pending']+=count_reenrol_no($comid,$reenrol_eid,'','');
				/* Have to count anyone removed from the roll after
				 * the cutoffdate as not reenrolling (leavers in September) whatever their
				 * reenroll status might be.
				 */
				$leavercomid=update_community(array('id'=>'','type'=>'alumni','name'=>'P:'.$yid,'year'=>$currentyear));
				$leavercom=get_community($leavercomid);
				$cutoffleavers=listin_community_new($leavercom,$cutoffdate);
				$cell['leavers']=sizeof($cutoffleavers)+count_reenrol_no($comid,$reenrol_eid,'L','LL');

				$cell['transfersout']=0;
				foreach($transfer_codes as $transfer_code){
					$cell['transfersout']+=count_reenrol_no($comid,$reenrol_eid,$transfer_code['result']);
					}

				if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes' and $yidindex==1){
					$rescoms=(array)list_communities('accomodation');
					foreach($rescoms as $rescom){
						$cell['confirm_boarder']+=count_reenrol_no($rescom['id'],$reenrol_eid,'C','');
						$cell['repeat_boarder']+=count_reenrol_no($rescom['id'],$reenrol_eid,'R','');
						$cell['pending_boarder']+=count_reenrol_no($rescom['id'],$reenrol_eid,'P','');
						$cell['leavers_boarder']+=count_reenrol_no($rescom['id'],$reenrol_eid,'L','LL');
						}
					}

				/* Carry forward the reenrollment numbers form the previous year group into pre_reenrolcell. */
				if(isset($enrol_tablerows[$previous_yid])){
					$pre_reenrolcell=$enrol_tablerows[$previous_yid][$enrolcol];
					}
				else{
					$pre_reenrolcell=$cell;
					$pre_reenrolcell['confirm']=0;
					}
				$cell['value']=$pre_reenrolcell['confirm']+$cell['repeat'];	
				if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
					$cell['value_boarder']=$pre_reenrolcell['confirm_boarder']+$cell['repeat_boarder'];	
					}
				$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. 
							$enrolyear.'&yid='. $yid. '&comid='. $comid.'&enrolstage=RE">' 
							.$cell['value'].'</a>';
				}
			elseif($enrolcol=='pending'){
				if(isset($enrol_tablerows[$previous_yid])){
					$cell['value']=$enrol_tablerows[$previous_yid]['reenroling']['pending'];	
					if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
						$cell['value_boarder']=$enrol_tablerows[$previous_yid]['reenroling']['pending_boarder'];	
						}
					}
				else{
					$cell['value']=0;
					}
				}
			elseif($enrolcol=='reenroled'){
				/*Confirmed, Pending and Repeats would all have been enrolled*/
				$cell['value']=count_reenrol_no($comid,$reenrol_eid,'P','C');
				$cell['value']+=count_reenrol_no($comid,$reenrol_eid,'R');

				/* An extreme measure to catch any who had no reenrolment status set when the database moved forward. */
				$d_noc=mysql_query("SELECT COUNT(student_id) FROM comidsid WHERE comidsid.community_id='$comid'
					AND (leavingdate>'$todate' OR leavingdate='0000-00-00' OR leavingdate IS NULL)
					AND joiningdate<='$yearenddate'
					AND NOT EXISTS(SELECT student_id FROM eidsid WHERE eidsid.assessment_id='$reenrol_eid' 
					AND eidsid.student_id=comidsid.student_id); 
					");
				if(mysql_num_rows($d_noc)>0){
					$no=mysql_result($d_noc,0);
					}
				else{
					$no=0;
					}
				$cell['value']+=$no;
				}
			elseif($enrolcol=='leaversprevious'){
				/* Students who left beofre the start of the new academic year. */
				$leavercomid=update_community(array('id'=>'','type'=>'alumni','name'=>'P:'.$yid,'year'=>$yearstart));
				$leavercom=get_community($leavercomid);
				$cell['value']=countin_community($leavercom);
				//$cell['value']=count_reenrol_no($leavercomid,$reenrol_eid,'LL','L');
				}
			elseif($enrolcol=='leaverstotal'){
				/* Total number of students who did not re-enrol from last year 
				 * plus any who have left since ie.during the current year. 
				 */
				$leavercomid=update_community(array('id'=>'','type'=>'alumni','name'=>'P:'.$yid,'year'=>$yearstart));
				$leavercom=get_community($leavercomid);
				//$cell['leaverslast']=countin_community($leavercom);
				$cell['leaverslast']=count_reenrol_no($leavercomid,$reenrol_eid,'LL','L');
				if(isset($enrol_tablerows[$previous_yid]['leaverstotal']['leaverslast'])){
					$cell['value']=$enrol_tablerows[$previous_yid]['leaverstotal']['leaverslast']+$enrol_tablecells['leaverssince']['value'];
					}
				else{
					$cell['value']=$enrol_tablecells['leaverssince']['value'];
					}
				}
			elseif($enrolcol=='transfersin'){
				$cell['value']=0;
				/* Accepteds plus any transfers from feeders*/
				if(sizeof($feeder_nos)>0){
					if(isset($feeder_nos[$previous_yid])){$cell['value']+=$feeder_nos[$previous_yid];}
					}
				}
			elseif($enrolcol=='transfersout'){
				$cell['value']=0;
				/* Accepteds plus any transfers from feeders*/
				if(isset($enrol_tablerows[$previous_yid])){$cell['value']+=$pre_reenrolcell['transfersout'];}
				}
			elseif($enrolcol=='newnewenrolments'){
				/* The new students who have joined the roll since the start of the year*/
				$cell['value']=$app_tablerows[$yid]['newnewenrolments']['value'];
				}
			elseif($enrolcol=='newenrolments'){
				$cell['value']+=$app_tablerows[$yid]['AC']['value'];
				if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
					$cell['value_boarder']+=$app_tablerows[$yid]['AC']['value_boarder'];
					}
				}
			elseif($enrolcol=='currentroll'){
				$cell['value']=countin_community($yearcommunity);
				if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
					$cell['value_boarder']=0;
					if($cell['value']>0){
						$cell['value_boarder']=countin_community_extra($yearcommunity,'boarder','B');
						}
					}

				if($enrolyear==$currentyear){
					$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. 
							$enrolyear.'&comid='. $cell['comid'].'&enrolstage=C">' 
							.$cell['value'].'</a>';
					}
				}
			elseif($enrolcol=='projectedroll'){

				$cell['value']=$enrol_tablecells['newenrolments']['value'] + $enrol_tablecells['transfersin']['value'] + $enrol_tablecells['reenroling']['repeat'] + $pre_reenrolcell['confirm'];
				if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
					$cell['value_boarder']=$enrol_tablecells['newenrolments']['value_boarder'] + $enrol_tablecells['reenroling_boarder']['repeat'] + $pre_reenrolcell['confirm_boarder'];
					}
				}
			elseif($enrolcol=='leaverssince'){
				$leavercomid=update_community(array('id'=>'','type'=>'alumni','name'=>'P:'.$yid,'year'=>$currentyear));
				$leavercom=get_community($leavercomid);
				$cell['value']=countin_community($leavercom);
				$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. 
							$enrolyear.'&comname='. $cell['yid'].'&comtype=alumni'.
							'&comid='. $leavercomid.'&enrolstage=C">' 
							.$cell['value'].'</a>';
				/* Assume any leavers where also reenroled last year before leaving an add to that total. */
				$enrol_tablecells['reenroled']['value']+=$cell['value'];
				$enrol_tablecells['reenroled']['display']=$enrol_tablecells['reenroled']['value'];
				}
			elseif($enrolcol=='leavers'){
				if(isset($enrol_tablerows[$previous_yid])){
					$cell['value']=$enrol_tablerows[$previous_yid]['reenroling']['leavers'];
					if($CFG->enrol_boarders=='yes'){	
						$cell['value_boarder']=$enrol_tablerows[$previous_yid]['reenroling']['leavers_boarder'];
						}
					}
				else{
					$cell['value']=0;
					$cell['value_boarder']=0;
					}
				}
			elseif($enrolcol=='notinvoiced'){
				$cell['value']=$app_tablerows[$yid]['AC']['value'];
				}
			elseif($enrolcol=='capacity'){
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
					}
				}
			elseif($enrolcol=='budgetroll' or $enrolcol=='targetroll'){
				$budcom=array('id'=>'','type'=>'applied', 
					   'name'=>$enrolcol.':'.$yid,'year'=>$enrolyear);
				$budcom['id']=update_community($budcom);
				$cell['value']=countin_community($budcom,'','',true);
				}
			elseif($enrolcol=='spaces'){
				if($enrolyear==$currentyear){
					$cell['value']=$enrol_tablecells['capacity']['value'] - $enrol_tablecells['currentroll']['value'] - $app_tablerows[$yid]['AC']['value'];
					}
				elseif(isset($pre_reenrolcell)){
					$cell['value']=$enrol_tablecells['capacity']['value'] -	$enrol_tablecells['transfersin']['value'] - $enrol_tablecells['newenrolments']['value'] - $enrol_tablecells['pending']['value'] - $enrol_tablecells['reenroling']['repeat'] - $pre_reenrolcell['confirm'];
					}
				else{
					$cell['value']=$enrol_tablecells['capacity']['value'] - $enrol_tablecells['newenrolments']['value'];
					}
				/* Don't display negative spaces. */
				if($cell['value']<0){$cell['value']=0;}
				}

			if(!isset($cell['display']) and in_array($enrolcol,$enrolcols_display)){$cell['display']=$cell['value'];}
			$enrol_tablecells[$enrolcol]=$cell;
			}

		if($enrolyear<=$currentyear){
			/* This should be a quick estimate of the new enrolments last year. */
			$enrol_tablecells['newenrolmentsprevious']['value']=$enrol_tablecells['currentroll']['value'] - $enrol_tablecells['reenroled']['value'] - $enrol_tablecells['newnewenrolments']['value'] + $enrol_tablecells['leaverssince']['value'];
			$enrol_tablecells['newenrolmentsprevious']['display']=$enrol_tablecells['newenrolmentsprevious']['value'];
			}

	    $enrol_tablerows[$yid]=$enrol_tablecells;
		}
?>
