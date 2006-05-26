<?php	
/*												infobook/fetch_student.php

	Retrieves all infobook information about one student using only their sid.
	Returns the data in an array $Student and sets it as a session variable.
*/	


function fetchshortStudent($sid){
   	$d_student=mysql_query("SELECT * FROM student WHERE id='$sid'");
	$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
   	$d_info=mysql_query("SELECT * FROM info WHERE student_id='$sid'");
	$info=mysql_fetch_array($d_info,MYSQL_ASSOC);
	$student=nullCorrect($student);
	$info=nullCorrect($info);

	$Student=array();

	$Student['id_db']=$sid;
	
	$Student['Surname']=array('label' => 'surname',  'value' => $student['surname']);
	$Student['Forename']=array('label' => 'forename', 'value' => $student['forename']);
   	$Student['MiddleNames']=array('label' => 'middlenames', 'value' => $student['middlenames']);
	$Student['PreferredForename']=array('label' =>
					'preferredforename', 'value' => $student['preferredforename']);
	$Student['FormerSurname']=array('label' => 'formersurname', 'value' => $student['formersurname']);

	if($student['preferredforename']!=' '){$displaypfn='('.$student['preferredforename'].') ';}
	else{$displaypfn='';}
	if($student['middlenamelast']=='Y'){
		$Student['DisplayFullName']=array('label' => 'fullname',  
		   'value' => $displaypfn. 
					$student['forename'] . ' ' . $student['surname']
									.' '. $student['middlenames']);
		}
	else{
		$Student['DisplayFullName']=array('label' => 'fullname',  
		   'value' => $displaypfn . 
					$student['forename'] . ' ' .$student['middlenames']
									  . ' ' .$student['surname']);
		}

	$Student['Gender']=array('label' => 'gender', 'value' => $student['gender']);
   	$Student['DOB']=array('label' => 'dateofbirth', 'value' => $student['dob']);
   	$Student['RegistrationGroup']=array('label' => 'formgroup',  'value' => $student['form_id']);
	$yid=$student['yeargroup_id'];
	$d_yeargroup=mysql_query("SELECT ncyear FROM yeargroup WHERE id='$yid'");
	$ncyear=mysql_result($d_yeargroup,0);	
	$Student['NCyearActual']=array('label' => 'year', 'table_db' =>
		'student', 'field_db' => 'yeargroup_id', 'type_db'=>'enum', 
		'id_db' => $yid, 'value' => $ncyear);

	return $Student;
}

