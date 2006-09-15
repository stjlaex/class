<?php

/*checks for a community and either updates or creates*/
/*expects an array with at least type and name set*/
function updateCommunity($community,$communityfresh=''){
	$type=$community['type'];
	$name=$community['name'];
	$typefresh=$communityfresh['type'];
	$namefresh=$communityfresh['name'];
	if(isset($community['details'])){$details=$community['details'];}
	if($type!='' and $name!=''){
		$d_community=mysql_query("SELECT id FROM community WHERE
				type='$type' AND name='$name'");	
		if(mysql_num_rows($d_community)==0){
			mysql_query("INSERT INTO community (name,type,details) VALUES
				('$name', '$type', '$details')");
			$comid=mysql_insert_id();
			}
		else{
			$comid=mysql_result($d_community,0);
			if($typefresh!='' and $namefresh!=''){
				if(isset($communityfresh['details'])){$detailsfresh=$communityfresh['details'];}
				mysql_query("UPDATE community SET type='$typefresh',
							name='$namefresh', details='$detailsfresh' WHERE name='$name'
								AND type='$type'");
				}
			}
		}
	return $comid;
	}


/*Lists all sids who are current members of a commmunity*/
function listin_unionCommunities($community1,$community2){
	if($community1['id']!=''){$comid1=$community1['id'];}
	else{$comid1=updateCommunity($community1);}
	if($community2['id']!=''){$comid2=$community2['id'];}
	else{$comid2=updateCommunity($community2);}

	mysql_query("CREATE TEMPORARY TABLE com1students
			(SELECT a.student_id, b.surname, b.forename,
			b.middlenames, b.form_id FROM
			comidsid a, student b WHERE a.community_id='$comid1' AND
			b.id=a.student_id AND (a.leavingdate='0000-00-00' OR a.leavingdate IS NULL))");
	mysql_query("CREATE TEMPORARY TABLE com2students
			(SELECT a.student_id, b.surname, b.forename,
			b.middlenames, b.form_id FROM
			comidsid a, student b WHERE a.community_id='$comid2' AND
			b.id=a.student_id AND (a.leavingdate='0000-00-00' OR a.leavingdate IS NULL))");
  	$d_student=mysql_query("SELECT a.student_id, a.forename, a.middlenames,
					a.surname, a.form_id FROM
					com2students AS a LEFT JOIN com1students AS b ON
					a.student_id=b.student_id WHERE
					b.student_id IS NULL ORDER BY a.form_id, a.surname");
	$scabstudents=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['student_id']!=''){$scabstudents[]=$student;}
		}

  	$d_student=mysql_query("SELECT a.student_id, a.forename, a.middlenames,
					a.surname, a.form_id FROM
					com2students AS a LEFT JOIN com1students AS b ON
					a.student_id=b.student_id WHERE
					b.student_id IS NOT NULL ORDER BY a.form_id, a.surname");
	$unionstudents=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['student_id']!=''){$unionstudents[]=$student;}
		}
	return array('scab'=>$scabstudents,'union'=>$unionstudents);
	}

/*Lists all sids who are current members of a commmunity*/
function listinCommunity($community){
	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=updateCommunity($community);}
	$d_student=mysql_query("SELECT id, surname,
				forename, form_id FROM student 
				JOIN comidsid ON comidsid.student_id=student.id
				WHERE comidsid.community_id='$comid' AND
				(comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)
					ORDER BY student.surname");
	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['id']!=''){$students[]=$student;}
		}
	return $students;
	}

/*simply does what it says*/
function countinCommunity($community){
	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=updateCommunity($community);}
	$d_student=mysql_query("SELECT COUNT(student_id) FROM comidsid
							  WHERE community_id='$comid' AND
				(comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)");
	$nosids=mysql_result($d_student,0);
	return $nosids;
	}

function checkCommunityMember($sid,$community){
	$todate=date("Y-m-d");
	$type=$community['type'];
	$name=$community['name'];
	if($community['id']!=''){
		$comid=$community['id'];
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.id='$comid' AND comidsid.student_id='$sid' AND
   				(comidsid.entrydate<='$todate' OR comidsid.joiningdate  IS NULL) 
				AND (comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
		}
	elseif($name!=''){
		$comid=updateCommunity($community);
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.id='$comid' AND comidsid.student_id='$sid' AND
   				(comidsid.entrydate<='$todate' OR comidsid.joiningdate IS NULL) 
				AND (comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
		}
	elseif($type!=''){
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.type='$type' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
		}
	else{
		$d_community=mysql_query("SELECT * FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
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
function joinCommunity($sid,$community){
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
		if($newyid!=$oldyid){joinCommunity($sid,array('type'=>'year','name'=>$yid));}
		}
	elseif($type=='year'){
		$studentfield='yeargroup_id';
		$oldtypes[]='form';
		$oldtypes[]=$type;
		$oldtypes[]='accepted';
		$oldtypes[]='applied';
		$oldtypes[]='enquired';
		$enrolstatus='C';
		/*on current roll so can't just disappear*/
		if($name=='' or $name=='none'){$name='none';$community['name']='none';}
		}
	elseif($type=='alumni'){
		$oldtypes[]='year';
		$oldtypes[]='form';
		$enrolstatus='P';
		}
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
	if($community['id']!=''){$comid=$community['id'];}
	elseif($name!=''){$comid=updateCommunity($community);}
	else{$comid='';}

	/*first remove sid from any old conflicting communities*/
	$leftcommunities=array();
	while(list($index,$oldtype)=each($oldtypes)){
		$checkcommunity=array('type'=>$oldtype,'name'=>'');
		$oldcommunities=array();
		$oldcommunities=checkCommunityMember($sid,$checkcommunity);
		while(list($index,$oldcommunity)=each($oldcommunities)){
			if($oldcommunity['name']!=$name){
				$leftcommunities[$oldtype][]=$oldcommunity;
				leaveCommunity($sid,$oldcommunity);
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
/* Should only really be called to do the work from within joinCommunity*/
function leaveCommunity($sid,$community){
	$todate=date("Y-m-d");
	$type=$community['type'];
	$name=$community['name'];
	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=updateCommunity($community);}
	mysql_query("UPDATE comidsid SET leavingdate='$todate' WHERE
							community_id='$comid' AND student_id='$sid'");
	if($type=='year'){mysql_query("UPDATE student SET yeargroup_id=NULL WHERE id='$sid'");}
	elseif($type=='form'){mysql_query("UPDATE student SET form_id='' WHERE id='$sid'");}
	return;
	}
?>