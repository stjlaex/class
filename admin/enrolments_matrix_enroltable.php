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
		/* If no reenrol assessment exists then create one for this enrolyear. */
		$d_g=mysql_query("SELECT grades FROM grading WHERE name='Re-enrolmentStatus';");
		if(mysql_num_rows($d_g)==0){
			mysql_query("INSERT INTO grading (name,grades,comment,author) VALUES ('Re-enrolmentStatus','C:0;P:1;L:2;R:7','Student re-enrolment for the next year','ClaSS')");
			}
		mysql_query("INSERT INTO assessment (subject_id,stage,description,grading_name,course_id,year,season) VALUES ('G','RE','Re-enrolment','Re-enrolmentStatus','%','$enrolyear','S')");
		$reenrol_eid=mysql_insert_id();
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

			foreach($boardercoms as $index=>$boardercom){
				$cell['name_boarders'][$index]=$enrolcol.':boarder';
				}

			/* Each enrol column has its own calculation */
			if($enrolcol=='reenroling'){
				$cell['confirm']=count_reenrol_no($comid,$reenrol_eid,'C','');
				$cell['repeat']=count_reenrol_no($comid,$reenrol_eid,'R','');
				$cell['pending']=count_reenrol_no($comid,$reenrol_eid,'P','');
				$cell['pending']+=count_reenrol_no($comid,$reenrol_eid,'','');
				/* Have to count anyone removed from the roll after
				 * the cutoffdate as not reenroling (leavers in September) whatever their
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

				if(isset($boardercoms) and $yidindex==1){
					foreach($boardercoms as $index => $boardercom){
						$cell['confirm_boarders'][$index]+=count_reenrol_no($boardercom['id'],$reenrol_eid,'C','');
						$cell['repeat_boarders'][$index]+=count_reenrol_no($boardercom['id'],$reenrol_eid,'R','');
						$cell['pending_boarders'][$index]+=count_reenrol_no($boardercom['id'],$reenrol_eid,'P','');
						$cell['leavers_boarders'][$index]+=count_reenrol_no($boardercom['id'],$reenrol_eid,'L','LL');
						}
					}

				/* Carry forward the reenrollment numbers form the previous year group into pre_reenrolcell. */
				if(isset($enrol_tablerows[$previous_yid])){
					$pre_reenrolcell=$enrol_tablerows[$previous_yid][$enrolcol];
					}
				else{
					$pre_reenrolcell=$cell;
					$pre_reenrolcell['confirm']=0;
					foreach($boardercoms as $index => $boardercom){
						$pre_reenrolcell['confirm_boarders'][$index]=0;
						}
					}
				$cell['value']=$pre_reenrolcell['confirm']+$cell['repeat'];	
				//$cell['value_boarder']=0;
				if(isset($boardercoms)){
					foreach($boardercoms as $index => $boardercom){
						$cell['value_boarders'][$index]=$pre_reenrolcell['confirm_boarders'][$index]+$cell['repeat_boarders'][$index];
						}
					}
				$cell['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. 
							$enrolyear.'&yid='. $yid. '&comid='. $comid.'&enrolstage=RE">' 
							.$cell['value'].'</a>';
				}
			elseif($enrolcol=='pending'){
				if(isset($enrol_tablerows[$previous_yid])){
					$cell['value']=$enrol_tablerows[$previous_yid]['reenroling']['pending'];	
					if(isset($boardercoms)){
						foreach($boardercoms as $index => $boardercom){
							$cell['value_boarders'][$index]=$enrol_tablerows[$previous_yid]['reenroling']['pending_boarders'][$index];
							}
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
				/**
				 * TODO: Doesn't work because boarders are permanent installed as boarders....
				 *
				foreach($boardercoms as $index => $boardercom){
					$cell['value_boarders'][$index]=count_reenrol_no($boardercom['id'],$reenrol_eid,'P','C');
					$cell['value_boarders'][$index]+=count_reenrol_no($boardercom['id'],$reenrol_eid,'R');
					}
				*/
				}
			elseif($enrolcol=='leaversprevious'){
				/* Students who left beofre the start of the new academic year. */
				$leavercomid=update_community(array('id'=>'','type'=>'alumni','name'=>'P:'.$yid,'year'=>$yearstart));
				$leavercom=get_community($leavercomid);
				$cell['value']=countin_community($leavercom);
				//$cell['value']=count_reenrol_no($leavercomid,$reenrol_eid,'LL','L');
				foreach($boardercoms as $index => $boardercom){
					$cell['value_boarders'][$index]=countin_community_extra($leavercom,'boarder',$boardercom['name']);
					}
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
					if(isset($boardercoms)){
						foreach($boardercoms as $index => $boardercom){
							//$cell['value_boarders'][$index]+=$enrol_tablecells[$previous_yid]['leaverstotal_boarders'][$index]['leaverslast'] + $enrol_tablecells['leaverssince']['value_boarders'][$index];
							}
						}
					}
				else{
					$cell['value']=$enrol_tablecells['leaverssince']['value'];
					if(isset($boardercoms)){
						foreach($boardercoms as $index => $boardercom){
							$cell['value_boarders'][$index]+=$enrol_tablecells[$yid]['leaverssince']['value_boarders'][$index];
							}
						}
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
				if(isset($boardercoms)){
					foreach($boardercoms as $index => $boardercom){
						$cell['value_boarders'][$index]+=$app_tablerows[$yid]['newnewenrolments']['value_boarders'][$index];
						}
					}
				}
			elseif($enrolcol=='newenrolments'){
				$cell['value']+=$app_tablerows[$yid]['AC']['value'];
				if(isset($boardercoms)){
					foreach($boardercoms as $index => $boardercom){
						$cell['value_boarders'][$index]+=$app_tablerows[$yid]['AC']['value_boarders'][$index];
						}
					}
				}
			elseif($enrolcol=='currentroll'){
				$cell['value']=countin_community($yearcommunity);
				if(isset($boardercoms)){
					foreach($boardercoms as $index => $boardercom){
						$cell['value_boarders'][$index]=0;
						if($cell['value']>0){
							$cell['value_boarders'][$index]=countin_community_extra($yearcommunity,'boarder',$boardercom['name']);
							}
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
				if(isset($boardercoms)){
					foreach($boardercoms as $index => $boardercom){
						$cell['value_boarders'][$index]=$enrol_tablecells['newenrolments']['value_boarders'][$index] + $enrol_tablecells['reenroling_boarders'][$index]['repeat'] + $pre_reenrolcell['confirm_boarders'][$index];
						}
					}
				}
			elseif($enrolcol=='leaverssince'){
				$leavercomid=update_community(array('id'=>'','type'=>'alumni','name'=>'P:'.$yid,'year'=>$currentyear));
				$leavercom=get_community($leavercomid);
				$cell['value']=countin_community($leavercom);
				if(isset($boardercoms)){
					foreach($boardercoms as $index => $boardercom){
						$cell['value_boarders'][$index]=countin_community_extra($leavercom,'boarder',$boardercom['name']);
						}
					}

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
					if(isset($boardercoms)){	
						foreach($boardercoms as $index => $boardercom){
							$cell['value_boarders'][$index]=$enrol_tablerows[$previous_yid]['reenroling']['leavers_boarders'][$index];
							}
						}
					}
				else{
					$cell['value']=0;
					foreach($boardercoms as $index => $boardercom){
						$cell['value_boarders'][$index]=0;
						}
					}
				}
			elseif($enrolcol=='notinvoiced'){
				$cell['value']=$app_tablerows[$yid]['AC']['value'];
				}
			elseif($enrolcol=='capacity'){
				if($enrolyear==$currentyear){
					/* capacity for the current year is in the year community */
					$cell['value']=$yearcommunity['capacity'];
					if(isset($boardercoms) and $yidindex==1){
						/* Only used in a total so count once. */	
						foreach($boardercoms as $index => $boardercom){
							$cell['value_boarders'][$index]=$boardercom['capacity'];
							}
						}
					}
				else{
					/* to allow it to change year to year it is 
						stored in the accepted community */
					$accom=array('id'=>'','type'=>'accepted', 
					   'name'=>'AC:'.$yid,'year'=>$enrolyear);
					$accomid=update_community($accom);
					$accom=get_community($accomid);
					$cell['value']=$accom['capacity'];
					/* Assume the capacity is fixed for boarders year-on-year as its easier...*/
					if(isset($boardercoms) and $yidindex==1){	
						foreach($boardercoms as $index => $boardercom){
							$cell['value_boarders'][$index]=$boardercom['capacity'];
							}
						}
					}
				}
			elseif($enrolcol=='budgetroll' or $enrolcol=='targetroll'){
				$budcom=array('id'=>'','type'=>'applied','name'=>$enrolcol.':'.$yid,'year'=>$enrolyear);
				$budcom['id']=update_community($budcom);
				$cell['value']=countin_community($budcom,'','',true);
				}
			elseif($enrolcol=='spaces'){
				if($enrolyear==$currentyear){
					$cell['value']=$enrol_tablecells['capacity']['value'] - $enrol_tablecells['currentroll']['value'] - $app_tablerows[$yid]['AC']['value'];
					if(isset($boardercoms)){
						foreach($boardercoms as $index => $boardercom){
							$cell['value_boarders'][$index]=$enrol_tablecells['capacity']['value_boarders'][$index] - $enrol_tablecells['currentroll']['value_boarders'][$index] - $app_tablerows[$yid]['AC']['value_boarders'][$index];
							}
						}
					}
				elseif(isset($pre_reenrolcell)){
					$cell['value']=$enrol_tablecells['capacity']['value'] -	$enrol_tablecells['transfersin']['value'] - $enrol_tablecells['newenrolments']['value'] - $enrol_tablecells['pending']['value'] - $enrol_tablecells['reenroling']['repeat'] - $pre_reenrolcell['confirm'];
					if(isset($boardercoms)){
						foreach($boardercoms as $index => $boardercom){
							$cell['value_boarders'][$index]=$enrol_tablecells['capacity']['value_boarders'][$index] - $enrol_tablecells['newenrolments']['value_boarders'][$index] - $enrol_tablecells['pending']['value_boarders'][$index] - $enrol_tablecells['reenroling']['repeat_boarders'][$index] - $pre_reenrolcell['confirm_boarders'][$index];
							}
						}
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
