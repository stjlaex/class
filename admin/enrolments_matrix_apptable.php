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
 * but all values (specified above) are still totalled in final column.
 * The exception is Enquired which is done outside this.
 */

if($enrolyear==$currentyear){
	$appcols_value=array('AP','AT','RE','CA','ACP','AC','WL');
	}
else{
	$appcols_value=array('AP','AT','RE','CA','ACP','AC','WL');
	}

	/* The cols array defines the column headers and display class. */
	foreach($application_steps as $enrolstatus){
		if(in_array($enrolstatus,$appcols_value)){
			if($enrolstatus=='EN'){
				/* Treated as static keyed values */
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


if($enrolyear==$currentyear){
	$appcols['newnewenrolments']['class']='blank';
	$appcols['newnewenrolments']['display']=get_string('newnewenrolments',$book);
	$appcols['newnewenrolments']['value']='newnewenrolments';
	}



	/* A tablecells array for each row holds the computed values and is stored in tablerows array indexed by the yid. */
	foreach($yeargroups as $year){
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
		$newnewcurrentsids=0;
		if($enrolyear==$currentyear){
			$yearcomid=$yeargroup_comids[$yid];
			/* Student whose applications have been accepted since the start of the year*/
			$d_nosids=mysql_query("SELECT COUNT(student_id) FROM
						comidsid WHERE community_id='$yearcomid'
					AND (leavingdate>'$todate' OR leavingdate='0000-00-00' OR leavingdate IS NULL)
					AND joiningdate<='$todate' AND joiningdate>='$yearstartdate';");
			$newnewcurrentsids=mysql_result($d_nosids,0);

			//trigger_error('countin - '.$yearcomid. ' :' .$yearstartdate.' : '.$todate,E_USER_WARNING);

			/* Students who joined the current roll regardless of when accepted*/

			/*
			$d_nosids=mysql_query("SELECT COUNT(c.student_id) FROM
					comidsid AS c JOIN info AS i ON c.student_id=i.student_id WHERE c.community_id='$yearcomid'
					AND i.entrydate>'$yearstartdate';");
			*/

			$d_nosids=mysql_query("SELECT COUNT(student_id) FROM
						comidsid WHERE community_id='$yearcomid'
					AND (leavingdate>'$todate' OR leavingdate='0000-00-00' OR leavingdate IS NULL)
					AND joiningdate<='$yearstartdate' AND joiningdate>='$yearenddate';");
			$newcurrentsids=mysql_result($d_nosids,0);
			}

		$values=array();//holds values for this row
		$values[0]=0;// index 0 is for the total for this row
		reset($application_steps);
		foreach($application_steps as $index => $enrolstatus){
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

			/* Two possibilities: 'live' or 'static' values. Only enquiries are static (ie. keyed) */
			$value_boarder=0;
			if($enrolstatus=='EN'){
				$value=countin_community($com,'','',true);
				$display=$value+$extravalue;
				}
			elseif($enrolstatus=='CA' or $enrolstatus=='RE'){
				/* Exclude applications which were canclled before the start of the year. */
				$d_nosids=mysql_query("SELECT COUNT(student_id) FROM
						comidsid WHERE community_id='$comid'
					AND (leavingdate>'$todate' OR leavingdate='0000-00-00' OR leavingdate IS NULL)
					AND joiningdate<='$todate' AND joiningdate>='$yearstartdate';");
				$value=mysql_result($d_nosids,0);
				$displayvalue=$value+$extravalue;
				$display='<a href="admin.php?current=enrolments_list.php&cancel='.
						$choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&yid='. $yid.
						'&comid='.$com['id'].'">'.$displayvalue.'</a>';
				}
			else{
				$value=countin_community($com);
				if($value>0){
					$value_boarder=countin_community_extra($com,'boarder','B');
					}
				$displayvalue=$value+$extravalue;
				$display='<a href="admin.php?current=enrolments_list.php&cancel='.
						$choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&yid='. $yid.
						'&comid='.$com['id'].'">'.$displayvalue.'</a>';
				}


			$values[$index+1]=$value;

			/* Don't count enquiries as full applications so don't add to row total. */
			if($enrolstatus!='EN'){
				$values[0]+=$values[$index+1];
				$values['B']+=$value_boarder;
				//trigger_error($index.' : '.$value.' : '.$value_boarder,E_USER_WARNING);
				}



			/* Only set the display value if the column is being
					displayed in the matrix but always set the value for totals*/
			if(in_array($enrolstatus,$appcols_value)){
				$app_tablecells[$enrolstatus]['display']=$display;
				}

			$app_tablecells[$enrolstatus]['value']=$value;
			$app_tablecells[$enrolstatus]['name']=$enrolstatus.':'.$yid;
			$app_tablecells[$enrolstatus]['value_boarder']=$value_boarder;
			$app_tablecells[$enrolstatus]['name_boarder']=$enrolstatus.':boarder';
			}

		/* Don't forget applications who have already joined current
		   roll have to be counted as applications received. */
		$app_tablecells['C']['value']=$newcurrentsids;
		$app_tablecells['newnewenrolments']['value']=$newnewcurrentsids;
		if($enrolyear==$currentyear){
			$app_tablecells['newnewenrolments']['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
							$choice.'&choice='. $choice.'&enrolyear='. 
							$enrolyear.'&yid='. $yid. '&startdate='.$yearstartdate.'&enrolstage=C">' 
							.$newnewcurrentsids.'</a>';
			}
		$app_tablecells['applicationsreceived']['value']=$values[0]+$newnewcurrentsids;
		$app_tablecells['applicationsreceived']['value_boarder']=$values['B'];
		$app_tablecells['applicationsreceived']['name']='TOTAL:'.$yid;
		$app_tablecells['applicationsreceived']['name_boarder']='TOTAL:boarder';
		$app_tablecells['applicationsreceived']['display']='<a href="admin.php?current=enrolments_list.php&cancel=' 
							   . $choice.'&choice='. $choice.'&enrolyear='
							   . $enrolyear.'&yid='. $yid.'&comid=-1">' .$app_tablecells['applicationsreceived']['value'].'</a>';


		if($enrolyear>$currentyear){
			/* Total enquries for current year */
			$d_s=mysql_query("SELECT SUM(count) FROM community AS c WHERE 
				 c.name LIKE 'EN:$yid' AND c.year='$enrolyear' AND c.type='enquired';");
			if(mysql_result($d_s,0)){
				$app_tablecells['enquiries']['value']=mysql_result($d_s,0);
				}
			else{
				$app_tablecells['enquiries']['value']=0;
				}
			$app_tablecells['enquiries']['name']='EN:'.$yid;
			$app_tablecells['enquiries']['display']=$app_tablecells['enquiries']['value'];
			}

		$app_tablerows[$yid]=$app_tablecells;
		}

?>
