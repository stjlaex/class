<?php
	/*****************************
	 *	This is the enrolments table.
	 *  The column headers are defined in enrolcols
	 */

    if($enrolyear==$currentyear){
		$enrolcols_value=array('reenroled','newenrolments','leaverssince',
							'currentroll','capacity','spaces');
		}
	else{
		$enrolcols_value=array('reenroling','transfersin','newenrolments','leavers',
							   'transfersout','projectedroll',
							   'budget','capacity','spaces');
		}
	$enrol_tablerows=array();
	$enrol_cols=array();
	while(list($colindex,$enrolcol)=each($enrolcols_value)){
		if($enrolcol=='capacity' or $enrolcol=='budget'){
			$enrolcols[$colindex]['class']='static';
			$enrolcols[$colindex]['display']='<a href="admin.php?current=enrolments_edit.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='.$enrolyear. 
							'&enrolstatus=capacity">'.get_string($enrolcol,$book).'</a>';
			}
		else{
			$enrolcols[$colindex]['display']=get_string($enrolcol,$book);
			if($enrolcol=='currentroll' or $enrolcol=='projectedroll'){
				$enrolcols[$colindex]['class']='other';
				}
			elseif($enrolcol=='spaces' or $enrolcol=='reenroled' or $enrolcol=='leavers'){
				$enrolcols[$colindex]['class']='blank';
				}
			else{
				$enrolcols[$colindex]['class']='live';
				}
			}
		$enrolcols[$colindex]['value']=$enrolcol;
		}

	/* For reenrolment status */
	$reenrol_assdefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
	if(isset($reenrol_assdefs[0])){
		$reenrol_eid=$reenrol_assdefs[0]['id_db'];
		}
	else{
		$reenrol_eid=-1;
		}
	/* And last years reenroled */
	$reenroled_assdefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear-1);
	if(isset($reenroled_assdefs[0])){
		$reenroled_eid=$reenroled_assdefs[0]['id_db'];
		}
	else{
		$reenroled_eid=-1;
		}

	reset($yeargroups);
	while(list($yearindex,$yeargroup)=each($yeargroups)){
		$yid=$yeargroup['id'];
		$enrol_tablecells=array();
		$comid=$yeargroup_comids[$yid];
		$yearcommunity=get_community($comid);
		reset($enrolcols_value);
		while(list($colindex,$enrolcol)=each($enrolcols_value)){
			$cell=array();
			$cell['value']=0;
			$cell['yid']=$yid;
			$cell['comid']=$comid;
			if($enrolcol=='reenroling'){
				$cell['confirm']=count_reenrol_no($comid,$reenrol_eid,'C');
				$cell['repeat']=count_reenrol_no($comid,$reenrol_eid,'R');

				if(isset($enrol_tablerows[$yid-1])){
					$pre_reenrolcell=$enrol_tablerows[$yid-1][$enrolcol];
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
			elseif($enrolcol=='reenroled'){
				$cell['value']=$reenroled_eid.' '.count_reenrol_no($comid,$reenroled_eid,'C','R');
				}
			elseif($enrolcol=='newenrolments'){
				if($enrolyear!=$currentyear){
					$cell['value']=0;
					/* Accepteds plus any transfers from feeders*/
					if(sizeof($feeder_nos)>0){
						if(isset($feeder_nos[$yid-1])){$cell['value']+=$feeder_nos[$yid-1];}
						}
					$cell['value']+=$app_tablerows[$yid]['AC']['value'];
					}
				else{
					/* Accepteds plus the new currents for this year*/
					$cell['value']=$app_tablerows[$yid]['AC']['value']
							+ $app_tablerows[$yid]['C']['value'];
					/*TODO
					$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
						$choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&yid='. $yid.
						'&comid=-1">'.$cell['value'].'</a>';
					*/
					}
				}
			elseif($enrolcol=='currentroll'){
				$cell['value']=countin_community($yearcommunity);
				if($enrolyear==$currentyear){
					$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. 
							$enrolyear.'.&comid='. $cell['comid'].'&enrolstage=C">' 
							.$cell['value'].'</a>';
					}
				}
			elseif($enrolcol=='projectedroll'){
					$cell['value']=$enrol_tablecells['newenrolments']['value'] + $enrol_tablecells['reenroling']['repeat'] + $pre_reenrolcell['confirm'];
				}
			elseif($enrolcol=='leaverssince'){
				$leavercomid=update_community(array('id'=>'','type'=>'alumni','name'=>$yid,'year'=>$currentyear));
				$leavercom=get_community($leavercomid);
				$cell['value']=countin_community($leavercom);
				$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. 
							$enrolyear.'&comname='. $cell['yid'].'&comtype=alumni'.
							'&comid='. $leavercomid.'&enrolstage=C">' 
							.$cell['value'].'</a>';
				}
			elseif($enrolcol=='leavers'){
				$cell['value']=count_reenrol_no($comid,$reenrol_eid,'L','LL');
				if(isset($enrol_tablerows[$yid-1])){
					$pre_leavercell=$enrol_tablerows[$yid-1][$enrolcol];
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
			elseif($enrolcol=='spaces'){
				if($enrolyear==$currentyear){
					$cell['value']=$enrol_tablecells['capacity']['value'] - $enrol_tablecells['currentroll']['value'] - $app_tablerows[$yid]['AC']['value'];
					}
				elseif(isset($pre_reenrolcell)){
					$cell['value']=$enrol_tablecells['capacity']['value'] -	$enrol_tablecells['newenrolments']['value'] - $enrol_tablecells['reenroling']['repeat'] - $pre_reenrolcell['confirm'];
					}
				else{
					$cell['value']=$enrol_tablecells['capacity']['value'] - $enrol_tablecells['newenrolments']['value'];
					}
				if($cell['value']<0){$cell['value']=0;}
				}
			if(!isset($cell['display'])){$cell['display']=$cell['value'];}
			$enrol_tablecells[$enrolcol]=$cell;
			}
	    $enrol_tablerows[$yid]=$enrol_tablecells;
		}
?>
