<?php
/**
 *							enrolments_matrix_apptable.php
 *
 *	This is the applications table - it can be 'live' numbers counted
 *  from the database or 'static' keyed values - configured in school.php
 *  by setting $CFG->enrol_applications='yes' or ='no' respectively.
 */


$app_tablerows=array();
$app_cols=array();

	/* The table's column headers are the application steps which are
	 * realy select enrolstatus codes*/
	$application_steps=array('EN','AP','AT','ATD','RE','CA','WL','ACP','AC');
	/* Only display a subset but all values are still totalled in final column*/
	$appcols_value=array('AT','ATD','RE','CA','WL','ACP','AC');

	while(list($colindex,$enrolstatus)=each($application_steps)){
			if(in_array($enrolstatus,$appcols_value)){
				if(!$applications_live and $enrolstatus!='AC'){
					$appcols[$colindex]['class']='static';
					$appcols[$colindex]['display']='<a href="admin.php?current=enrolments_edit.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='.$enrolyear. 
							'&enrolstatus='.$enrolstatus.'">'.get_string(displayEnum($enrolstatus,'enrolstatus'),$book).'</a>';
					}
				else{
					$appcols[$colindex]['class']='live';
					$appcols[$colindex]['display']=get_string(displayEnum($enrolstatus,'enrolstatus'),$book);
					}
				}
			$appcols[$colindex]['value']=$enrolstatus;
			}
	/* Final column for row totals*/
	$totalindex=$colindex++;
	$appcols[$totalindex]['class']='other';
	$appcols[$totalindex]['display']=get_string('applicationsreceived',$book);
	$appcols[$totalindex]['value']='applicationsreceived';

	reset($yeargroups);
	while(list($yindex,$year)=each($yeargroups)){
		$app_tablecells=array();
		$yid=$year['id'];
		
		/* First count applicants who have joined the current roll and
		 * hence are not counted in one of the applied groups -
		 * still want the number as part of the matrix for totals
		 */
		$newcurrentsids=0;
		if($enrolyear==$currentyear){
			$yearcomid=$yeargroup_comids[$yid];
			$d_nosids=mysql_query("SELECT COUNT(student_id) FROM
						comidsid WHERE community_id='$yearcomid'
					AND (leavingdate>'$todate' OR 
					leavingdate='0000-00-00' OR leavingdate IS NULL) 
					AND joiningdate<='$todate' AND joiningdate>='$yearstartdate-09-01';");
			$newcurrentsids=mysql_result($d_nosids,0);
			//$values[0]+=$newcurrentsids;
			}

		$values=array();//holds values for this row
		$values[0]=0;// index 0 is for the total for this row
		reset($application_steps);
		while(list($index,$enrolstatus)=each($application_steps)){
			$value=0;
			$extravalue=0;
			if($enrolstatus=='EN'){$comtype='enquired';}
			elseif($enrolstatus=='AC'){
				$comtype='accepted';
				$extravalue=$newcurrentsids;
				}
			else{$comtype='applied';}
			$com=array('id'=>'','type'=>$comtype, 
					   'name'=>$enrolstatus.':'.$yid,'year'=>$enrolyear);
			$comid=update_community($com);
			$com['id']=$comid;

			/* 'live' or 'static' values */
			if($applications_live or $enrolstatus=='AC'){
				$value=countin_community($com);
				$displayvalue=$value+$extravalue;
				$display='<a href="admin.php?current=enrolments_list.php&cancel='.
						$choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&yid='. $yid.
						'&comid='.$com['id'].'">'
						.$displayvalue.'</a>';
				}
			else{
				$value=countin_community($com,'','',true);
				$display=$value+$extravalue;
				}
			$values[$index+1]=$value;
			$values[0]+=$values[$index+1];

			/* Only set the display value if the column is being
					displayed in the matrix but always set the value for totals*/
			if(in_array($enrolstatus,$appcols_value)){
				$app_tablecells[$enrolstatus]['display']=$display;
						}
			$app_tablecells[$enrolstatus]['value']=$value;
			$app_tablecells[$enrolstatus]['extravalue']=$extravalue;
			}

		/*Don't forget applications who have already joined current roll*/
		$app_tablecells['C']['value']=$newcurrentsids;
		$app_tablecells['applicationsreceived']['value']=$values[0]+$newcurrentsids;

		$app_tablecells['applicationsreceived']['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
		$choice.'&choice='. $choice.'&enrolyear='. 
		$enrolyear.'&yid='. $yid.'&comid=-1">' .$app_tablecells['applicationsreceived']['value'].'</a>';
		$app_tablerows[$yid]=$app_tablecells;
		}
?>
