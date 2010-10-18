<?php
/**
 *											enrolments_matrix_apptable.php
 *
 *	This is the applications table - it can be 'live' numbers counted
 *  from the database or 'static' keyed values - configured in school.php
 *  by setting $CFG->enrol_applications='yes' or ='no' respectively.
 */


$app_tablerows=array();
$appcols=array();


if($enrolyear>$currentyear){
	$appcols['EN']['class']='blank';
	$appcols['EN']['display']='<a href="admin.php?current=enrolments_edit.php&cancel='.
		$choice.'&choice='. $choice.'&enrolyear='.$enrolyear. 
		'&enrolstatus=EN">'.get_string('enquiries',$book).'</a>';
	$appcols['EN']['value']='enquiries';
	}
$appcols['TOTAL']['class']='other';
$appcols['TOTAL']['display']=get_string('applicationsreceived',$book);
$appcols['TOTAL']['value']='applicationsreceived';

/* The order here will define the order of the columns in the table. */
$application_steps=array('AP','ATD','AT','RE','CA','ACP','AC','WL');
/* The table's column headers are the application steps which are
 * really enrolstatus codes. Only display the subset specified here
 * but all values (specified above) are still totalled in final column
 * with the exception of Enquired which is a special case.
 */
$appcols_value=array('AP','AT','RE','CA','ACP','AC','WL');


	foreach($application_steps as $enrolstatus){
		if(in_array($enrolstatus,$appcols_value)){
			if((!$applications_live and $enrolstatus!='AC') or $enrolstatus=='EN'){
				$appcols[$enrolstatus]['class']='static';
				$appcols[$enrolstatus]['display']='<a href="admin.php?current=enrolments_edit.php&cancel='.
					$choice.'&choice='. $choice.'&enrolyear='.$enrolyear. 
			   		'&enrolstatus='.$enrolstatus.'">'.get_string(displayEnum($enrolstatus,'enrolstatus'),$book).'</a>';
				}
			else{
				$appcols[$enrolstatus]['class']='live';
				$appcols[$enrolstatus]['display']=get_string(displayEnum($enrolstatus,'enrolstatus'),$book);
				}
			}
		$appcols[$enrolstatus]['value']=$enrolstatus;
		}



	reset($yeargroups);
	while(list($yindex,$year)=each($yeargroups)){
		$app_tablecells=array();
		if($enrolyear>$currentyear){
			$app_tablecells['enquiries']=array();
			}
		$app_tablecells['applicationsreceived']=array();
		$yid=$year['id'];

		/* First count applicants who have joined the current roll and
		 * hence are not counted in one of the applied groups -
		 * still want the number as part of the matrix for totals
		 */
		$newcurrentsids=0;
		if($enrolyear>$currentyear){
			}
		else{
			$yearcomid=$yeargroup_comids[$yid];
			/* Student whose applications have been accepted since the start of the year*/
			$d_nosids=mysql_query("SELECT COUNT(student_id) FROM
						comidsid WHERE community_id='$yearcomid'
					AND (leavingdate>'$todate' OR 
					leavingdate='0000-00-00' OR leavingdate IS NULL) 
					AND joiningdate<='$todate' AND joiningdate>='$yearstartdate';");
			/* Students who joined the current roll regardless of when accepted*/
			$d_nosids=mysql_query("SELECT COUNT(c.student_id) FROM
					comidsid AS c JOIN info AS i ON c.student_id=i.student_id WHERE c.community_id='$yearcomid'
					AND i.entrydate>'$yearstartdate';");
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
				//$extravalue=$newcurrentsids;
				}
			else{$comtype='applied';}
			if($enrolstatus=='AT'){
				$extravalue=$app_tablecells['ATD']['value'];
				/*This is a deprecated code - just here for backward compatibility*/
				}
			$com=array('id'=>'','type'=>$comtype, 
					   'name'=>$enrolstatus.':'.$yid,'year'=>$enrolyear);
			$comid=update_community($com);
			$com['id']=$comid;

			/* 'live' or 'static' values */
			if(($applications_live or $enrolstatus=='AC') and $enrolstatus!='EN'){
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
			/* Don't count enquiries as full applications. */
			if($enrolstatus!='EN'){$values[0]+=$values[$index+1];}

			/* Only set the display value if the column is being
					displayed in the matrix but always set the value for totals*/
			if(in_array($enrolstatus,$appcols_value)){
				$app_tablecells[$enrolstatus]['display']=$display;
				}
			$app_tablecells[$enrolstatus]['value']=$value;
			$app_tablecells[$enrolstatus]['name']=$enrolstatus.':'.$yid;
			//$app_tablecells[$enrolstatus]['extravalue']=$extravalue;
			}

		/* Don't forget applications who have already joined current
		   roll have to be counted as applications received. */
		$app_tablecells['C']['value']=$newcurrentsids;
		$app_tablecells['applicationsreceived']['value']=$values[0];
		$app_tablecells['applicationsreceived']['name']='TOTAL:'.$yid;
		$app_tablecells['applicationsreceived']['display']='<a href="admin.php?current=enrolments_list.php&cancel='. 
			$choice.'&choice='. $choice.'&enrolyear='. 
			$enrolyear.'&yid='. $yid.'&comid=-1">' .$app_tablecells['applicationsreceived']['value'].'</a>';


		if($enrolyear>$currentyear){
			/* Total enquries for current year */
			$d_s=mysql_query("SELECT SUM(count) FROM community AS c WHERE 
				 c.name LIKE 'EN:$yid' AND c.year='$enrolyear' AND c.type='enquired';");
			$app_tablecells['enquiries']['value']=mysql_result($d_s,0);
			$app_tablecells['enquiries']['name']='EN:'.$yid;
			$app_tablecells['enquiries']['display']=$app_tablecells['enquiries']['value'];
			}

		$app_tablerows[$yid]=$app_tablecells;
		}

?>
