<?php
/**							lib/community_functions.php
 */

/* return an array of communitites of one particular type*/
/* igonres differences in year by default*/
function list_communities($type='',$year=''){
	if($type!='' and $year==''){
		$d_com=mysql_query("SELECT id, name, year, capacity, detail FROM community WHERE 
								type='$type' ORDER BY name");
		}
	elseif($type!=''){
		$d_com=mysql_query("SELECT id, name, year, capacity, detail FROM community WHERE 
								type='$type' AND year='$year' ORDER BY name");
		}

	if(mysql_num_rows($d_com)>0){
		$communities=array();
		while($com=mysql_fetch_array($d_com,MYSQL_ASSOC)){
			$community=array();
			$community['id']=$com['id'];
			$community['type']=$type;
			$community['name']=$com['name'];
			$community['year']=$com['year'];
			$community['capacity']=$com['capacity'];
			$community['detail']=$com['detail'];
			$communities[]=$community;
			}
		}

	return $communities;
	}

/*Returns the xml Community identified by its comid*/
function fetchCommunity($comid=''){
  	$d_com=mysql_query("SELECT name, type, year, cpacpity, 
					detail FROM community WHERE id='$comid'");
	$com=mysql_fetch_array($d_com,MYSQL_ASSOC);
	$Community=array();
	$Community['id_db']=$comid;
	$Community['Type']=array('label' => 'type',
							 'value' => ''.$com['type']);
	$Community['Name']=array('label' => 'name',
							 'value' => ''.$com['name']);
	$Community['Year']=array('label' => 'year',
							 'value' => ''.$com['year']);
	$Community['Capacity']=array('label' => 'capacity',
							 'value' => ''.$com['capacity']);
	$Community['Detail']=array('label' => 'name',
							 'value' => ''.$com['detail']);
	return $Community;
	}

/*Returns a community array identified by its comid*/
function get_community($comid=''){
	$community=array();
  	$d_com=mysql_query("SELECT name, type, year, capacity, detail 
					FROM community WHERE id='$comid'");
	if(mysql_num_rows($d_com)>0){
		$com=mysql_fetch_array($d_com,MYSQL_ASSOC);
		$community['id']=$comid;
		$community['type']=$com['type'];
		$community['name']=$com['name'];
		$community['year']=$com['year'];
		$community['capacity']=$com['capacity'];
		$community['detail']=$com['detail'];
		}
	else{
		$community['id']='';
		$community['type']='';
		$community['name']='';
		$community['year']='';
		$community['capacity']='';
		$community['detail']='';
		}
	return $community;
	}

/* this is the uber community function*/
/* checks for a community and either updates or creates*/
/* expects an array with at least type and name set*/
function update_community($community,$communityfresh=array('id'=>'','type'=>'','name'=>'')){
	$comid='';
	$type=$community['type'];
	$name=$community['name'];
	if(isset($community['year'])){$year=$community['year'];}
	elseif($type=='accepted' or $type=='applied' or $type=='enquired'){$year=get_curriculumyear();}
	else{$year='0000';}
	//trigger_error('upcom type:'.$type.' name:'.$name,E_USER_WARNING);
	if(isset($community['detail'])){$detail=$community['detail'];}else{$detail='';}
	if(isset($community['capacity'])){$capacity=$community['capacity'];}else{$capacity='0';}
	if($type!='' and $name!=''){
		$d_community=mysql_query("SELECT id FROM community WHERE
				type='$type' AND name='$name' AND year='$year'");	
		if(mysql_num_rows($d_community)==0){
			if($type=='year'){
				/*a year group always gets a detail of yeargroup_name*/
				$d_y=mysql_query("SELECT name FROM yeargroup WHERE id='$name'");
				if(mysql_num_rows($d_y)>0){$detail=mysql_result($d_y,0);}
				else{$detail='No year group';}
				}
			mysql_query("INSERT INTO community (name,type,year,capacity,detail) VALUES
				('$name', '$type', '$year', '$capacity', '$detail')");
			$comid=mysql_insert_id();
			}
		else{
			$comid=mysql_result($d_community,0);
			$tfresh=$communityfresh['type'];
			$nfresh=$communityfresh['name'];
			if($tfresh!='' and $nfresh!=''){
				mysql_query("UPDATE community SET type='$tfresh', 
									name='$nfresh' WHERE id='$comid'");
				}
			if(isset($communityfresh['detail'])){
				$dfresh=$communityfresh['detail'];
				mysql_query("UPDATE community SET detail='$dfresh' WHERE id='$comid'");
				}
			if(isset($communityfresh['year'])){
				$yfresh=$communityfresh['year'];
				mysql_query("UPDATE community SET year='$yfresh' WHERE id='$comid'");
				}
			if(isset($communityfresh['capacity'])){
				$cfresh=$communityfresh['capacity'];
				mysql_query("UPDATE community SET capacity='$cfresh' WHERE id='$comid'");
				}
			}
		}
	return $comid;
	}


/*Lists all sids who are current members of a commmunity*/
function listin_union_communities($community1,$community2){
	$todate=date("Y-m-d");
	if(isset($community1['id']) and $community1['id']!=''){$comid1=$community1['id'];}
	else{$comid1=update_community($community1);}
	if(isset($community2['id']) and $community2['id']!=''){$comid2=$community2['id'];}
	else{$comid2=update_community($community2);}

	mysql_query("CREATE TEMPORARY TABLE com1students
			(SELECT a.student_id, b.surname, b.forename,
			b.middlenames, b.preferredforename, b.form_id FROM
			comidsid a, student b WHERE a.community_id='$comid1' AND
			b.id=a.student_id AND (a.leavingdate>'$todate' 
			OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL))");
	mysql_query("CREATE TEMPORARY TABLE com2students
			(SELECT a.student_id, b.surname, b.forename,
			b.middlenames, b.preferredforename, b.form_id FROM
			comidsid a, student b WHERE a.community_id='$comid2' AND
			b.id=a.student_id AND (a.leavingdate>'$todate' OR 
			a.leavingdate='0000-00-00' OR a.leavingdate IS NULL))");

  	$d_student=mysql_query("SELECT a.student_id, a.forename, a.middlenames,
					a.surname, a.preferredforename, a.form_id FROM
					com2students AS a LEFT JOIN com1students AS b ON
					a.student_id=b.student_id WHERE
					b.student_id IS NULL ORDER BY a.form_id, a.surname");
	$scabstudents=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['student_id']!=''){$scabstudents[]=$student;}
		}

  	$d_student=mysql_query("SELECT a.student_id, a.forename, a.middlenames,
					a.surname, a.preferredforename, a.form_id FROM
					com2students AS a LEFT JOIN com1students AS b ON
					a.student_id=b.student_id WHERE
					b.student_id IS NOT NULL ORDER BY a.form_id, a.surname");
	$unionstudents=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['student_id']!=''){$unionstudents[]=$student;}
		}
	return array('scab'=>$scabstudents,'union'=>$unionstudents);
	}

/* Lists all sids who are current members of a commmunity*/
/* With $stardate set all students who joined after that date*/
/* and with $enddate set lists all student members in that period*/
function listin_community($community,$startdate='',$enddate=''){
	$todate=date("Y-m-d");
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	if($startdate==''){
		$d_student=mysql_query("SELECT id, surname,
				forename, preferredforename, form_id FROM student 
				JOIN comidsid ON comidsid.student_id=student.id
				WHERE comidsid.community_id='$comid' AND
				(comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)
					ORDER BY student.surname");
		}
	elseif($enddate==''){
		$d_student=mysql_query("SELECT id, surname,
				forename, preferredforename, form_id FROM student 
				JOIN comidsid ON comidsid.student_id=student.id
				WHERE comidsid.community_id='$comid' AND
				(comidsid.joiningdate>'$startdate')
					ORDER BY student.surname");
		}
	else{
		$d_student=mysql_query("SELECT id, surname,
				forename, preferredforename, form_id FROM student 
				JOIN comidsid ON comidsid.student_id=student.id
				WHERE comidsid.community_id='$comid' AND
				(comidsid.joiningdate<'$enddate') AND
				(comidsid.leavingdate>'$startdate' OR 
					comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)
					ORDER BY student.surname");
		}

	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['id']!=''){$students[]=$student;}
		}
	return $students;
	}


/* Joins up a student to a commmunity for a set period*/
/* Can be used instead of join_community but*/
/* NOT to be used for enrolment communities, yeargroups, or forms!*/
function set_community_stay($sid,$community,$startdate,$enddate){
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	if($comid!=''){
		$d_comidsid=mysql_query("SELECT * FROM comidsid WHERE
				community_id='$comid' AND student_id='$sid'");
		if(mysql_num_rows($d_comidsid)==0){
			mysql_query("INSERT INTO comidsid SET joiningdate='$startdate',
			   	leavingdate='$enddate', community_id='$comid', student_id='$sid'");
			}
		else{
			mysql_query("UPDATE comidsid SET joiningdate='$startdate', 
				leavingdate='enddate' WHERE community_id='$comid' AND student_id='$sid'");
			}
		}
	return $comid;
	}

/*simply does what it says*/
function countin_community($community){
	$todate=date("Y-m-d");
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	$d_student=mysql_query("SELECT COUNT(student_id) FROM comidsid
							  WHERE community_id='$comid' AND
				(comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)");
	$nosids=mysql_result($d_student,0);
	return $nosids;
	}

/*Returns all communities to which a student is currently enrolled*/
function list_member_communities($sid,$community){
	$todate=date("Y-m-d");
	$type=$community['type'];
	$name=$community['name'];
	if($community['id']!=''){
		$comid=$community['id'];
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.id='$comid' AND comidsid.student_id='$sid' AND
   				(comidsid.entrydate<='$todate' OR comidsid.joiningdate  IS NULL) 
				AND (comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)");
		}
	elseif($name!=''){
		$comid=update_community($community);
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.id='$comid' AND comidsid.student_id='$sid' AND
   				(comidsid.entrydate<='$todate' OR comidsid.joiningdate IS NULL) 
				AND (comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)");
		}
	elseif($type!=''){
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.type='$type' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
		}
	else{
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
		}
	$communities=array();
   	while($community=mysql_fetch_array($d_community, MYSQL_ASSOC)){
		$communities[]=$community;
		}
	return $communities;
	}

/* Add a sid to a community, type must be set, if name is blank then */
/* you are actually leaving any communities of that type. Will also */
/* leave any communitites which conflict the one being joined. Always */
/* returns an array of oldcommunities left*/
function join_community($sid,$community){
	$todate=date("Y-m-d");
	$type=$community['type'];
	$name=$community['name'];

	/*membership of a form or yeargroup is exclusive - need to remove
	from old group first, and also where student progresses through
	application procedure from enquired to apllied to accepted to year*/
	$oldtypes=array();
    if($type=='form'){
		$studentfield='form_id';
		$oldtypes[]=$type;
		$enrolstatus='C';
		$d_yeargroup=mysql_query("SELECT yeargroup_id FROM form WHERE id='$name'");
		$newyid=mysql_result($d_yeargroup,0);
		$d_student=mysql_query("SELECT yeargroup_id FROM student WHERE id='$sid'");
		$oldyid=mysql_result($d_yeargroup,0);
		if($newyid!=$oldyid){join_community($sid,array('type'=>'year','name'=>$yid));}
		}
	elseif($type=='year'){
		$studentfield='yeargroup_id';
		$oldtypes[]='form';
		$oldtypes[]=$type;
		$enrolstatus='C';
		/*on current roll so can't just disappear if yeargroup blank*/
		if($name=='' or $name=='none'){$name='none';$community['name']='none';}
		}
	elseif($type=='alumni'){
		$oldtypes[]='year';
		$oldtypes[]='form';
		/*may be joining form previous point in application procedure*/
		$oldtypes[]='accepted';
		$oldtypes[]='applied';
		$oldtypes[]='enquired';
		$enrolstatus='P';
		}
	elseif($type=='accepted' or $type=='applied' or $type=='enquired'){
		list($enrolstatus,$year)=split($name,':');
		/*may be joining form previous point in application procedure*/
		$oldtypes[]='accepted';
		$oldtypes[]='applied';
		$oldtypes[]='enquired';
		/*remove from current roll if appropriate*/
		$d_student=mysql_query("SELECT yeargroup_id FROM student WHERE id='$sid'");
		if(mysql_num_rows($d_student)>0){
			$oldtypes[]='year';
			$oldtypes[]='form';
			}
		}

	/*prior to version 0.8.13
	elseif($type=='accepted'){
		$oldtypes[]='applied';
		$oldtypes[]='enquired';
		$enrolstatus='AC';
		}
	elseif($type=='applied'){
		$oldtypes[]='enquired';
		$enrolstatus='AP';
		}
	elseif($type=='enquired'){
		$enrolstatus='EN';
		}
	*/

	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	elseif($name!=''){$comid=update_community($community);}
	else{$comid='';}

	/*first remove sid from any old conflicting communities*/
	$leftcommunities=array();
	while(list($index,$oldtype)=each($oldtypes)){
		$checkcommunity=array('id'=>'','type'=>$oldtype,'name'=>'');
		$oldcommunities=array();
		$oldcommunities=list_member_communities($sid,$checkcommunity);
		while(list($index,$oldcommunity)=each($oldcommunities)){
			if($oldcommunity['name']!=$name){
				$leftcommunities[$oldtype][]=$oldcommunity;
				leave_community($sid,$oldcommunity);
				}
			}
		}

	if($comid!=''){
		$d_comidsid=mysql_query("SELECT * FROM comidsid WHERE
				community_id='$comid' AND student_id='$sid'");
		if(mysql_num_rows($d_comidsid)==0){
			mysql_query("INSERT INTO comidsid SET joiningdate='$todate',
							community_id='$comid', student_id='$sid'");
			}
		else{
			mysql_query("UPDATE comidsid SET leavingdate='' WHERE
							community_id='$comid' AND student_id='$sid'");
			}
		}

	/*update the student with new enrolstatus, and new id for form or yeargroup*/
	if(isset($studentfield)){
		if($name!='none'){
			mysql_query("UPDATE student SET $studentfield='$name' WHERE id='$sid'");
			}
		else{
			mysql_query("UPDATE student SET $studentfield NULL WHERE id='$sid'");
			}
		}
	if(isset($enrolstatus)){
		mysql_query("UPDATE info SET enrolstatus='$enrolstatus' WHERE student_id='$sid'");
		}

	return $leftcommunities;
	}

/* Remove a sid from a commmunity*/
/* Should only really be called to do the work from within join_community*/
function leave_community($sid,$community){
	$todate=date('Y-m-d');
	$type=$community['type'];
	$name=$community['name'];
	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	mysql_query("UPDATE comidsid SET leavingdate='$todate' WHERE
							community_id='$comid' AND student_id='$sid'");
	if($type=='year'){mysql_query("UPDATE student SET yeargroup_id=NULL WHERE id='$sid'");}
	elseif($type=='form'){mysql_query("UPDATE student SET form_id='' WHERE id='$sid'");}
	return;
	}

/*Checks for a cohort and creates if it doesn't exist*/
/*expects an array with at least course_id and stage set*/
/*returns the cohort_id*/
function update_cohort($cohort){
	$crid=$cohort['course_id'];
	$stage=$cohort['stage'];
	if(isset($cohort['year'])){$year=$cohort['year'];}
	else{$year=get_curriculumyear($crid);}
	if(isset($cohort['season'])){$season=$cohort['season'];}
	else{$season='S';}
	if($crid!='' and $stage!=''){
		$d_cohort=mysql_query("SELECT id FROM cohort WHERE
				course_id='$crid' AND stage='$stage' AND year='$year'
				AND season='$season'");
		if(mysql_num_rows($d_cohort)==0){
			mysql_query("INSERT INTO cohort (course_id,stage,year,season) VALUES
				('$crid','$stage','$year','$season')");
			$cohid=mysql_insert_id();
			}
		else{
			$cohid=mysql_result($d_cohort,0);
			}
		}
	return $cohid;
	}


/*Lists all sids who are current members of a cohort*/
function listin_cohort($cohort){
	$todate=date("Y-m-d");
	if($cohort['id']!=''){$cohid=$cohort['id'];}
	else{$cohid=update_cohort($cohort);}
	mysql_query("CREATE TEMPORARY TABLE cohortstudent (SELECT DISTINCT student_id FROM comidsid 
				JOIN cohidcomid ON comidsid.community_id=cohidcomid.community_id
				WHERE cohidcomid.cohort_id='$cohid' AND
				(comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL))");
	$d_cohortstudent=mysql_query("SELECT b.id, b.surname,
				b.forename, b.middlenames, b.preferredforename, 
				b.form_id FROM cohortstudent a,
				student b WHERE b.id=a.student_id ORDER BY b.surname");
	$students=array();
   	while($student=mysql_fetch_array($d_cohortstudent,MYSQL_ASSOC)){
		$students[]=$student;
		}
	return $students;
	}

/*Find all current cohorts which a community is associated with*/
function list_community_cohorts($community){
	if($community['type']=='form'){
		/*forms only associate with cohorts through their yeargroup*/
		$fid=$community['name'];
		$d_form=mysql_query("SELECT yeargroup_id FROM form WHERE id='$fid'");
		$yid=mysql_result($d_form,0);
		$community=array('id'=>'','type'=>'year','name'=>$yid);
		}

	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}

	$cohorts=array();
	$d_cohort=mysql_query("SELECT * FROM cohort JOIN
						cohidcomid ON cohidcomid.cohort_id=cohort.id WHERE
						cohidcomid.community_id='$comid' ORDER BY course_id");
   	while($cohort=mysql_fetch_array($d_cohort, MYSQL_ASSOC)){
		$currentyear=get_curriculumyear($cohort['course_id']);
		$currentseason='S';
		if($cohort['year']==$currentyear and $cohort['season']==$currentseason){
			$cohorts[]=$cohort;
			}
		}
	return $cohorts;
	}

/*Defined as the calendar year that the current academic year ends */
/*TODO to sophisticate in future to cover definite endmonths for courses*/
function get_curriculumyear($crid=''){
	$d_course=mysql_query("SELECT endmonth FROM course WHERE id='$crid'");
	if(mysql_num_rows($d_course)>0){$endmonth=mysql_result($d_course,0);}
	else{$endmonth='';}
	if($endmonth==''){$endmonth='7';/*defaults to July*/}
	$thismonth=date('m');
	$thisyear=date('Y');
	if($thismonth>$endmonth){$thisyear++;}
	return $thisyear;
	}

?>