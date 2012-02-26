<?php
/**
 *											enrolments_matrix_apptable.php
 *
 *	This is the applications table.
 */


$app_tablerows=array();
$appcols=array();

/* The order here will define the order of the columns in the table. */
$application_steps=array('EN','AP','AT','RE','CA','ACP','AC','WL');

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

if($enrolyear>$currentyear){
	$appcols['enquiries']['class']='blank';
	if(!empty($CFG->enrol_enquiries) and $CFG->enrol_enquiries=='static'){
		$appcols['enquiries']['display']='<a href="admin.php?current=enrolments_edit.php&cancel='.
		$choice.'&choice='. $choice.'&enrolyear='.$enrolyear. 
		'&enrolstatus=EN">'.get_string('enquiries',$book).'</a>';
		}
	else{
		$appcols['enquiries']['display']=get_string('enquiries',$book);
		}
	$appcols['enquiries']['value']='enquiries';
	}
$appcols['TOTAL']['class']='other';
$appcols['TOTAL']['display']=get_string('applicationsreceived',$book);
$appcols['TOTAL']['value']='applicationsreceived';


/* The cols array defines the column headers and display class. */
foreach($application_steps as $cellindex){
	if(in_array($cellindex,$appcols_value)){
		$appcols[$cellindex]['class']='live';
		$appcols[$cellindex]['display']=get_string(displayEnum($cellindex,'enrolstatus'),$book);
		$appcols[$cellindex]['value']=$cellindex;
		}
	}

if($enrolyear==$currentyear){
	$appcols['newnewenrolments']['class']='blank';
	$appcols['newnewenrolments']['display']=get_string('newnewenrolments',$book);
	$appcols['newnewenrolments']['value']='newnewenrolments';
	}

/* A tablecells array for each row holds the computed values and
   is stored in tablerows array indexed by the yid. */
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

	$values=array();// holds values for this row
	$values['TOTAL']=0;// the sum total applications for this row
	foreach($application_steps as $colindex => $enrolstatus){

		$value=0;
		$extravalue=0;
		if($enrolstatus=='EN'){$comtype='enquired';}
		elseif($enrolstatus=='AC'){
			$comtype='accepted';
			//$extravalue=$newcurrentsids;
			}
		else{$comtype='applied';}

		$com=array('id'=>'','type'=>$comtype, 
				   'name'=>$enrolstatus.':'.$yid,'year'=>$enrolyear);
		$comid=update_community($com);
		$com['id']=$comid;

		foreach($boardercoms as $bindex=>$boardercom){
			$value_boarders[$bindex]=0;
			}

		if($enrolstatus=='EN' and !empty($CFG->enrol_enquiries) and $CFG->enrol_enquiries=='static'){
			/* Exception is static values for enquiries. */
			$value=countin_community($com,'','',true);
			$displayvalue=$value+$extravalue;
			$display=$displayvalue;
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
						'&comid='.$com['id'].'&enrolstatus='.$enrolstatus.'">'.$displayvalue.'</a>';
			}
		else{
			$value=countin_community($com);
			if($value>0){
				foreach($boardercoms as $bindex=>$boardercom){
					$value_boarders[$bindex]=countin_community_extra($com,'boarder',$boardercom['name']);
					}
				}
			$displayvalue=$value+$extravalue;
			$display='<a href="admin.php?current=enrolments_list.php&cancel='.
						$choice.'&choice='. $choice.'&enrolyear='. $enrolyear.'&yid='. $yid.
						'&comid='.$com['id'].'&enrolstatus='.$enrolstatus.'">'.$displayvalue.'</a>';
			}

		
		/* Don't count enquiries as full applications so don't add to row total. */
		if($enrolstatus!='EN'){
			$values[$colindex]=$value;
			$values['TOTAL']+=$value;
			foreach($boardercoms as $bindex=>$boardercom){
				$values['B'][$bindex]+=$value_boarders[$bindex];
				}
			$cellindex=$enrolstatus;
			}
		else{
			$cellindex='enquiries';
			}
		/* Only set the display value if the column is being
			   displayed in the matrix but always set the value for totals*/
		if(in_array($enrolstatus,$appcols_value) OR $enrolstatus=='EN'){
			$app_tablecells[$cellindex]['display']=$display;
			}

		$app_tablecells[$cellindex]['value']=$value;
		$app_tablecells[$cellindex]['name']=$enrolstatus.':'.$yid;
		foreach($boardercoms as $bindex=>$boardercom){
			$app_tablecells[$cellindex]['value_boarders'][$bindex]=$value_boarders[$bindex];
			$app_tablecells[$cellindex]['name_boarders'][$bindex]=$enrolstatus.':boarder';
			}
		}

	/* Don't forget applications who have already joined current
	   roll have to be counted as applications received. */
	$app_tablecells['C']['value']=$newcurrentsids;
	$app_tablecells['newnewenrolments']['value']=$newnewcurrentsids;
	if($enrolyear==$currentyear){
		$app_tablecells['newnewenrolments']['display']='<a href="admin.php?current=enrolments_list.php&cancel='.
			$choice.'&choice='. $choice.'&enrolyear='. 
			$enrolyear.'&yid='. $yid. '&startdate='.$yearstartdate.'&enrolstage=C">' .$newnewcurrentsids.'</a>';
		}
	$app_tablecells['applicationsreceived']['value']=$values['TOTAL']+$newnewcurrentsids;
	foreach($boardercoms as $bindex=>$boardercom){
		$app_tablecells['applicationsreceived']['value_boarders'][$bindex]=$values['B'][$bindex];
		}
	$app_tablecells['applicationsreceived']['name']='TOTAL:'.$yid;
	$app_tablecells['applicationsreceived']['name_boarder']='TOTAL:boarder';
	$app_tablecells['applicationsreceived']['display']='<a href="admin.php?current=enrolments_list.php&cancel=' 
		. $choice.'&choice='. $choice.'&enrolyear='
		. $enrolyear.'&yid='. $yid.'&comid=-1">' .$app_tablecells['applicationsreceived']['value'].'</a>';

	$app_tablerows[$yid]=$app_tablecells;
	}

?>
