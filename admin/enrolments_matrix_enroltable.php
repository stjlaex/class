<?php
	/*****************************
	 *	This is the enrolments table.
	 *  The column headers are defined in enrolcols
	 */

    if($enrolyear>$currentyear){
		$enrolcols_value=array('reenroling','pending','transfersin','newenrolments','leavers',
							   'projectedroll','targetroll','budgetroll','capacity','spaces');
		}
	else{
		/*		$enrolcols_value=array('reenroled','newenrolments','leaverssince',
							'currentroll','budgetroll','capacity','spaces');
		*/
		$enrolcols_value=array('reenroled','newenrolments','leaverssince',
							'currentroll','budgetroll','leaversprevious','capacity','spaces');
		}

	$enrol_tablerows=array();
	$enrol_cols=array();
	foreach($enrolcols_value as $colindex => $enrolcol){
		if($enrolcol=='capacity' or $enrolcol=='budgetroll' or $enrolcol=='targetroll'){
			$enrolcols[$colindex]['class']='static';
			$enrolcols[$colindex]['display']='<a href="admin.php?current=enrolments_edit.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='.$enrolyear. 
				'&enrolstatus='.$enrolcol.'">'.get_string($enrolcol,$book).'</a>';
			if($enrolcol=='targetroll'){
				$disyear=$enrolyear-1;
				$enrolcols[$colindex]['display'].='<br />('. 
					display_date($disyear. '-'.$CFG->enrol_cutoffmonth.'-30').')';
				}
			}
		else{
			$enrolcols[$colindex]['display']=get_string($enrolcol,$book);
			if($enrolcol=='currentroll' or $enrolcol=='projectedroll'){
				$enrolcols[$colindex]['class']='other';
				if($enrolcol=='projectedroll'){
					$enrolcols[$colindex]['display'].='<br />('. display_date($todate).')';
					}
				}
			elseif($enrolcol=='spaces' or $enrolcol=='reenroled' or $enrolcol=='leavers' or $enrolcol=='leaversprevious'){
				$enrolcols[$colindex]['class']='blank';
				}
			else{
				$enrolcols[$colindex]['class']='live';
				}
			}
		$enrolcols[$colindex]['value']=$enrolcol;
		}

	/* For reenrolment status */
	$reenrol_assdefs=(array)fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
	if(isset($reenrol_assdefs[0])){
		$reenrol_eid=$reenrol_assdefs[0]['id_db'];
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
				$cell['confirm']=count_reenrol_no($comid,$reenrol_eid,'C');
				$cell['repeat']=count_reenrol_no($comid,$reenrol_eid,'R');
				$cell['pending']=count_reenrol_no($comid,$reenrol_eid,'P');
				$cell['leavers']=count_reenrol_no($comid,$reenrol_eid,'L','LL');

				if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes' and $yidindex==1){
					$rescoms=(array)list_communities('accomodation');
					foreach($rescoms as $rescom){
						$cell['confirm_boarder']+=count_reenrol_no($rescom['id'],$reenrol_eid,'C');
						$cell['repeat_boarder']+=count_reenrol_no($rescom['id'],$reenrol_eid,'R');
						$cell['pending_boarder']+=count_reenrol_no($rescom['id'],$reenrol_eid,'P');
						$cell['leavers_boarder']+=count_reenrol_no($rescom['id'],$reenrol_eid,'L','LL');
						}
					}

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
							$enrolyear.'&yid='. $yid.
							'&comid='. $comid.'&enrolstage=RE">' 
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
				$cell['value']=count_reenrol_no($comid,$reenrol_eid,'R','C');
				}
			elseif($enrolcol=='leaversprevious'){
				$leavercomid=update_community(array('id'=>'','type'=>'alumni','name'=>'P:'.$yid,'year'=>$yearstart));
				$leavercom=get_community($leavercomid);
				//$cell['value']=count_reenrol_no($leavercomid,$reenrol_eid,'LL','L');
				$cell['value']=countin_community($leavercom);
				}
			elseif($enrolcol=='transfersin'){
				$cell['value']=0;
				/* Accepteds plus any transfers from feeders*/
				if(sizeof($feeder_nos)>0){
					if(isset($feeder_nos[$previous_yid])){$cell['value']+=$feeder_nos[$previous_yid];}
					}
				}
			elseif($enrolcol=='newenrolments'){
				if($enrolyear!=$currentyear){
					$cell['value']+=$app_tablerows[$yid]['AC']['value'];
					$cell['value_boarder']+=$app_tablerows[$yid]['AC']['value_boarder'];
					}
				else{
					/* The new students who have joined the roll 
					   since the start of the year*/
					$cell['value']=$app_tablerows[$yid]['C']['value'];
					$cell['value_boarder']=$app_tablerows[$yid]['C']['value_boarder'];
					/*TODO: list new students including transfers from other schools
					$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
						$choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&yid='. $yid.
						'&comid=-1">'.$cell['value'].'</a>';
					*/
					}
				}
			elseif($enrolcol=='currentroll'){
				$cell['value']=countin_community($yearcommunity);
				$cell['value_boarder']=0;
				if($cell['value']>0){
					$cell['value_boarder']=countin_community_extra($yearcommunity,'boarder','B');
					}

				if($enrolyear==$currentyear){
					$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. 
							$enrolyear.'&comid='. $cell['comid'].'&enrolstage=C">' 
							.$cell['value'].'</a>';
					}
				}
			elseif($enrolcol=='projectedroll'){
				$cell['value']=$enrol_tablecells['newenrolments']['value'] + $enrol_tablecells['transfersin']['value'] + $enrol_tablecells['reenroling']['repeat'] + $enrol_tablecells['pending']['value'] + $pre_reenrolcell['confirm'];
				$cell['value_boarder']=$enrol_tablecells['newenrolments']['value_boarder'] + $enrol_tablecells['reenroling_boarder']['repeat'] + $enrol_tablecells['pending']['value_boarder'] + $pre_reenrolcell['confirm_boarder'];
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

				}
			elseif($enrolcol=='leavers'){
				if(isset($enrol_tablerows[$previous_yid])){
					$cell['value']=$enrol_tablerows[$previous_yid]['reenroling']['leavers'];	
					$cell['value_boarder']=$enrol_tablerows[$previous_yid]['reenroling']['leavers_boarder'];	
					}
				else{
					$cell['value']=0;
					$cell['value_boarder']=0;
					}
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

			if(!isset($cell['display'])){$cell['display']=$cell['value'];}
			$enrol_tablecells[$enrolcol]=$cell;
			}

	    $enrol_tablerows[$yid]=$enrol_tablecells;
		}
?>
