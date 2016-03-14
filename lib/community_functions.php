<?php
/**							lib/community_functions.php
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2016
 *	@version
 *	@since
 */


/**
 *
 * Return an array of communitites of one particular type,
 * ignores differences in year by default.
 *
 *	@param string $type type of community
 *	@param string $year
 *	@param string $yid
 *
 *	@return array all students in a community
 */
function list_communities($type='',$year='',$yid='%'){
	if($type!='' and $year==''){
		if($type=='year'){
			if($yid!='%' and $yid!=''){
				$d_com=mysql_query("SELECT community.id, community.name,
					community.type, community.year, community.capacity,
					community.detail, yeargroup.name AS displayname
					FROM community JOIN yeargroup ON
					community.name=yeargroup.id WHERE
					community.type='$type' AND community.name='$yid';");
				}
			else{
				$d_com=mysql_query("SELECT community.id, community.name,
					community.type, community.year, community.capacity,
					community.detail, yeargroup.name AS displayname
					FROM community JOIN yeargroup ON
					community.name=yeargroup.id WHERE
					community.type='$type' ORDER BY yeargroup.section_id,
					yeargroup.sequence;");
				}
			}
		elseif($type=='form' or $type=='house'){
			$d_com=mysql_query("SELECT community.id, community.name,
					community.type, community.year, community.capacity,
					community.detail, groups.yeargroup_id, groups.gid
					FROM community JOIN groups ON community.id=groups.community_id
					JOIN yeargroup ON groups.yeargroup_id=yeargroup.id
					WHERE community.type='$type' AND groups.yeargroup_id LIKE '$yid'
					ORDER BY yeargroup.sequence, groups.yeargroup_id, community.name;");
			}
		elseif($type=='reg'){
			$d_com=mysql_query("SELECT community.id, community.name,
					community.type, community.year, community.capacity,
					community.detail, groups.yeargroup_id, groups.gid
					FROM community JOIN groups ON
					community.id=groups.community_id WHERE
					community.type='$type'
					ORDER BY community.name;");
			}
		else{
			$d_com=mysql_query("SELECT id, name, type, year, capacity, detail FROM community
									WHERE type='$type' ORDER BY name;");
			}
		}
	elseif($type!=''){
		$d_com=mysql_query("SELECT id, name, type, year, capacity, detail FROM community
								WHERE type='$type' AND year='$year' ORDER BY name;");
		}

	$communities=array();
	if(isset($d_com) and mysql_num_rows($d_com)>0){
		while($com=mysql_fetch_array($d_com,MYSQL_ASSOC)){
			if($com['detail']!=''){$com['displayname']=$com['detail'];}
			elseif($com['type']=='house' and array_key_exists('yeargroup_id',$com)){$com['displayname']=get_yeargroupname($com['yeargroup_id']).' - '. $com['name'];}
			else{$com['displayname']=$com['name'];}
  			$communities[]=$com;
			}
		}

	return $communities;
	}

/**
 * Returns the xml Community identified by its comid
 *
 *	@param string $comid
 *	@return array
 */
function fetchCommunity($comid=''){
  	$d_com=mysql_query("SELECT name, type, year, capacity,
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
	if($com['detail']==''){$display=$com['name'];}
	else{$display=$com['detail'];}
	$Community['Displayname']=array('label' => 'displayname',
									'value' => ''.$display);
	return $Community;
	}

/**
 * Returns a community array identified by its comid
 *
 *	@param string $comid
 *	@return array
 *
 */
function get_community($comid=''){
	$community=array();
  	$d_com=mysql_query("SELECT id, name, type, year, capacity, detail, charge, chargetype, sessions
					FROM community WHERE id='$comid';");
	if(mysql_num_rows($d_com)>0){
		$com=mysql_fetch_array($d_com,MYSQL_ASSOC);
		$community=$com;
		if($community['detail']==''){$community['displayname']=$community['name'];}
		else{$community['displayname']=$community['detail'];}

		/* Fetch any associated permissions groups for this community
		 * and store in an array indexed by their yid. Note yeargroup
		 * communities are the only exception to the rule of searching
		 * by community_id because they have community_id=0 in the groups
		 * table.
		 */
		if($community['type']=='year'){
			$yid=$com['name'];
			$d_g=mysql_query("SELECT yeargroup_id AS yid, gid FROM groups WHERE community_id='0' AND yeargroup_id='$yid' AND type='p';");
			}
		else{
			$d_g=mysql_query("SELECT yeargroup_id AS yid, gid FROM groups WHERE community_id='$comid';");
			}
		if(mysql_num_rows($d_g)>0){
			$groups=array();
			while($g=mysql_fetch_array($d_g,MYSQL_ASSOC)){
				$groups[$g['yid']]=$g['gid'];
				}
			$community['groups']=$groups;
			}

		}
	else{
		$community['id']='';
		$community['type']='';
		$community['name']='';
		$community['year']='';
		$community['capacity']='';
		$community['detail']='';
		$community['displayname']='';
		}
	return $community;
	}

/**
 *
 * This is the uber community function, it first checks if a community
 * exists and either updates or creates. It expects an array with at
 * least type and name set.
 *
 *	@param array $community
 *	@param array $communityfresh
 *	@return string $comid.
 *
 */
function update_community($community,$communityfresh=array('id'=>'','type'=>'','name'=>'')){
	$comid='';
	$type=$community['type'];
	$name=$community['name'];
	if(isset($community['year'])){$year=$community['year'];}
	elseif($type=='accepted' or $type=='applied' or $type=='enquired'){
		$year=get_curriculumyear();
		}
	else{$year='0000';}
	if(isset($community['detail'])){$detail=$community['detail'];}else{$detail='';}
	if(isset($community['capacity'])){$capacity=$community['capacity'];}else{$capacity='0';}
	if($type!='' and $name!=''){
		$d_community=mysql_query("SELECT id FROM community WHERE
				type='$type' AND name='$name' AND year='$year'");
		/* If it doesn't exist then create. */
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

			/* pastoral communities need to have associated permissions' gorups created too */
			if(strtolower($type)=='house'){
				$yeargroups=list_yeargroups();
				foreach($yeargroups as $yeargroup){
					$yid=$yeargroup['id'];
					mysql_query("INSERT INTO groups (community_id,yeargroup_id,type) VALUES ('$comid','$yid','p');");
					}
				}
			elseif(strtolower($type)=='form' and isset($community['yeargroup_id'])){
				$yid=$community['yeargroup_id'];
				mysql_query("INSERT INTO groups (community_id,yeargroup_id,type) VALUES ('$comid','$yid','p');");
				}
			elseif(strtolower($type)=='reg'){
				$yid='-9000';
				mysql_query("INSERT INTO groups (community_id,yeargroup_id,type) VALUES ('$comid','$yid','p');");
				}

			}
		else{
			$comid=mysql_result($d_community,0);
			$tfresh=$communityfresh['type'];
			$nfresh=$communityfresh['name'];
			if($tfresh!='' and $nfresh!=''){
				mysql_query("UPDATE community SET type='$tfresh', name='$nfresh' WHERE id='$comid';");
				/* Form and year communities are special cases because
				 * the name is held in the student table as a shortcut
				 * and must be updated too.
				 */
				if($nfresh!=$community['name']){
					$oldname=$community['name'];
					if($community['type']=='form'){
						mysql_query("UPDATE student SET form_id='$nfresh' WHERE form_id='$oldname';");
						}
					elseif($community['type']=='year'){
						mysql_query("UPDATE student SET yeargroup_id='$nfresh' WHERE yeargroup_id='$oldname';");
						}
					}
				}
			if(isset($communityfresh['detail'])){
				$dfresh=$communityfresh['detail'];
				mysql_query("UPDATE community SET detail='$dfresh' WHERE id='$comid';");
				}
			if(isset($communityfresh['year'])){
				$yfresh=$communityfresh['year'];
				mysql_query("UPDATE community SET year='$yfresh' WHERE id='$comid';");
				}
			if(isset($communityfresh['capacity'])){
				$cfresh=$communityfresh['capacity'];
				mysql_query("UPDATE community SET capacity='$cfresh' WHERE id='$comid';");
				}
			if(isset($communityfresh['sessions'])){
				$sfresh=$communityfresh['sessions'];
				mysql_query("UPDATE community SET sessions='$sfresh' WHERE id='$comid';");
				}
			if(isset($communityfresh['charge'])){
				$chfresh=$communityfresh['charge'];
				mysql_query("UPDATE community SET charge='$chfresh' WHERE id='$comid';");
				}
			if(isset($communityfresh['yeargroup_id']) and $tfresh=='form'){
				$yidfresh=$communityfresh['yeargroup_id'];
				mysql_query("UPDATE groups SET yeargroup_id='$yidfresh' WHERE community_id='$comid';");
				}
			}
		}

	return $comid;
	}


/*
 * Lists all sids who are current members of both commmunities
 *
 *	@param array $community1
 *	@param array $community2
 *	@return array
 *
 */
function listin_both_communities($community1,$community2){
	$todate=date('Y-m-d');
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

	/* Not in com1 but in com2... */
  	$d_student=mysql_query("SELECT a.student_id, a.forename, a.middlenames,
					a.surname, a.preferredforename, a.form_id FROM
					com2students AS a LEFT JOIN com1students AS b ON
					a.student_id=b.student_id WHERE
					b.student_id IS NULL ORDER BY a.form_id, a.surname");
	$complement_students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['student_id']!=''){$complement_students[]=$student;}
		}

	/* Are in both com1 and com2... */
  	$d_student=mysql_query("SELECT a.student_id, a.forename, a.middlenames,
					a.surname, a.preferredforename, a.form_id FROM
					com2students AS a LEFT JOIN com1students AS b ON
					a.student_id=b.student_id WHERE
					b.student_id IS NOT NULL ORDER BY a.form_id, a.surname");
	$intersection_students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['student_id']!=''){$intersection_students[]=$student;}
		}

	mysql_query("DROP TABLE com2students");
	mysql_query("DROP TABLE com1students");

	return array('complement'=>$complement_students,'intersection'=>$intersection_students);
	}


/**
 *
 * Lists all sids who are current members of a commmunity.  With
 * $startdate set all students who joined before that date and with
 * $enddate set lists all sids who were members for that whole period
 * ie. joined before startdate and left after enddate.
 *
 *	@param array $community
 *	@param date $enddate
 *	@param date $startdate
 *	return array
 */
function listin_community($community,$enddate='',$startdate=''){
	$todate=date('Y-m-d');
	if($enddate==''){$enddate=$todate;}
	if($startdate==''){$startdate=$enddate;}
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	if(isset($community['yeargroup_id']) and $community['yeargroup_id']!='' and $community['yeargroup_id']>-9000){
		$yid=$community['yeargroup_id'];
		}

	$orderby=get_studentlist_order();

	/* Limit to students in this com for one yeargroup.
	 * NB. LIKE '%' will NOT get all students because it ignores yeargorup_id NULL!
	 * Hence the need for the second SELECT which ignores $yid.
	 */
	if(isset($yid) and $yid!='%'){
		$d_student=mysql_query("SELECT id, surname,
				forename, middlenames, preferredforename, form_id, gender, dob, comidsid.special AS special FROM student
				JOIN comidsid ON comidsid.student_id=student.id
				WHERE student.yeargroup_id LIKE '$yid' AND comidsid.community_id='$comid' AND
				(comidsid.leavingdate>'$enddate' OR comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)
				AND (comidsid.joiningdate<='$startdate' OR comidsid.joiningdate IS NULL) ORDER BY $orderby;");
		}
	else{
		$d_student=mysql_query("SELECT id, surname, forename, middlenames, preferredforename,
				form_id, gender, dob, comidsid.special AS special FROM student
				JOIN comidsid ON comidsid.student_id=student.id
				WHERE comidsid.community_id='$comid' AND
				(comidsid.leavingdate>'$enddate' OR comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)
				AND (comidsid.joiningdate<='$startdate' OR comidsid.joiningdate IS NULL) ORDER BY $orderby;");
		}

	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['id']!=''){$students[]=$student;}
		}

	return $students;
	}


/**
 *
 * Lists all sids who newly joined a commmunity, not just current members.
 * With $stardate set all students who joined after that date.
 * With $enddate set lists all student members who joined between the two dates.
 *
 *	@param array $community
 *	@param date $enddate
 *	@param date $startdate
 *	return array
 */
function listin_community_new($community,$startdate='',$enddate=''){
	$todate=date('Y-m-d');
	if($enddate==''){$enddate=$todate;}
	if($startdate==''){$startdate=$enddate;}
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	/*
	   TODO: this would be needed if it is to be restricted to current members who joined after start date
					AND (comidsid.leavingdate>'$enddate' OR comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)
					AND comidsid.joiningdate<='$enddate' AND comidsid.joiningdate>='$startdate';");
	*/
	$d_student=mysql_query("SELECT id, surname,
				forename, preferredforename, form_id, gender, dob, comidsid.special AS special FROM student
				JOIN comidsid ON comidsid.student_id=student.id
				WHERE comidsid.community_id='$comid'
						AND comidsid.joiningdate<='$enddate' AND comidsid.joiningdate>='$startdate';");
	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['id']!=''){$students[]=$student;}
		}

	return $students;
	}

/**
 *
 * Lists all sids who left the community after a certain date and/or joined
 * the community after the start date.
 *
 *	@param array $community
 *	@param date $enddate
 *	@param date $startdate
 *	return array
 */
function listin_community_leavers($community,$enddate='',$startdate=''){
	$todate=date('Y-m-d');
	if($enddate==''){$enddate=$todate;}
	if(isset($startdate)){$joiningdate=" AND comidsid.joiningdate>='$startdate' ";}
	else{$joiningdate="";}
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	$d_student=mysql_query("SELECT id, surname,
						forename, preferredforename, form_id, gender, dob, comidsid.special AS special FROM student
						JOIN comidsid ON comidsid.student_id=student.id
						WHERE comidsid.community_id='$comid' $joiningdate
							AND comidsid.leavingdate>'$enddate';");
	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
			if($student['id']!=''){$students[]=$student;}
			}

	return $students;
	}



/**
 * Joins $sid to the appropriate accomodation community based on a residencial stay
 * identified by $accid
 *
 *	@param string $sid
 *	@param string $accid
 *
 */
function set_accomodation($sid,$accid=''){
	$Student=fetchStudent_short($sid);
	$field=fetchStudent_singlefield($sid,'Boarder');
	$Student=array_merge($Student,$field);
	if($accid==''){
		$d_a=mysql_query("SELECT id FROM accomodation ORDER BY departuredate DESC");
		if(mysql_num_rows($d_a)<1){
			if($Student['Boarder']['value']!='' and  $Student['Boarder']['value']!='N'){
				mysql_query("INSERT INTO accomodation SET student_id='$sid'");
				$accid=mysql_insert_id();
				}
			}
		else{$a=mysql_fetch_array($d_a);$accid=$a['id'];}
		}
	$d_acc=mysql_query("SELECT * FROM accomodation WHERE id='$accid'");
	$acc=mysql_fetch_array($d_acc, MYSQL_ASSOC);
	$oldcomid=$acc['community_id'];
	//$comname=$Student['Gender']['value']. $acc['roomcategory']. $Student['Boarder']['value'];
	$comname=$Student['Boarder']['value'];
	if($oldcomid!=0){$oldcom=get_community($oldcomid);}
	else{$oldcom=array('id'=>'','name'=>'');}
	if($oldcom['name']!=$comname and $oldcomid!=0){
		/* if the accomodation needs have changed then the community */
		/* membership will have to change too, delete the old one*/
		mysql_query("DELETE FROM comidsid WHERE community_id='$oldcomid' AND student_id='$sid'");
		}

	if($Student['Boarder']['value']!='' and  $Student['Boarder']['value']!='N'){
		$community=array('type'=>'accomodation','name'=>$comname);
		$comid=set_community_stay($sid,$community,$acc['arrivaldate'],$acc['departuredate']);
		mysql_query("UPDATE accomodation SET community_id='$comid' WHERE id='$accid'");
		}
	else{
		mysql_query("DELETE FROM accomodation WHERE id='$accid' LIMIT 1");
		}
		/*delete any double bookings for accomodation!!!
		$d_comidsid=mysql_query("DELETE FROM comidsid USING comidsid, community WHERE
				 community.id=comidsid.community_id AND community.type='accomodation'
				AND community.id!='$comid' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<'$enddate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$startdate' OR
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)");
		*/
	}

/**
 *
 * Joins up a student to a commmunity for a set period
 * Can be used instead of join_community but
 * NOT to be used for enrolment communities, yeargroups, or forms!
 *
 *	@param integer $sid student
 *	@param array $community
 *	@param date $startdate
 *	@param date $enddate
 *	@return string
 */
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
				leavingdate='$enddate' WHERE community_id='$comid' AND student_id='$sid'");
			}
		}
	return $comid;
	}


/**
 * Simply does what it says. In very rare occasions (ie. applications)
 * the values are not counted but are instead static values stored in
 * the community table itself.
 *
 *	@param array $community
 *	@param date $startdate
 *	@param date $enddate
 *	@param logical $static
 *	@return integer
 */
function countin_community($community,$enddate='',$startdate='',$static=false){
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	if(isset($community['yeargroup_id']) and $community['yeargroup_id']!='' and $community['yeargroup_id']>-9000){$yid=$community['yeargroup_id'];}

	if($static){
		$d_c=mysql_query("SELECT count FROM community WHERE id='$comid';");
		$nosids=mysql_result($d_c,0);
		}
	else{
		$todate=date('Y-m-d');
		if($enddate==''){$enddate=$todate;}
		if($startdate==''){$startdate=$enddate;}
		if(isset($yid)){
			$d_student=mysql_query("SELECT COUNT(student_id) FROM comidsid JOIN student ON student.id=comidsid.student_id
				 WHERE community_id='$comid' AND student.yeargroup_id='$yid' AND (comidsid.leavingdate>'$enddate' OR
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)
				AND (comidsid.joiningdate<='$startdate' OR
				comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL);");
			}
		else{
			$d_student=mysql_query("SELECT COUNT(student_id) FROM comidsid
				 WHERE community_id='$comid' AND (comidsid.leavingdate>'$enddate' OR
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)
				AND (comidsid.joiningdate<='$startdate' OR
				comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL);");
			}
		$nosids=mysql_result($d_student,0);
		}
	return $nosids;
	}


/**
 * Another count function but this time returning a number for one gender.
 *
 *	@param array $community
 *	@param date $startdate
 *	@param date $enddate
 *	@param string $gender
 *	@return integer
 *
 */
function countin_community_gender($community,$gender='M',$enddate='',$startdate=''){
	$todate=date('Y-m-d');
	if($enddate==''){$enddate=$todate;}
	if($startdate==''){$startdate=$enddate;}
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	$d_student=mysql_query("SELECT COUNT(student_id) FROM comidsid
				JOIN student ON student.id=comidsid.student_id
				WHERE comidsid.community_id='$comid' AND student.gender='$gender'
				AND (comidsid.leavingdate>='$enddate' OR
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)
				AND (comidsid.joiningdate<='$startdate' OR
				comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL)");
	$nosids=mysql_result($d_student,0);
	return $nosids;
	}



/**
 *
 *
 */
function countin_community_extra($community,$field,$value,$enddate='',$startdate=''){
	$todate=date('Y-m-d');
	if($enddate==''){$enddate=$todate;}
	if($startdate==''){$startdate=$enddate;}
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	$d_student=mysql_query("SELECT COUNT(comidsid.student_id) FROM comidsid
				JOIN info ON info.student_id=comidsid.student_id
				WHERE comidsid.community_id='$comid' AND info.$field='$value'
				AND (comidsid.leavingdate>'$enddate' OR
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)
				AND (comidsid.joiningdate<='$startdate' OR
				comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL)");
	$nosids=mysql_result($d_student,0);
	return $nosids;
	}



/**
 *
 * Lists all sids who are current members of a commmunity but with the
 * extra restirction linked to the info table. NB. only returns sids
 * not a true student array.
 *
 * With $stardate set all students who joined after that date
 * and with $enddate set lists all student members in that period.
 *
 *	@param array $community
 *	@param string $extra
 *	@param date $enddate
 *	@param date $startdate
 *	return array
 *
 */
function listin_community_extra($community,$extra,$enddate='',$startdate=''){
	$todate=date('Y-m-d');

	if($enddate==''){$enddate=$todate;}
	if($startdate==''){$startdate=$enddate;}
	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}
	$d_student=mysql_query("SELECT comidsid.student_id AS id FROM comidsid
				JOIN info ON info.student_id=comidsid.student_id
				WHERE comidsid.community_id='$comid' AND $extra
				AND (comidsid.leavingdate>='$enddate' OR
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)
				AND (comidsid.joiningdate<='$startdate' OR
				comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL)");
	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['id']!=''){$students[]=$student;}
		}
	return $students;
	}


/**
 *
 * Returns all communities to which a student is currently enrolled.
 * Filter for community id, name or type optional.
 *
 *	@param integer $sid
 *	@param array $community
 *  $param boolean $current
 *	@return array
 */
function list_member_communities($sid,$community,$current=true){
	$todate=date("Y-m-d");
	$type=$community['type'];
	$name=$community['name'];
	if($community['id']!=''){
		$comid=$community['id'];
		$d_community=mysql_query("SELECT id, special, joiningdate, leavingdate FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.id='$comid' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)");
		}
	elseif($name!=''){
		$comid=update_community($community);
		$d_community=mysql_query("SELECT id, special, joiningdate, leavingdate FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.id='$comid' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL)");
		}
	elseif($type!=''){
		if($current){
			/* Current communities of one type. */
			$d_community=mysql_query("SELECT id, special, joiningdate, leavingdate FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.type='$type' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR
				comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00');");
			}
		else{
			/* Previous communities only of one type. */
			$d_community=mysql_query("SELECT id, special, joiningdate, leavingdate FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.type='$type' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<=comidsid.leavingdate AND comidsid.leavingdate!='0000-00-00' AND comidsid.leavingdate<'$todate');");
			}
		}
	else{
		/* All communities a student is a current member of. */
		$d_community=mysql_query("SELECT id, special, joiningdate, leavingdate FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR
				comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00')");
		}

	$coms=array();
   	while($com=mysql_fetch_array($d_community,MYSQL_ASSOC)){
		$coms[]=array_merge($com,get_community($com['id']));
		}

	return $coms;
	}





/**
 *
 * Returns all communities of one particular type to which a student
 * has belonged. Including current and preivous memberships.
 *
 *	@param integer $sid
 *	@param string $type
 *	@return array
 */
function list_member_history($sid,$type){
	$todate=date("Y-m-d");
	$coms=array();

	if($type!=''){

			/* Current communities of one type. */
			$d_community=mysql_query("SELECT id, special, joiningdate, leavingdate FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.type='$type' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR
				comidsid.leavingdate IS NULL OR comidsid.leavingdate='0000-00-00') ORDER BY comidsid.joiningdate DESC;");

			while($com=mysql_fetch_array($d_community,MYSQL_ASSOC)){
				$coms[]=array_merge($com,get_community($com['id']));
				}

			/* Previous communities only of one type. */
			$d_community=mysql_query("SELECT id, special, joiningdate, leavingdate FROM community JOIN
				comidsid ON community.id=comidsid.community_id
				WHERE community.type='$type' AND comidsid.student_id='$sid' AND
   				(comidsid.joiningdate<=comidsid.leavingdate AND comidsid.leavingdate!='0000-00-00' AND comidsid.leavingdate<'$todate')
				ORDER BY comidsid.joiningdate DESC;");

			while($com=mysql_fetch_array($d_community,MYSQL_ASSOC)){
				$coms[]=array_merge($com,get_community($com['id']));
				}

			}


	return $coms;
	}







/**
 *
 * Add a sid to a community, type must be set, if name is blank then
 * you are actually leaving any communities of that type. Will also
 * leave any communitites which conflict with the one being joined. Always
 * returns an array of oldcommunities left.
 *
 *	@param integer $sid
 *	@param array $community
 *	@return array
 *
 */
function join_community($sid,$community){
	global $CFG;

	$todate=date('Y-m-d');
	$type=$community['type'];
	$name=$community['name'];
	if(isset($community['year'])){$year=$community['year'];}
	else{$year='';}

	/* Membership of a form or yeargroup is exclusive - need to remove
	 * from old group first, and also where student progresses through
	 * application procedure from enquired to apllied to accepted to year
	 */
	$oldtypes=array();
    if($type=='form'){
		$studentfield='form_id';
		$oldtypes[]=$type;
		$enrolstatus='C';
		if($name!=''){
			/* If no new fid given then just remove from form group
					and leave in the same yeargroup*/
			$newyid=get_form_yeargroup($name);
			$d_student=mysql_query("SELECT yeargroup_id FROM student WHERE id='$sid';");
			$oldyid=mysql_result($d_student,0);
			/*if new form is in another yeargroup then need to move yeargroup too*/
			if($newyid!=$oldyid){join_community($sid,array('type'=>'year','name'=>$newyid));}
			}
		}
	elseif($type=='reg'){
		$oldtypes[]='reg';
		}
	elseif($type=='year'){
		$studentfield='yeargroup_id';
		$newyid=$name;
		$d_student=mysql_query("SELECT yeargroup_id FROM student WHERE id='$sid';");
		$oldyid=mysql_result($d_student,0);
		/*if moving yeargroup then need to leave old form too*/
		if($newyid!=$oldyid){$oldtypes[]='form';$oldtypes[]=$type;}
		/*may be joining from previous point in application procedure*/
		$oldtypes[]='alumni';
		$oldtypes[]='accepted';
		$oldtypes[]='applied';
		$oldtypes[]='enquired';
		/*on current roll so can't just disappear if yeargroup blank*/
		if($name=='' or $name=='none'){$name='none';$community['name']='none';}
		$enrolstatus='C';
		}
	elseif($type=='alumni'){
		$oldtypes[]='year';
		$oldtypes[]='form';
		$oldtypes[]='reg';
		$oldtypes[]='transport';
		$oldtypes[]='tutor';
		/*may be joining from previous point in application procedure*/
		$oldtypes[]='accepted';
		$oldtypes[]='applied';
		$oldtypes[]='enquired';
		$enrolstatus='P';
		}
	elseif($type=='accepted' or $type=='applied' or $type=='enquired'){
		list($enrolstatus,$yid)=explode(':',$name);
		/*may be joining from previous point in application procedure*/
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

	if(isset($community['id']) and $community['id']!=''){$comid=$community['id'];}
	elseif($name!=''){$comid=update_community($community);}
	else{$comid='';}

	/*first remove sid from any old conflicting communities*/
	$leftcommunities=array();
	foreach($oldtypes as $oldtype){
		$checkcommunity=array('id'=>'','type'=>$oldtype,'name'=>'');
		$oldcommunities=array();
		$oldcommunities=(array)list_member_communities($sid,$checkcommunity);
		foreach($oldcommunities as $oldcommunity){
			if($oldcommunity['name']!=$name or $oldcommunity['year']!=$year){
				$leftcommunities[$oldtype][]=$oldcommunity;
				leave_community($sid,$oldcommunity);
				}
			}
		}

	if($comid!=''){
		$d_comidsid=mysql_query("SELECT * FROM comidsid WHERE
				community_id='$comid' AND student_id='$sid' AND (leavingdate IS NULL OR leavingdate='0000-00-00')");
		if(mysql_num_rows($d_comidsid)==0){
			mysql_query("INSERT INTO comidsid SET joiningdate='$todate',
							community_id='$comid', student_id='$sid'");
			}
		else{
			/*mysql_query("UPDATE comidsid SET leavingdate='' WHERE
							community_id='$comid' AND student_id='$sid'");*/
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
		mysql_query("UPDATE info SET enrolstatus='$enrolstatus' WHERE student_id='$sid';");
		if($enrolstatus=='P'){
			/* Record the school leaving date. */
			mysql_query("UPDATE info SET leavingdate='$todate' WHERE student_id='$sid';");
			/* Remove from the transport lists */
			delete_journey_booking_all($sid,$todate);
			//mysql_query("DELETE FROM cidsid WHERE student_id='$sid';");
			}
		if(($enrolstatus=='C' or $enrolstatus=='AC') and $CFG->enrol_number_generate=='yes'){
			/* The student will now have an enrolment number issued if
			 * one does not exist and if configured for the enrolment
			 * number to be auto generated.
			 */
			$d_i=mysql_query("SELECT formerupn FROM info WHERE student_id='$sid';");
			$oldenrolno=mysql_result($d_i,0);
			if(empty($oldenrolno)){
				$enrolno=generate_enrolno($sid);
				}
			}
		}

	return $leftcommunities;
	}


/**
 *
 * Mark a sid as having left a commmunity
 * Does not delete the record only sets leavingdate to today
 * Should only really be called to do the work from within join_community
 *
 *	@param integer $sid
 *	@param array $community
 *	@return null
 */
function leave_community($sid,$community){
	$todate=date('Y-m-d');
	$type=$community['type'];
	$name=$community['name'];
	if($community['id']!=''){
		$comid=$community['id'];
		mysql_query("UPDATE comidsid SET leavingdate='$todate' WHERE
							community_id='$comid' AND student_id='$sid'");
		}
	if($type=='year'){mysql_query("UPDATE student SET yeargroup_id=NULL WHERE id='$sid'");}
	elseif($type=='form'){mysql_query("UPDATE student SET form_id='' WHERE id='$sid'");}
	}


/**
 *
 * Archives a community (not actually delete) by setting type empty.
 * Closes all student memberships of the community by setting
 * leavingdate to today.
 *
 *	@param array $community
 *	@return null
 */
function delete_community($com){
	$todate=date('Y-m-d');
	$type=$com['type'];
	$name=$com['name'];
	$year=get_curriculumyear();
	if($com['id']!=''){
		$comid=$com['id'];
		mysql_query("UPDATE community SET type='', year='$year' WHERE id='$comid';");
		$students=(array)listin_community($com);
		foreach($students as $student){
			$sid=$student['id'];
			leave_community($sid,$com);
			if($type=='year'){mysql_query("UPDATE student SET yeargroup_id=NULL WHERE id='$sid'");}
			elseif($type=='form'){mysql_query("UPDATE student SET form_id='' WHERE id='$sid'");}
			}
		}
	}



/**
 * Returns the yeargroup to which the form belongs.
 *
 *	@param integer $fid
 *	@param type $type
 *	@return integer
 */
function get_form_yeargroup($fid,$type='form'){
	if($fid!=' ' and $fid!=''){
		$d_y=mysql_query("SELECT yeargroup_id FROM groups
							JOIN community ON groups.community_id=community.id
							WHERE community.name='$fid' AND community.type='$type';");
		$yid=mysql_result($d_y,0);
		}
	else{
		$yid='';
		}
	return $yid;
	}


/**
 *  Reutrns all users with responsibilities for this community. These
 *  would be the form tutors for example for a form cummunity.
 *
 *  Where the community array is a form then it will already have a
 *	unique gid set (one-to-one relationship).  But for other com types
 *	then the gids will be fetched and it can be one-to-many. The $yid
 *	will limit to gids associated to a single yeargroup.
 *
 *	@param array $com
 *	@param array $perms
 *	@param integer $yid
 *	@return array
 */
function list_community_users($com,$perms=array('r'=>1,'w'=>1,'x'=>1),$yid='%'){
	$users=array();
	$gids=array();

	$r=$perms['r'];
	$w=$perms['w'];
	$x=$perms['x'];

	if(isset($com['gid']) and $com['gid']!=''){
		$gids[]=$com['gid'];
		}
	else{
		if(isset($com['id']) and $com['id']!=''){
			$comid=$com['id'];
			}
		elseif(isset($com['name']) and $com['name']!='' and isset($com['type']) and $com['type']!='' ){
			$comid=update_community($com);
			}
		if(isset($comid)){
			$d_g=mysql_query("SELECT DISTINCT gid FROM groups WHERE groups.community_id='$comid' AND groups.yeargroup_id LIKE '$yid';");
			while($g=mysql_fetch_array($d_g,MYSQL_ASSOC)){
				$gids[]=$g['gid'];
				}
			}
		}

	if(sizeof($gids)>0){
		foreach($gids as $gid){
			$d_u=mysql_query("SELECT DISTINCT users.uid,
					username, forename, surname, email, epfusername, role, title
					FROM users JOIN perms ON users.uid=perms.uid
					WHERE users.nologin='0' AND perms.gid='$gid' AND perms.r='$r'
					AND perms.w='$w' AND perms.x='$x';");
			while($user=mysql_fetch_array($d_u,MYSQL_ASSOC)){
				$uid=$user['uid'];
				$users[$uid]=$user;
				}
			}
		}

	return $users;
	}


/**
 * Returns the section id for the yeargroup to which the
 * student belongs, defaults to whole school secid=1 if nothing else available.
 *
 *	@param integer $sid
 *	@return integer
 */
function get_student_section($sid){
	if($sid!=' ' and $sid!=''){
		$d_s=mysql_query("SELECT section_id FROM yeargroup JOIN
				student ON student.yeargroup_id=yeargroup.id WHERE student.id='$sid';");
		if(mysql_num_rows($d_s)>0){
			$secid=mysql_result($d_s,0);
			}
		}
	if(!isset($secid)){
		$secid=1;// default to whole school
		}
	return $secid;
	}

/**
 * Returns the yeargroup id for the yeargroup to which the student
 * belongs.
 *
 *	@param integer $sid
 *	@return integer
 */
function get_student_yeargroup($sid){
	if($sid!=' ' and $sid!=''){
		$d_y=mysql_query("SELECT yeargroup_id FROM student WHERE id='$sid';");
		$yid=mysql_result($d_y,0);
		}
	else{
		$yid=-1000;
		}

	return $yid;
	}



/**
 *
 * Find all current cohorts with which a community is associated.  By
 * defualt only returns cohorts for this academic year when current is
 * true.
 *
 *	@param array $community
 *  @param boolean $current
 *
 *	@return array
 *
 */
function list_community_cohorts($community,$current=true){
	if($community['type']=='form'){
		/*forms only associate with cohorts through their yeargroup*/
		$fid=$community['name'];
		$yid=get_form_yeargroup($fid);
		$community=array('id'=>'','type'=>'year','name'=>$yid);
		}

	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=update_community($community);}

	$cohorts=array();
	$d_cohort=mysql_query("SELECT * FROM cohort JOIN
						cohidcomid ON cohidcomid.cohort_id=cohort.id WHERE
						cohidcomid.community_id='$comid' ORDER BY year DESC, course_id;");
   	while($cohort=mysql_fetch_array($d_cohort, MYSQL_ASSOC)){
		if($current){
			$currentyear=get_curriculumyear($cohort['course_id']);
			$currentseason='S';
			if($cohort['year']==$currentyear and $cohort['season']==$currentseason){
				$cohorts[]=$cohort;
				}
			}
		else{
			$cohorts[]=$cohort;
			}
		}
	return $cohorts;
	}




/**
 * Returns an array of yids which are asscociated with this course. It
 * will not include other types of groups which may contain students
 * subscribed to the course.
 *
 *
 *	@param string $crid
 *
 *	@return array $yids
 *
 */
function list_course_yeargroups($crid){

	$curryear=get_curriculumyear($crid);

	$yids=array();
	$d_c=mysql_query("SELECT id, name FROM community JOIN
						cohidcomid ON cohidcomid.community_id=community.id WHERE community.type='year' AND
						cohidcomid.cohort_id=ANY(SELECT cohort.id FROM cohort WHERE cohort.year='$curryear' AND cohort.course_id='$crid');");
   	while($com=mysql_fetch_array($d_c, MYSQL_ASSOC)){
		$yids[]=$com['name'];
		}

	return $yids;
	}



/**
 *
 * This call generate_epfusername and evaluate if the return efun is
 * unique. The scope for uniqueness is either the db itself.
 *
 */
function new_epfusername($User=array(),$role='student'){
	global $CFG;

	$fresh=false;

        /* Scope for uniqueness is just this db. */
        while(!($fresh)){
                $epfusername=generate_epfusername($User,$role);
                if($role=='guardian'){
                        $sr=mysql_query("SELECT id FROM guardian WHERE epfusername='$epfusername';");
                        }
                elseif($role=='student'){
                        $sr=mysql_query("SELECT * FROM info WHERE epfusername='$epfusername';");
                        }
                elseif($role=='staff'){
                        }

                if(isset($sr) and mysql_num_rows($sr)>1){$fresh=false;}
                else{$fresh=true;}
                }

	return $epfusername;
	}


/**
 *
 * Generates a new epfusername for the given User xml-array.
 *
 * The epfusername format is decided by one of three possible roles:
 * staff, student or guardian.
 *
 * Student username will be firstname plus four digit number.
 * Contact username will be first six characters of surname plus four digit number.
 * Staff username will be the schools clientid plus their ClaSS username.
 *
 * This on its own does not guarantee a unique epfusername (which it
 * must be!) but instead relies on the calling function to ensure
 * uniqueness.
 *
 *	@param array $User
 *	@param string role
 *	@return string
 */
function generate_epfusername($User=array(),$role='student'){
	global $CFG;
	setlocale(LC_CTYPE,'en_GB');
	$epfusername='';
	$classid=$User['id_db'];

	$nums='';
	$code='';
	if($role=='student'){
		/* Only use the first part of the forename.
		 * Use preferred forename if it is set (often best for Chinese students).
		 */
		if(!empty($User['PreferredForename']['value']) and strlen($User['PreferredForename']['value'])>2){
			$forename=$User['PreferredForename']['value'];
			}
		else{
			$forename=$User['Forename']['value'];
			}
		$forename=(array)explode(' ',$forename);
		$start=iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $forename[0]);
		$start=utf8_to_ascii($start);
		$start=str_replace(' ','',$start);
		$start=str_replace("'",'',$start);
		$start=str_replace('-','',$start);
		$start=str_replace('?','',$start);
		$classtable='info';
		$classfield='student_id';
		while(count($nums)<9){$nums[rand(1,9)]=null;}
		while(strlen($code)<4){$code.=array_rand($nums);}
		$tail=$code;
		}
	elseif($role=='guardian' or $role=='contact'){
		/* Only use the first part of the surname. */
		$surname=$User['Surname']['value'];
		$start=iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $surname);
		$start=utf8_to_ascii($start);
		$start=str_replace(' ','',$start);
		$start=str_replace("'",'',$start);
		$start=str_replace('-','',$start);
		$start=str_replace('?','',$start);
		$start=substr($start,0,6);
		$classtable='guardian';
		$classfield='id';
		while(count($nums)<9){$nums[rand(1,9)]=null;}
		while(strlen($code)<4){$code.=array_rand($nums);}
		$tail=$code;
		}
	elseif($role=='staff'){
		/* Staff usernames are unique within their own ClaSS but need
		 * to maintain that within the epf by adding the school's clientid.
		 */
		if(isset($CFG->clientid)){$start=$CFG->clientid;}
		else{$start='';}
		$tail=$User['Username']['value'];
		$classtable='users';
		$classfield='uid';
		}

	$epfusername=good_strtolower($start. $tail);
	$start=str_replace('-','',$epfusername);
	$epfusername=str_replace("'",'',$epfusername);
	$epfusername=str_replace(' ','',$epfusername);
	$epfusername=clean_text($epfusername);

	mysql_query("UPDATE $classtable SET epfusername='$epfusername' WHERE $classfield='$classid';");

	return $epfusername;
	}





/**
 *
 * Generates a new enrolment number and places in info table. Only
 * used when CFG->enrol_number_generate is yes. Only place this could
 * be overridden is if enrolno is part of an import of student
 * details.
 *
 * If sid=-1 then no update will be made and the enrolno will simply
 * be returned.
 *
 * Checks for duplicate values being generated and returns -1 on
 * error.
 *
 *	@param integer sid
 *	@return string enrolno
 */
function generate_enrolno($sid){
	global $CFG;

	if(function_exists('enrolno_formula')){
		/* Custom formula defined in enrolno_formula function. */
		$enrolno=enrolno_formula($sid);
		}
	else{
		/* The default is merely to take largest existing enrolment
		 * number and increment by 1
		 */
		$d_i=mysql_query("SELECT MAX(CAST(formerupn AS UNSIGNED)) FROM info;");
		$enrolno=mysql_result($d_i,0)+1;
		}

	if($sid>0){
		/* Only set the value if a sid is specified */
		$d_i=mysql_query("SELECT student_id FROM info WHERE formerupn='$enrolno';");
		if(mysql_num_rows($d_i)==0){
			mysql_query("UPDATE info SET formerupn='$enrolno' WHERE student_id='$sid';");
			trigger_error('NEW Enrol No: '.$enrolno,E_USER_ERROR);
			}
		else{
			trigger_error('DUPLICATE Enrol No: '.$enrolno,E_USER_ERROR);
			$enrolno=-1;
			}
		}

	return $enrolno;
	}

?>