function fetchStudent($sid){
   	$d_student=mysql_query("SELECT * FROM student WHERE id='$sid'");
	$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
   	$d_info=mysql_query("SELECT * FROM info WHERE student_id='$sid'");
	$info=mysql_fetch_array($d_info,MYSQL_ASSOC);
	$student=nullCorrect($student);
	$info=nullCorrect($info);

	$Student=array();

/*
	Student is an xml compliant array designed for use with Serialize
	to generate xml from the values in the database. Each value from
	the database is stored in an array element identified by its
	xmltag. Various useful accompanying attributes are also stored. Of
	particular use are Label for displaying the value and _db
	attributes facilitating updates to the database when values are
	changed (the type_db for instance facilitates validation).

	$Student['xmltag']=array('label' => 'Display label','table_db' => '', 'field_db' =>
				'ClaSSdb field name', 'type_db'=>'ClaSSdb data-type', 'value' => $student['field_db']);

	The table from which the values are pulled are generally
	identifiable by the array in which they are stored (eg. address,
	student etc.) but table_db is avaiable if needed.


   	$Student['']=array('label' => '','table_db' => '', 'field_db' => '',
					'type_db'=>'', 'value' => $student['']);

*/
	$Student['id_db']=$sid;
	
	$Student['Surname']=array('label' => 'surname', 'table_db' => 'student', 'field_db' => 'surname',
					'type_db'=>'varchar(30)', 'value' => $student['surname']);
	$Student['Forename']=array('label' => 'forename','table_db' => 'student', 'field_db' => 'forename',
					'type_db'=>'varchar(30)', 'value' => $student['forename']);
   	$Student['MiddleNames']=array('label' => 'middlenames','table_db' => 'student', 'field_db' => 'middlenames',
					'type_db'=>'varchar(30)', 'value' => $student['middlenames']);
	$Student['PreferredForename']=array('label' =>
					'preferredforename', 'table_db' => 
					'student', 'field_db' => 'preferredforename',
					'type_db'=>'varchar(30)', 'value' => $student['preferredforename']);
	$Student['FormerSurname']=array('label' => 'formersurname', 
					'table_db' => 'student', 'field_db' => 'formersurname',
					'type_db'=>'varchar(30)', 'value' => $student['formersurname']);

	if($student['preferredforename']!=' '){$displaypfn='('.$student['preferredforename'].') ';}
	else{$displaypfn='';}
	if($student['middlenamelast']=='Y'){
		$Student['DisplayFullName']=array('label' => 'fullname',  
		   'value' => $displaypfn . 
					$student['forename'] . ' ' .$student['surname']
									  . ' ' .$student['middlenames']);
		}
	else{
		$Student['DisplayFullName']=array('label' => 'fullname',  
		   'value' => $displaypfn . 
					$student['forename'] . ' ' .$student['middlenames']
									  . ' ' . $student['surname']);
		}

	$Student['Gender']=array('label' => 'gender','table_db' => 'student', 'field_db' => 'gender',
					'type_db'=>'enum', 'value' => $student['gender']);
   	$Student['DOB']=array('label' => 'dateofbirth','table_db' => 'student', 'field_db' => 'dob',
					'type_db'=>'date', 'value' => $student['dob']);
   	$Student['RegistrationGroup']=array('label' => 'formgroup', 'table_db' => 'student', 'field_db' => 'form_id',
					'type_db'=>'varchar(30)', 'value' => $student['form_id']);
	
	$yid=$student['yeargroup_id'];
	$d_yeargroup=mysql_query("SELECT ncyear FROM yeargroup WHERE id='$yid'");
	$ncyear=mysql_result($d_yeargroup,0);
	
	$Student['NCyearActual']=array('label' => 'year', 'table_db' => 'student', 'field_db' => 'yeargroup_id',
					'type_db'=>'enum', 'id_db' => $yid, 'value' => $ncyear);


   	$Student['Nationality']=array('label' => 'nationality','table_db' => 'info', 'field_db' => 'nationality','type_db'=>'char(30)', 'value' => $info['nationality']);
   	$Student['MedicalFlag']=array('label' => 'medicalinformation','table_db' => 'info', 'field_db' => 'medical',
					'type_db'=>'enum', 'value' => $info['medical']);
   	$Student['SENFlag']=array('label' => 'seninformation','table_db' => 'info', 'field_db' => 'sen',
					'type_db'=>'enum', 'value' => $info['sen']);

   	$Student['Religion']=array('label' => 'religion','table_db' => 'info', 'field_db' => 'religion',
					'type_db' => 'enum', 'value' => $info['religion']);
   	$Student['FirstLanguage']=array('label' => 'firstlanguage','table_db' => 'info', 'field_db' => 'firstlanguage',
					'type_db'=>'enum', 'value' => $info['firstlanguage']);

   	$Student['E-Mail']=array('label' => 'email','table_db' => 'info', 'field_db' => 'email',
					'type_db'=>'varhar(50)', 'value' => 'blank');

   	$Student['EnrolNumber']=array('label' => 'enrolmentnumber','table_db' => 'info', 'field_db' => 'formerupn', 'type_db'=>'varchar(13)', 'value' => $info['formerupn']);

	$Student['EntryDate']=array('label' => 'schoolstartdate', 'table_db' => 'info', 'field_db' => 'entrydate', 'type_db'=>'date', 'value' => $info['entrydate']);

	$Student['LeavingDate']=array('label' => 'schoolleavingdate', 
	   'table_db' => 'info', 'field_db' => 'leavingdate', 'type_db'=>'date', 'value' => 'null');
   	$Student['Boarder']=array('label' => 'boarder','table_db' => 'info', 'field_db' => 'boarder',
					'type_db'=>'enum', 'value' => $info['boarder']);
   	$Student['PartTime']=array('label' => 'parttime','table_db' => 'info', 'field_db' => 'parttime',
					'type_db'=>'enum', 'value' => $info['parttime']);
   	$Student['TransportMode']=array('label' => 'modeoftransport','table_db' => 'info', 'field_db' => 'transportmode',
					'type_db'=>'enum', 'value' => $info['transportmode']);
   	$Student['TransportRoute']=array('label' => 'transportroute','table_db' => 'info', 'field_db' => 'transportroute',
					'type_db'=>'transportroute', 'value' => $info['transportroute']);

/*******Contacts****/
	$Contacts=array();

	$d_gidsid=mysql_query("SELECT * FROM gidsid WHERE student_id='$sid' ORDER BY priority");
	while($gidsid=mysql_fetch_array($d_gidsid,MYSQL_ASSOC)){
		$gidsid=nullCorrect($gidsid);

		$Contact=array();

		$gid=$gidsid['guardian_id'];
	   	$d_guardian=mysql_query("SELECT * FROM guardian WHERE id='$gid'");
		
		$guardian=mysql_fetch_array($d_guardian,MYSQL_ASSOC);
		$guardian=nullCorrect($guardian);

		$Contact['id_db']=$gid;
	
	   	$Contact['Order']=array('label' => 'priority','table_db' => 'gidsid', 'field_db' => 'priority',
				'type_db'=>'smallint(5)', 'value' => $gidsid['priority']);

	   	$Contact['Relationship']=array('label' => 'relationship', 'table_db' => 'gidsid', 'field_db' =>
					'relationship', 'type_db'=>'enum', 'value' => $gidsid['relationship']);

	   	$Contact['Surname']=array('label' => 'surname','table_db' => 'guardian', 'field_db' => 'surname',
					'type_db' => 'varchar(30)', 'value' => $guardian['surname']);
	   	$Contact['Forename']=array('label' => 'forename','table_db' => 'guardian', 'field_db' => 'forename',
					'type_db' => 'varchar(30)', 'value' => $guardian['forename']);
	   	$Contact['MiddleNames']=array('label' => 'middlenames','table_db' => 'guardian', 'field_db' =>
			'middlenames', 'type_db' => 'varchar(30)', 'value' =>	$guardian['middlenames']);
		

/*******ContactsAddresses****/
		$Addresses=array();
		$d_gidaid=mysql_query("SELECT * FROM gidaid WHERE guardian_id='$gid' ORDER BY priority");
		while($gidaid=mysql_fetch_array($d_gidaid,MYSQL_ASSOC)){
			$gidaid=nullCorrect($gidaid);
			$Address=array();

			$aid=$gidaid['address_id'];
		   	$d_address=mysql_query("SELECT * FROM address WHERE id='$aid'");
			$address=mysql_fetch_array($d_address,MYSQL_ASSOC);
			$address=nullCorrect($address);

			$Address['id_db']=$aid;
		   	$Address['Order']=array('label' => 'priority','table_db' => 'gidaid', 'field_db' => 'priority',
					'type_db'=>'smallint(5)', 'value' => $gidaid['priority']);
		   	$Address['AddressType']=array('label' => 'type','table_db'=>'gidaid', 'field_db' => 'addresstype',
					'type_db'=>'enum', 'value' => $gidaid['addresstype']);
		   	$Address['BuildingName']=array('label' => 'building','table_db' => 'address', 'field_db' => 'building',
					'type_db'=>'varchar(60)', 'value' => $address['building']);
		   	$Address['StreetNo']=array('label' => 'streetno.','table_db' => 'address', 'field_db' => 'streetno',
					'type_db'=>'varchar(10)', 'value' => $address['streetno']);
		   	$Address['Road']=array('label' => 'street','table_db' => 'address', 'field_db' => 'street',
					'type_db'=>'varchar(100)', 'value' => $address['street']);
		   	$Address['Neighbourhood']=array('label' => 'neighbourhood','table_db' => 'address', 'field_db' => 'neighbourhood',
					'type_db'=>'varchar(50)', 'value' => $address['neighbourhood']);
		   	$Address['Town']=array('label' => 'town/city','table_db' => 'address', 'field_db' => 'town',
					'type_db'=>'varchar(40)', 'value' => $address['town']);
		   	$Address['County']=array('label' => 'county','table_db' => 'address', 'field_db' => 'county',
					'type_db'=>'varchar(40)', 'value' => $address['county']);
		   	$Address['Postcode']=array('label' => 'postcode','table_db' => 'address', 'field_db' => 'postcode',
					'type_db'=>'varchar(8)', 'value' => $address['postcode']);

			$Addresses[]=$Address;
			}

		$Addresses=nullCorrect($Addresses);

/*******ContactsPhones****/
		$Phones=array();
		$d_phone=mysql_query("SELECT * FROM phone WHERE some_id='$gid' ORDER BY phonetype");
		while($phone=mysql_fetch_array($d_phone,MYSQL_ASSOC)){
			$phone=nullCorrect($phone);

			$Phone=array();

			$Phone['id_db']=$phone['id'];
	
		   	$Phone['PhoneNo']=array('label' => 'number','table_db' => 'phone', 'field_db' => 'number',
					'type_db'=>'varchar(22)', 'value' => $phone['number']);
		   	$Phone['PhoneType']=array('label' => 'type','table_db' => 'phone', 'field_db' => 'phonetype',
					'type_db'=>'enum', 'value' =>   	$phone['phonetype']);
			$Phones[]=$Phone;
			}
		$Contact['Addresses']=$Addresses;
		$Contact['Phones']=$Phones;
		$Contact=nullCorrect($Contact);
	
		$Contacts[]=$Contact;
		}
	$Contacts=nullCorrect($Contacts);

	$Student['Contacts']=$Contacts;




/*******SEN****/
	$SEN=array();
	$d_senhistory=mysql_query("SELECT * FROM senhistory WHERE 
				student_id='$sid' ORDER BY startdate");
/*		only working for one senhistory per student but could be */
/*					ramped up for more*/

	$SENhistory=array();
	while($senhistory=mysql_fetch_array($d_senhistory,MYSQL_ASSOC)){
		$senhistory=nullCorrect($senhistory);
		$senhid=$senhistory['id'];
		$SENhistory['id_db']=$senhid;
	   	$SENhistory['SENprovision']=array('label' => 'provision', 
					'table_db' => 'senhistory', 'field_db' => 'senprovision',
					'type_db'=>'enum', 'value' => $senhistory['senprovision']);
	   	$SENhistory['StartDate']=array('label' => 'startdate', 
					'table_db' => 'senhistory', 'field_db' => 'startdate',
					'type_db'=>'date', 'value' => $senhistory['startdate']);
	   	$SENhistory['NextReviewDate']=array('label' => 'nextreviewdate', 
					'table_db' => 'senhistory', 'field_db' => 'reviewdate',
					'type_db'=>'date', 'value' => $senhistory['reviewdate']);

/*				only working for one senhistory_id*/

		$d_sencurriculum=mysql_query("SELECT * FROM sencurriculum
				WHERE senhistory_id='$senhid' ORDER BY subject_id");
		$NationalCurriculum=array();
		while($sencurriculum=mysql_fetch_array($d_sencurriculum,MYSQL_ASSOC)){
			$sencurriculum=nullCorrect($sencurriculum);
			$Subject=array();
		   	$Subject['Subject']=array('label' =>
				'subject','table_db' => 'sencurriculum', 'field_db' =>
				'subject_id', 'type_db'=>'varchar(10)', 'value' => $sencurriculum['subject_id']);
			$Subject['Strengths']=array('label' =>
				'strengths','table_db' => 'sencurriculum', 'field_db' =>
				'comments', 'type_db'=>'varchar(250)', 'value' => $sencurriculum['comments']);
		   	$Subject['Weaknesses']=array('label' =>
				'weaknesses','table_db' => 'sencurriculum', 'field_db' =>
				'targets', 'type_db'=>'varchar(250)', 'value' => $sencurriculum['targets']);
		   	$Subject['Strategies']=array('label' =>
				'strategies','table_db' => 'sencurriculum', 'field_db' =>
				'outcome', 'type_db'=>'varchar(250)', 'value' => $sencurriculum['outcome']);
		   	$NationalCurriculum[]=$Subject;
			}
		$NationalCurriculum=nullCorrect($NationalCurriculum);
		$SEN['NationalCurriculum']=$NationalCurriculum;
		}

	$SENhistory=nullCorrect($SENhistory);
	$SEN['SENhistory']=$SENhistory;
	$d_sentypes=mysql_query("SELECT * FROM sentypes WHERE 
					student_id='$sid' ORDER BY senranking");
	$SENtypes=array();
	while($sentypes=mysql_fetch_array($d_sentypes,MYSQL_ASSOC)){
		$sentypes=nullCorrect($sentypes);
		$SENtype=array();
	   	$SENtype['SENtypeRank']=array('label' =>
	   	'ranking','table_db' => 'sentypes', 'field_db' =>
	   	'senranking', 'type_db'=>'enum', 'value' => $sentypes['senranking']);
	   	$SENtype['SENtype']=array('label' =>
	   	'type','table_db' => 'sentypes', 'field_db' =>
	   	'sentype', 'type_db'=>'enum', 'value' => $sentypes['sentype']);
		$SENtypes[]=$SENtype;
		}
	$SENtypes=nullCorrect($SENtypes);
	$SEN['SENtypes']=$SENtypes;
	$Student['SEN']=$SEN;


/*******Exclusions****/
	$Exclusions=array();
	$d_exclusions=mysql_query("SELECT * FROM exclusions WHERE 
				student_id='$sid' ORDER BY startdate");
	while($exclusion=mysql_fetch_array($d_exclusions,MYSQL_ASSOC)){
		$exclusions=nullCorrect($exclusions);
		$Exclusion=array();
	   	$Exclusion['Category']=array('label' => 'category', 
					'table_db' => 'exclusions', 'field_db' => 'category',
					'type_db'=>'enum', 'value' => $exclusion['category']);
	   	$Exclusion['StartDate']=array('label' => 'startdate', 
					'table_db' => 'exclusions', 'field_db' => 'startdate',
					'type_db'=>'date', 'value' => $exclusion['startdate']);
	   	$Exclusion['EndDate']=array('label' => 'enddate', 
					'table_db' => 'exclusions', 'field_db' => 'enddate',
					'type_db'=>'date', 'value' => $exclusion['enddate']);
	   	$Exclusion['Reason']=array('label' => 'reason', 
					'table_db' => 'exclusions', 'field_db' => 'reason',
					'type_db'=>'varchar(60)', 'value' => $exclusion['reason']);
		$Exclusions[]=$Exclusion;
		}
	$Exclusions=nullCorrect($Exclusions);
	$Student['Exclusions']=$Exclusions;

/*******Activities****/
	$Activities=array();
	$d_activities=mysql_query("SELECT * FROM activities WHERE 
				student_id='$sid' ORDER BY ncyear");
	while($activity=mysql_fetch_array($d_activities,MYSQL_ASSOC)){
		$activities=nullCorrect($activities);
		$Activity=array();
		$Activity['id_db']=$activity['id'];
	   	$Activity['Category']=array('label' => 'category', 'table_db'
				=> 'activities', 'field_db' => 'category', 'type_db'=>'varchar(30)', 
				'value' => $activity['category']);
	   	$Activity['EntryDate']=array('label' => 'date', 'table_db'
				=> 'activities', 'field_db' => 'category', 'type_db'=>'date', 
				'value' => $activity['entrydate']);
	   	$Activity['Details']=array('label' => 'details', 'table_db'
				=> 'activities', 'field_db' => 'detail', 'type_db'=>'varchar(250)', 
				'value' => $activity['category']);
	   	$Activity['NCyear']=array('label' => 'year', 
					'table_db' => 'activities', 'field_db' => 'ncyear',
					'type_db'=>'enum', 'value' => $activities['ncyear']);
		$Activities[]=$Activity;
		}
	$Activities=nullCorrect($Activities);
	$Student['Activities']=$Activities;


/*******Backgrounds****/
	$Backgrounds=array();
	$d_backgrounds=mysql_query("SELECT * FROM background WHERE
						student_id='$sid' ORDER BY entrydate DESC");
	while($background=mysql_fetch_array($d_backgrounds,MYSQL_ASSOC)){
		$background=nullCorrect($background);
		$Background=array();
		$Background['id_db']=$background['id'];
	   	$Background['EntryDate']=array('label' => 'entrydate', 
				'table_db' => 'background', 'field_db' => 'entrydate',
			   	'type_db'=>'date', 'value' => $background['entrydate']);
	   	$Background['Teacher']=array('label' => 'teacher', 
					'table_db' => 'comments', 'field_db' => 'teacher_id',
					'type_db'=>'varchar(14)', 'value' => $background['teacher_id']);
		$Categories=array();
		$Categories=array('label' => 'category', 
					'table_db' => 'comments', 'field_db' => 'category',
					'type_db'=>'varchar(100)', 'value' => ' ');
		$pairs=explode(';',$background['category']);
		for($c3=0; $c3<sizeof($pairs)-1; $c3++){
			list($catid, $rank)=split(':',$pairs[$c3]);
			$Category=array();
			$d_categorydef = mysql_query("SELECT name FROM categorydef
				WHERE id='$catid'");
			$catname = mysql_result($d_categorydef,0);
			$Category=array('label' => 'category', 'id_db' => $catid,
					'table_db' => 'categorydef', 'field_db' => 'name',
					'type_db'=>'varchar(30)', 'value' => $catname);
			$Category['rating']=array('value' => $rank);
			$Categories['Category'][]=$Category;
			}
		if (!isset($Categories['Category'])){		
			$Category=array('label' => 'category', 'id_db' => ' ',
					'table_db' => 'categorydef', 'field_db' => 'name',
					'type_db'=>'varchar(30)', 'value' => ' ');
			$Categories['Category'][]=$Category;
			}
		$Background['Categories']=$Categories;
	   	$Background['Comment']=array('label' => 'comment', 
				'table_db' => 'background', 'field_db' => 'detail',
			   	'type_db'=>'blob', 'value' => $background['detail']);
		$Backgrounds[]=$Background;
		}
	$Backgrounds=nullCorrect($Backgrounds);
	$Student['Backgrounds']=$Backgrounds;

/*******Incidents****/
	$Incidents=array();
	$d_incidents=mysql_query("SELECT * FROM incidents WHERE
		student_id='$sid' ORDER BY entrydate DESC");
	while($incident=mysql_fetch_array($d_incidents,MYSQL_ASSOC)){
		$incident=nullCorrect($incident);
		$Incident=array();
		$Incident['id_db']=$incident['id'];
	   	$Incident['Category']=array('label' => 'category', 'table_db'
					=> 'incidents', 'field_db' => 'category',
					'type_db' => 'varchar(30)', 'value' => $incident['category']);
	   	$Incident['Detail']=array('label' => 'detail', 
				'table_db' => 'incidents', 'field_db' => 'detail',
				'type_db'=>'varchar(250)', 'value' => $incident['detail']);
	   	$Incident['Subject']=array('label' => 'subject', 
				'table_db' => 'incidents', 'field_db' => 'subject',
			   	'type_db'=>'varchar(10)', 'value' => $incident['subject_id']);
	   	$Incident['Outcome']=array('label' => 'outcome', 
				'table_db' => 'incidents', 'field_db' => 'outcome',
			   	'type_db'=>'varchar(200)', 'value' => $incident['outcome']);
	   	$Incident['EntryDate']=array('label' => 'date', 
				'table_db' => 'incidents', 'field_db' => 'entrydate',
			   	'type_db'=>'date', 'value' => $incident['entrydate']);
	   	$Incident['NCyear']=array('label' => 'year', 
				'table_db' => 'incidents', 'field_db' => 'ncyear',
			   	'type_db'=>'enum', 'value' => $incident['ncyear']);
		$Incidents[]=$Incident;
		}
	$Incidents=nullCorrect($Incidents);
	$Student['Incidents']=$Incidents;

/*******Prizes****/
	$Prizes=array();
	$d_prizes=mysql_query("SELECT * FROM prizes WHERE
	student_id='$sid' ORDER BY entrydate DESC");
	while($prize=mysql_fetch_array($d_prizes,MYSQL_ASSOC)){
		$prize=nullCorrect($prize);
		$Prize=array();
		$Prize['id_db']=$prize['id'];
	   	$Prize['Category']=array('label' => 'priority','table_db' =>
				'prizes', 'field_db' => 'category',
			   	'type_db'=>'smallint(5)', 'value' => $prize['category']);
	   	$Prize['Detail']=array('label' => 'detail', 
				'table_db' => 'prizes', 'field_db' => 'detail',
			   	'type_db'=>'varchar(250)', 'value' => $prize['detail']);
	   	$Prize['NCyear']=array('label' => 'year', 
				'table_db' => 'prizes', 'field_db' => 'ncyear',
			   	'type_db'=>'enum', 'value' => $prize['ncyear']);
	   	$Prize['EntryDate']=array('label' => 'date', 
				'table_db' => 'prizes', 'field_db' => 'entrydate',
			   	'type_db'=>'date', 'value' => $prize['entrydate']);
		$Prizes[]=$Prize;
		}
	$Prizes=nullCorrect($Prizes);
	$Student['Prizes']=$Prizes;

/*******Fails****/
	$Fails=array();
	$d_fails=mysql_query("SELECT * FROM fails WHERE student_id='$sid' ORDER BY entrydate DESC");
	while($fail=mysql_fetch_array($d_fails,MYSQL_ASSOC)){
		$fail=nullCorrect($fail);
		$Fail=array();
		$Fail['id_db']=$fail['id'];
	   	$Fail['Subject']=array('label' => 'subject', 
				'table_db' => 'fails', 'field_db' => 'subject_id',
			   	'type_db'=>'varchar(30)', 'value' => $fail['subject_id']);
	   	$Fail['Detail']=array('label' => 'detail','table_db' => 'fails', 'field_db' => 'detail',
					'type_db'=>'varchar(250)', 'value' => $fail['detail']);
	   	$Fail['NCyear']=array('label' => 'year','table_db' => 'fails', 'field_db' => 'ncyear',
					'type_db'=>'enum', 'value' => $fail['ncyear']);
		$Fails[]=$Fail;
		}
	$Fails=nullCorrect($Fails);
	$Student['Fails']=$Fails;
	
/**Finished********/
	return $Student;
}


function fetchComments($sid,$date,$ncyear){
	$Comments=array();
//if no date set choose this academic year
	if($date==''){$date='2005-09-01';}
	if($ncyear==''){$ncyear='%';}
	$d_comments=mysql_query("SELECT * FROM comments WHERE
		student_id='$sid' AND entrydate > '$date' AND ncyear LIKE
		'$ncyear' ORDER BY ncyear DESC, entrydate DESC, id DESC, subject_id");
	while($comment=mysql_fetch_array($d_comments,MYSQL_ASSOC)){
		$comment=nullCorrect($comment);
		$Comment=array();
		$Comment['id_db']=$comment['id'];
		$bid=$comment['subject_id'];
		if($bid!=' '){
			$d_subject=mysql_query("SELECT name FROM subject WHERE id='$bid'");
			$subjectname=mysql_result($d_subject,0);
			}
		else{$subjectname=$bid;}
	   	$Comment['Subject']=array('label' => 'subject',
					'id' => $bid, 'value' => $subjectname);
	   	$Comment['Teacher']=array('label' => 'teacher', 
					 'value' => $comment['teacher_id']);
		$Categories=array();
		$Categories=array('label' => 'category', 
					 'value' => ' ');
		$pairs=explode(';',$comment['category']);
		for($c3=0; $c3<sizeof($pairs)-1; $c3++){
			list($catid, $rank)=split(':',$pairs[$c3]);
			$Category=array();
			$d_categorydef=mysql_query("SELECT name FROM categorydef
				WHERE id='$catid'");
			$catname=mysql_result($d_categorydef,0);
			$Category=array('label' => $catname, 
					 'value' => $catid);
			$Category['rating']=array('value' => $rank);
			$Categories['Category'][]=$Category;
			}
		if (!isset($Categories['Category'])){		
			$Category=array('label' => ' ', 
					'table_db' => 'categorydef', 'field_db' => 'name',
					'type_db'=>'varchar(30)', 'value' => ' ');
			$Categories['Category'][]=$Category;
			}
		$Comment['Categories']=$Categories;
	   	$Comment['Detail']=array('label' => 'detail', 
					'table_db' => 'comments', 'field_db' => 'detail',
					'type_db'=>'varchar(250)', 'value' => $comment['detail']);
	   	$Comment['EntryDate']=array('label' => 'date', 
					'table_db' => 'comments', 'field_db' => 'entrydate',
					'type_db'=>'date', 'value' => $comment['entrydate']);
	   	$Comment['NCyear']=array('label' => 'year', 
					'table_db' => 'comments', 'field_db' => 'ncyear',
					'type_db'=>'enum', 'value' => $comment['ncyear']);
		$Comments[]=$Comment;
		}
	$Comments=nullCorrect($Comments);
	return $Comments;
}

function commentDisplay($sid,$date,$Comments=''){
	$commentdisplay=array();
	if($Comments==''){
		$thisncyear=fetchNCYear($sid);	
		$Comments=fetchComments($sid,$date,$thisncyear);
		}
	if(is_array($Comments)){
			if($Comments[0]['Categories']['Category'][0]['rating']['value']==-1){
				$commentdisplay['class']='negative';
				}
			else{$commentdisplay['class']='positive';}
			$header=$Comments[0]['Subject']['value']. 
								' ('.$Comments[0]['EntryDate']['value'].')';
			$commentdisplay['body']=$header.'<br />'.$Comments[0]['Detail']['value'];
			}
		else{$commentdisplay['class']='';$commentdisplay['body']='';}

	return $commentdisplay;
	}

function fetchNCYear($sid){
	$d_yeargroup=mysql_query("SELECT ncyear FROM yeargroup JOIN
		student ON student.yeargroup_id=yeargroup.id WHERE student.id='$sid'");
	$ncyear=mysql_result($d_yeargroup,0);	
	return $ncyear;
	}
?>
