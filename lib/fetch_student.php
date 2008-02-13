<?php	
/**												infobook/fetch_student.php
 *
 *	Retrieves all infobook information about one student using only their sid.
 *	Returns the data in an array $Student and sets it as a session variable.
 */	


function fetchStudent_short($sid){
   	$d_student=mysql_query("SELECT * FROM student WHERE id='$sid'");
	$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
   	$d_info=mysql_query("SELECT * FROM info WHERE student_id='$sid'");
	$info=mysql_fetch_array($d_info,MYSQL_ASSOC);

	$Student=array();
	$Student['id_db']=$sid;
	$Student['Surname']=array('label' => 'surname',  
							  'value' => ''.$student['surname']);
	$Student['Forename']=array('label' => 'forename', 
							   'value' => ''.$student['forename']);
   	$Student['MiddleNames']=array('label' => 'middlenames', 
								  'value' => ''.$student['middlenames']);
	$Student['PreferredForename']=array('label' => 'preferredforename',
										'value' => ''.$student['preferredforename']);
	$Student['FormerSurname']=array('label' => 'formersurname', 
									'value' => ''.$student['formersurname']);

	if($student['preferredforename']!=''){$displaypfn='('.$student['preferredforename'].') ';}
	else{$displaypfn='';}
	if($student['middlenamelast']=='Y'){
		$Student['DisplayFullName']=array('label' => 'fullname',  
										  'value' => $displaypfn. 
										  $student['forename'] . ' ' . $student['surname']
										  .' '. $student['middlenames']);
		$Student['DisplayFullSurname']=array('label' => 'fullname',  
											 'value' => $student['surname'] . 
											 ' '. $student['middlenames'] .', '.  
											 $student['forename'] .' '. $displaypfn);
		}
	else{
		$Student['DisplayFullName']=array('label' => 'fullname',  
										  'value' => $displaypfn . 
										  $student['forename'] . ' ' .$student['middlenames']
										  . ' ' .$student['surname']);
		$Student['DisplayFullSurname']=array('label' => 'fullname',  
										  'value' => $student['surname'] .', '. 
										  $student['forename'] . ' ' .$student['middlenames']
										  . ' ' .$displaypfn);
		}

	$Student['Gender']=array('label' => 'gender', 
							 'field_db' => 'gender',
							 'type_db' => 'enum',
							 'value' => ''.$student['gender']);
   	$Student['DOB']=array('label' => 'dateofbirth', 
						  'type_db' => 'date',
						  'value' => ''.$student['dob']);
   	$Student['RegistrationGroup']=array('label' => 'formgroup',  
										'value' => ''.$student['form_id']);
   	$Student['YearGroup']=array('label' => 'yeargroup', 
								'value' => ''.$student['yeargroup_id']);
	return nullCorrect($Student);
	}


function fetchStudent_singlefield($sid,$tag){
	/*this is a ad-hoc function for use by student_list only at the moment*/
	/*to quickly get at sid fields outside of student*/
	$fieldtype='';
	if($tag=='Nationality'){$fieldname='nationality';$fieldtype='enum';}
	elseif($tag=='EnrolNumber'){$fieldname='formerupn';}
	elseif($tag=='Language'){$fieldname='language';$fieldtype='enum';}
	elseif($tag=='Boarder'){$fieldname='boarder';$fieldtype='enum';}
	elseif($tag=='EntryDate'){$fieldname='entrydate';}
	elseif($tag=='EmailAddress'){$fieldname='email';}
	elseif($tag=='MobilePhone'){$fieldname='phonenumber';}
	elseif($tag=='EPFUsername'){$fieldname='epfusername';}
	elseif(substr_count($tag,'FirstContact')){$contactno=0;}
	elseif(substr_count($tag,'SecondContact')){$contactno=1;}

	if(isset($contactno)){
		if(substr_count($tag,'Phone')){
			/*NOT a part of the xml def for Student but useful here*/
			$Contacts=(array)fetchContacts($sid);
			$Phones=(array)$Contacts[$contactno]['Phones'];
			$Student[$tag]=array('label' => '',
								 'value' => '');
			while(list($phoneno,$Phone)=each($Phones)){
				$Student[$tag]['value']=$Student[$tag]['value'] . 
						$Phone['PhoneNo']['value'].' ';				
				}
			}
		elseif(substr_count($tag,'EmailAddress')){
			/*NOT a part of the xml def for Student but useful here*/
			$Contacts=(array)fetchContacts($sid);
			$Student[$tag]=array('label' => '',
								 'value' => $Contacts[$contactno]['EmailAddress']['value']);
			}
		else{
			/*NOT a part of the xml def for Student but useful here*/
			$Contacts=(array)fetchContacts($sid);
			$rel=displayEnum($Contacts[$contactno]['Relationship']['value'], 'relationship'); 
			$rel=get_string($rel,'infobook');
			$firstcontact='('.$rel.') '. 
					$Contacts[$contactno]['Forename']['value']. ' '.$Contacts[$contactno]['Surname']['value'];
			$Student[$tag]=array('label' => '',
								 'value' => '');
			$Student[$tag]['value']=$firstcontact; 
			}
		}

	if(isset($fieldname)){
		$d_info=mysql_query("SELECT $fieldname FROM info WHERE student_id='$sid'");
		$info=mysql_fetch_array($d_info,MYSQL_ASSOC);
		$Student[$tag]=array('label' => '',
							 'field_db' => ''.$fieldname,
							 'type_db' => ''.$fieldtype,
							 'value' => ''.$info[$fieldname]
							 );
		}
	return $Student;
	}


function fetchStudent($sid='-1'){
   	$d_student=mysql_query("SELECT * FROM student WHERE id='$sid'");
	$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
   	$d_info=mysql_query("SELECT * FROM info WHERE student_id='$sid'");
	$info=mysql_fetch_array($d_info,MYSQL_ASSOC);


	/**
	Student is an xml compliant array designed for use with Serialize
	to generate xml from the values in the database. Each value from
	the database is stored in an array element identified by its
	xmltag. Various useful accompanying attributes are also stored. Of
	particular use are Label for displaying the value and _db
	attributes facilitating updates to the database when values are
	changed (the type_db for instance facilitates validation).

	$Student['xmltag']=array('label' => 'Display label', 'field_db' =>
				'ClaSSdb field name', 'type_db'=>'ClaSSdb data-type', 'value' => $student['field_db']);

	*/

	$Student=array();
	$Student['id_db']=$sid;
	$Student['Surname']=array('label' => 'surname', 
							  'inputtype'=> 'required',
							  'table_db' => 'student', 
							  'field_db' => 'surname',
							  'type_db' => 'varchar(120)', 
							  'value' => ''.$student['surname']);
	$Student['Forename']=array('label' => 'forename', 
							   'inputtype'=> 'required',
							   'table_db' => 'student', 
							   'field_db' => 'forename',
							   'type_db' => 'varchar(120)', 
							   'value' => ''.$student['forename']);
   	$Student['MiddleNames']=array('label' => 'middlenames', 
								  'table_db' => 'student', 
								  'field_db' => 'middlenames',
								  'type_db' => 'varchar(120)', 
								  'value' => ''.$student['middlenames']);
	$Student['PreferredForename']=array('label' => 'preferredforename', 
										'table_db' => 'student', 
										'field_db' => 'preferredforename',
										'type_db' => 'varchar(30)', 
										'value' => ''.$student['preferredforename']);
	$Student['FormerSurname']=array('label' => 'formersurname', 
									'table_db' => 'student', 
									'field_db' => 'formersurname',
									'type_db' => 'varchar(120)', 
									'value' => ''.$student['formersurname']);

	if($student['preferredforename']!=''){$displaypfn='('.$student['preferredforename'].') ';}
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

	$Student['Gender']=array('label' => 'gender', 
							 'inputtype'=> 'required',
							 'table_db' => 'student', 
							 'field_db' => 'gender',
							 'type_db' => 'enum', 
							 'value' => ''.$student['gender']
							 );
   	$Student['DOB']=array('label' => 'dateofbirth', 
						  'table_db' => 'student', 
						  'field_db' => 'dob',
						  'type_db' => 'date', 
						  'value' => ''.$student['dob']
						  );

   	$Student['RegistrationGroup']=array('label' => 'formgroup', 
										'value' => ''.$student['form_id']
										);
   	$Student['YearGroup']=array('label' => 'yeargroup',   
								'value' => ''.$student['yeargroup_id']);
	$Student['NCyearActual']=array('label' => 'ncyear',  
								   'id_db' => ''.$student['yeargroup_id'], 
								   'value' => ''.getNCyear($student['yeargroup_id'])
								   );
   	$Student['Nationality']=array('label' => 'nationality', 
								  'table_db' => 'info', 
								  'field_db' => 'nationality', 
								  'type_db' => 'enum', 
								  'value' => ''.$info['nationality']
								  );
   	$Student['SecondNationality']=array('label' => 'secondnationality', 
								  'table_db' => 'info', 
								  'field_db' => 'secondnationality', 
								  'type_db' => 'enum', 
								  'value' => ''.$info['secondnationality']
								  );
   	$Student['Birthplace']=array('label' => 'placeofbirth', 
								 'table_db' => 'info', 
								 'field_db' => 'birthplace', 
								 'type_db' => 'varchar(240)', 
								 'value' => ''.$info['birthplace']
								 );
   	$Student['CountryofOrigin']=array('label' => 'countryoforigin', 
									  'table_db' => 'info', 
									  'field_db' => 'countryoforigin', 
									  'type_db' => 'enum', 
									  'value' =>''.$info['countryoforigin']
									  );
   	$Student['Ethnicity']=array('label' => 'ethnicity', 
								'table_db' => 'info', 
								'field_db' => 'ethnicity', 
								'type_db' => 'enum', 
								'value' =>''.$info['ethnicity']
								);
   	$Student['MedicalFlag']=array('label' => 'medicalinformation', 
								  'value' => ''.$info['medical']
								  );
   	$Student['SENFlag']=array('label' => 'seninformation', 
							  'value' => ''.$info['sen']
							  );
   	$Student['Religion']=array('label' => 'religion', 
							   'table_db' => 'info', 
							   'field_db' => 'religion',
							   'type_db' => 'enum', 
							   'value' => ''.$info['religion']
							   );
   	$Student['Language']=array('label' => 'firstlanguage', 
							   'table_db' => 'info', 
							   'field_db' => 'language',
							   'type_db' => 'enum', 
							   'value' => ''.$info['language']
							   );
   	$Student['MobilePhone']=array('label' => 'mobilephone',
								  'table_db' => 'info', 
								  'field_db' => 'phonenumber',
								  'type_db' => 'varhar(22)', 
								  'value' => ''.$info['phonenumber']
								  );
   	$Student['EmailAddress']=array('label' => 'email',
								   'table_db' => 'info', 
								   'field_db' => 'email',
								   'type_db' => 'varhar(240)', 
								   'value' => ''.$info['email']
								   );
   	$Student['EPFUsername']=array('label' => 'epfusername',
								   'table_db' => 'info', 
								   'field_db' => 'epfusername',
								   'type_db' => 'varhar(128)', 
								   'value' => ''.$info['epfusername']
								   );
   	$Student['EnrolNumber']=array('label' => 'enrolmentnumber', 
								  'table_db' => 'info', 
								  'field_db' => 'formerupn', 
								  'type_db' => 'varchar(13)', 
								  'value' => ''.$info['formerupn']
								  );
   	$Student['EnrolmentStatus']=array('label' => 'enrolstatus', 
									  //'table_db' => 'info', 
								  'field_db' => 'enrolstatus', 
								  'type_db' => 'enum', 
								  'value' => ''.$info['enrolstatus']
								  );
	$Student['EntryDate']=array('label' => 'schoolstartdate', 
								'table_db' => 'info', 
								'field_db' => 'entrydate', 
								'type_db' => 'date', 
								'value' => ''.$info['entrydate']
								);
	$Student['LeavingDate']=array('label' => 'schoolleavingdate', 
								  'field_db' => 'leavingdate', 
								  'type_db' => 'date', 
								  'value' => ''.$info['leavingdate']
								  );
   	$Student['Boarder']=array('label' => 'boarder', 
							  'inputtype'=> 'required',
							  'table_db' => 'info', 
							  'field_db' => 'boarder',
							  'type_db' => 'enum', 
							  'default_value' => 'N',
							  'value' => ''.$info['boarder']
							  );
   	$Student['PartTime']=array('label' => 'parttime', 
							   'table_db' => 'info', 
							   'field_db' => 'parttime',
							   'type_db' => 'enum', 
							   'value' => ''.$info['parttime']
							   );
   	$Student['TransportMode']=array('label' => 'modeoftransport', 
									'table_db' => 'info', 
									'field_db' => 'transportmode',
									'type_db' => 'enum', 
									'value' => ''.$info['transportmode']);
   	$Student['EnrolmentNotes']=array('label' => 'enrolmentnotes', 
									 'table_db' => 'info', 
									 'field_db' => 'enrolnotes',
									 'type_db' => 'text', 
									 'value' => ''.$info['enrolnotes']
									 );


	/*******Contacts****/

	$Student['Contacts']=fetchContacts($sid);


	/*******SEN****/
	$SEN=array();
	$d_senhistory=mysql_query("SELECT * FROM senhistory WHERE 
				student_id='$sid' ORDER BY startdate");
	/*only working for one senhistory per student but could be */
	/*ramped up for more*/

	$SENhistory=array();
	while($senhistory=mysql_fetch_array($d_senhistory,MYSQL_ASSOC)){
		$senhid=$senhistory['id'];
		$SENhistory['id_db']=$senhid;
	   	$SENhistory['SENprovision']=array('label' => 'provision', 
										  'table_db' => 'senhistory', 
										  'field_db' => 'senprovision',
										  'type_db' => 'enum', 
										  'value' => ''.$senhistory['senprovision']);
	   	$SENhistory['SpecialProvisionIndicator']=array('label' => 'specialprovisionindicator', 
										  'type_db' => 'enum', 
										  'value' => '');
	   	$SENhistory['StartDate']=array('label' => 'startdate', 
									   'table_db' => 'senhistory', 
									   'field_db' => 'startdate',
									   'type_db' => 'date', 
									   'value' => ''.$senhistory['startdate']);
	   	$SENhistory['NextReviewDate']=array('label' => 'nextreviewdate', 
											'table_db' => 'senhistory', 
											'field_db' => 'reviewdate',
											'type_db' => 'date', 
											'value' => ''.$senhistory['reviewdate']);

		/*only working for one senhistory_id - in other words no history!*/
		$d_sencurriculum=mysql_query("SELECT * FROM sencurriculum
				WHERE senhistory_id='$senhid' ORDER BY subject_id");
		$Curriculum=array();
		while($sencurriculum=mysql_fetch_array($d_sencurriculum,MYSQL_ASSOC)){
			$Subject=array();
			$bid=$sencurriculum['subject_id'];
			$subjectname=get_subjectname($bid);
		   	$Subject['Subject']=array('label' => 'subject', 
									  'table_db' => 'sencurriculum', 
									  'field_db' => 'subject_id', 
									  'type_db' => 'varchar(10)', 
									  'value_db' => ''.$bid,
									  'value' => ''.$subjectname);
			$Subject['Modification']=array('label' => 'sencurriculum', 
										  'table_db' => 'sencurriculum', 
										  'field_db' => 'curriculum',
										  'type_db' => 'enum', 
										  'value' => ''.$sencurriculum['curriculum']);
			$catid=$sencurriculum['categorydef_id'];
			if($catid!=0){
				$d_categorydef=mysql_query("SELECT name FROM
						categorydef WHERE id='$catid'");
				$catname=mysql_result($d_categorydef,0);
				}
			else{
				$catname='';
				}
		   	$Subject['ExtraSupport']=array('label' => 'extrasupport',
										   'table_db' => 'sencurriculum',
										   'field_db' => 'categorydef_id',
										   'type_db'=> 'int',
										   'value_db' => ''.$catid,
										   'value' => ''.$catname);
			$Subject['Strengths']=array('label' => 'strengths', 
										'table_db' => 'sencurriculum', 
										'field_db' => 'comments', 
										'type_db' => 'text', 
										'value' => ''.$sencurriculum['comments']);
		   	$Subject['Weaknesses']=array('label' => 'weaknesses', 
										 'table_db' => 'sencurriculum', 
										 'field_db' => 'targets', 
										 'type_db' => 'text', 
										 'value' => ''.$sencurriculum['targets']);
		   	$Subject['Strategies']=array('label' => 'strategies',
										 'table_db' => 'sencurriculum', 
										 'field_db' => 'outcome', 
										 'type_db' => 'text', 
										 'value' => ''.$sencurriculum['outcome']);
		   	$Curriculum[]=$Subject;
			}
		$SEN['NCmodifications']=$Curriculum;
		}

	$SEN['SENhistory']=$SENhistory;
	$d_sentypes=mysql_query("SELECT * FROM sentypes WHERE 
					student_id='$sid' ORDER BY senranking");
	$SENtypes=array();
	while($sentypes=mysql_fetch_array($d_sentypes,MYSQL_ASSOC)){
		$SENtype=array();
	   	$SENtype['SENtypeRank']=array('label' => 'ranking',
									  'table_db' => 'sentypes', 
									  'field_db' => 'senranking', 
									  'type_db' => 'enum', 
									  'value' => ''.$sentypes['senranking']);
	   	$SENtype['SENtype']=array('label' => 'sentype', 
								  'table_db' => 'sentypes', 
								  'field_db' => 'sentype', 
								  'type_db' => 'enum', 
								  'value' => ''.$sentypes['sentype']);
		$SENtypes[]=$SENtype;
		}
	$SEN['SENtypes']=$SENtypes;
	$Student['SEN']=$SEN;


	/*******Exclusions****/
	$Exclusions=array();
	$d_exclusions=mysql_query("SELECT * FROM exclusions WHERE 
				student_id='$sid' ORDER BY startdate");
	while($exclusion=mysql_fetch_array($d_exclusions,MYSQL_ASSOC)){
		$Exclusion=array();
	   	$Exclusion['id_db']=$exclusion['startdate'];
	   	$Exclusion['Category']=array('label' => 'category', 
									 'type_db' => 'enum', 
									 'value' => ''.$exclusion['category']);
	   	$Exclusion['StartDate']=array('label' => 'startdate', 
									  'type_db' => 'date', 
									  'value' => ''.$exclusion['startdate']);
	   	$Exclusion['EndDate']=array('label' => 'enddate', 
									'type_db' => 'date', 
									'value' => ''.$exclusion['enddate']);
	   	$Exclusion['Reason']=array('label' => 'reason', 
								   'type_db' => 'varchar(60)', 
								   'value' => ''.$exclusion['reason']);
		$Exclusions[]=$Exclusion;
		}
	$Student['Exclusions']=$Exclusions;


	/*******Backgrounds****/
	$Backgrounds=array();
	$d_catdef=mysql_query("SELECT name AS tagname, 
				subtype AS background_type FROM categorydef WHERE 
				type='ent' ORDER BY rating");
	while($back=mysql_fetch_array($d_catdef,MYSQL_ASSOC)){
		$type=$back['background_type'];
		$tagname=ucfirst($back['tagname']);

		$Entries=array();
		$d_background=mysql_query("SELECT * FROM background WHERE 
				student_id='$sid' AND type='$type' ORDER BY yeargroup_id");
		while($entry=mysql_fetch_array($d_background,MYSQL_ASSOC)){
			$Entry=array();
			$Entry['id_db']=$entry['id'];
			$Entry['Teacher']=array('label' => 'teacher', 
									'type_db' => 'varchar(14)', 
									'value' => ''.$entry['teacher_id']);
			$Categories=array();
			$Categories=array('label' => 'category', 
							  'type_db' => 'varchar(100)', 
							  'value' => ' ');
			$pairs=explode(';',$entry['category']);
			for($c3=0; $c3<sizeof($pairs)-1; $c3++){
				list($catid, $rank)=split(':',$pairs[$c3]);
				$Category=array();
				$d_categorydef=mysql_query("SELECT name FROM categorydef
									WHERE id='$catid'");
				$catname=mysql_result($d_categorydef,0);
				$Category=array('label' => 'category', 
								'type_db'=> 'varchar(30)', 
								'value_db' => ''.$catid,
								'value' => ''.$catname);
				$Category['rating']=array('value' => $rank);
				$Categories['Category'][]=$Category;
				}
			if(!isset($Categories['Category'])){
				$Category=array('label' => 'category', 
								'type_db' => 'varchar(30)', 
								'value_db' => ' ',
								'value' => ' ');
				$Categories['Category'][]=$Category;
				}
			$Entry['Categories']=$Categories;
			$Entry['EntryDate']=array('label' => 'date',
									  'value' => ''.$entry['entrydate']);
			$Entry['Detail']=array('label' => 'details', 
								   'type_db'=> 'text', 
								   'value' => ''.$entry['detail']);
			$bid=$entry['subject_id'];
			$subjectname=get_subjectname($bid);
			$Entry['Subject']=array('label' => 'subject', 
									'type_db' => 'varchar(15)', 
									'value_db' => ''.$bid, 
									'value' => ''.$subjectname);
			$Entry['YearGroup']=array('label' => 'yeargroup', 
									  'type_db' => 'smallint', 
									  'value' => ''.$entry['yeargroup_id']);
			$Entries[]=$Entry;
			}
		$Backgrounds["$tagname"]=$Entries;
		}

	$Student['Backgrounds']=$Backgrounds;

	if(file_exists('../schoolarrays.php')){include('../schoolarrays.php');}

	return $Student;
	}


function fetchContacts($sid='-1'){
	$Contacts=array();
	$d_gidsid=mysql_query("SELECT * FROM gidsid WHERE student_id='$sid' ORDER BY priority");
	while($gidsid=mysql_fetch_array($d_gidsid,MYSQL_ASSOC)){
		$Contacts[]=fetchContact($gidsid);
		}
	return $Contacts;
	}


function fetchContacts_emails($sid='-1'){
	$Contacts=array();
	$d_gidsid=mysql_query("SELECT * FROM gidsid JOIN guardian ON
				guardian.id=gidsid.guardian_id WHERE guardian.email IS
				NOT NULL AND gidsid.student_id='$sid' AND gidsid.mailing='1'");
	while($gidsid=mysql_fetch_array($d_gidsid,MYSQL_ASSOC)){
		$Contacts[]=fetchContact($gidsid);
		}
	return $Contacts;
	}

function fetchDependents($gid='-1'){
	$Dependents=array();
	$d_gidsid=mysql_query("SELECT * FROM gidsid WHERE guardian_id='$gid' ORDER BY priority");
	while($gidsid=mysql_fetch_array($d_gidsid,MYSQL_ASSOC)){
		$Dependent=array();
		$Dependent['id_db']=$gidsid['student_id'];
		$Dependent['Student']=fetchStudent_short($gidsid['student_id']);
		$Dependent['Order']=array('label' => 'priority', 
								  'inputtype'=> 'required', 
								  'table_db' => 'gidsid',
								  'field_db' => 'priority',
								  'type_db' => 'enum', 
								  'value' => ''.$gidsid['priority']);
		$Dependent['ReceivesMailing']=array('label' => 'receivesmailing', 
											'inputtype'=> 'required',
											'table_db' => 'gidsid', 
											'field_db' => 'mailing',
											'type_db' => 'enum', 
											'value' => ''.$gidsid['mailing']);
		$Dependent['Relationship']=array('label' => 'relationship', 
										 'inputtype'=> 'required', 
										 'table_db' => 'gidsid', 
										 'field_db' => 'relationship',
										 'type_db' => 'enum', 
										 'value' => ''.$gidsid['relationship']);
		$Dependents[]=$Dependent;
		}
	return $Dependents;
	}


/* Receives a gidsid record and returns full Contact for a sid or a
 * blank Contact for gid=-1,sid=-1. Will be none sid specific if sid
 * is not set (which is used by contact search in the InfoBook)
 */
function fetchContact($gidsid=array('guardian_id'=>'-1','student_id'=>'-1','priority'=>'','mailing'=>'','relationship'=>'')){
	$gid=$gidsid['guardian_id'];
	$d_guardian=mysql_query("SELECT * FROM guardian WHERE id='$gid';");
	$guardian=mysql_fetch_array($d_guardian,MYSQL_ASSOC);
	$Contact=array();
	$Contact['id_db']=$gid;

	if(isset($gidsid['student_id'])){
		$Contact['Order']=array('label' => 'priority', 
								'inputtype'=> 'required', 
								'table_db' => 'gidsid', 
								'field_db' => 'priority',
								'type_db' => 'enum', 
								'value' => ''.$gidsid['priority']);
		$Contact['ReceivesMailing']=array('label' => 'receivesmailing', 
										  'inputtype'=> 'required',
										  'table_db' => 'gidsid', 
										  'field_db' => 'mailing',
										  'type_db' => 'enum', 
										  'value' => ''.$gidsid['mailing']);
		$Contact['Relationship']=array('label' => 'relationship', 
									  'inputtype'=> 'required',
									   'table_db' => 'gidsid', 
									   'field_db' => 'relationship',
									   'type_db' => 'enum', 
									   'value' => ''.$gidsid['relationship']);
		}
	$Contact['Surname']=array('label' => 'surname', 
							  'inputtype'=> 'required',
							  'table_db' => 'guardian', 
							  'field_db' => 'surname',
							  'type_db' => 'varchar(120)', 
							  'value' => ''.$guardian['surname']);
	$Contact['Forename']=array('label' => 'forename', 
							   'table_db' => 'guardian', 
							   'field_db' => 'forename',
							   'type_db' => 'varchar(120)', 
							   'value' => ''.$guardian['forename']);
	/*	$Contact['MiddleNames']=array('label' => 'middlenames', 
								  'table_db' => 'guardian', 
								  'field_db' => 'middlenames', 
								  'type_db' => 'varchar(30)', 
								  'value' => ''.$guardian['middlenames']);
	*/
	$Contact['Title']=array('label' => 'title', 
							'table_db' => 'guardian', 
							'field_db' => 'title', 
							'type_db' => 'enum', 
							'default_value' => '0',
							'value' => ''.$guardian['title']);
	$Contact['DisplayFullName']=array('label' => 'fullname',  
									  'value' =>
			   					  get_string(displayEnum($Contact['Title']['value'], 'title'),'infobook')
									  .'  ' . $guardian['forename'] . ' ' 
									  .$guardian['middlenames']
									  . ' ' . $guardian['surname']);

	$Contact['EmailAddress']=array('label' => 'email', 
								  'table_db' => 'guardian', 
								  'field_db' => 'email', 
								  'type_db' => 'varchar(240)', 
								  'value' => ''.$guardian['email']);
   	$Contact['Nationality']=array('label' => 'nationality', 
								  'table_db' => 'guardian', 
								  'field_db' => 'nationality', 
								  'type_db' => 'enum', 
								  'value' => ''.$guardian['nationality']);
   	$Contact['Profession']=array('label' => 'profession', 
								  'table_db' => 'guardian', 
								  'field_db' => 'profession', 
								  'type_db'=> 'varchar(120)', 
								  'value' => ''.$guardian['profession']);
   	$Contact['CompanyName']=array('label' => 'nameofcompany', 
								  'table_db' => 'guardian', 
								  'field_db' => 'companyname', 
								  'type_db' => 'varchar(240)', 
								  'value' => ''.$guardian['companyname']);


	/*******ContactsAddresses****/
	$Addresses=array();
	$d_gidaid=mysql_query("SELECT * FROM gidaid WHERE guardian_id='$gid' ORDER BY priority");
	while($gidaid=mysql_fetch_array($d_gidaid,MYSQL_ASSOC)){
		$Addresses[]=fetchAddress($gidaid);
		}
	$Contact['Addresses']=$Addresses;

	/*******ContactsPhones****/
	$Phones=array();
	$d_phone=mysql_query("SELECT * FROM phone WHERE some_id='$gid' ORDER BY phonetype");
	while($phone=mysql_fetch_array($d_phone,MYSQL_ASSOC)){
		$Phones[]=fetchPhone($phone);
		}
	$Contact['Phones']=$Phones;

	return $Contact;
	}


function fetchPhone($phone=array('id'=>'-1','number'=>'','phonetype'=>'')){
	$Phone=array();
	$Phone['id_db']=$phone['id'];
	$Phone['PhoneNo']=array('label' => 'phonenumber', 
							'table_db' => 'phone', 
							'field_db' => 'number',
							'type_db' => 'varchar(22)', 
							'value' => ''.$phone['number']);
	$Phone['PhoneType']=array('label' => 'phonetype', 
							  'table_db' => 'phone', 
							  'field_db' => 'phonetype',
							  'type_db' => 'enum', 
							  'value' => ''.$phone['phonetype']);
	return $Phone;
	}

function fetchAddress($gidaid=array('address_id'=>'-1','addresstype'=>'')){
	$Address=array();
	$aid=$gidaid['address_id'];
	$d_address=mysql_query("SELECT * FROM address WHERE id='$aid'");
	$address=mysql_fetch_array($d_address,MYSQL_ASSOC);
	$Address['id_db']=$aid;
	/*Only makes sense if multiple addresses are implemented
	 $Address['Order']=array('label' => 'priority', 
									'table_db' => 'gidaid', 'field_db' => 'priority',
									'type_db' => 'enum', 'value' => $gidaid['priority']);
	*/
	$Address['AddressType']=array('label' => 'type', 
								  //'inputtype'=> 'required',
								  'table_db' => 'gidaid', 
								  'field_db' => 'addresstype',
								  'type_db' => 'enum', 
								  //'default_value' => 'H',
								  'value' => ''.$gidaid['addresstype']);
	/*Now deprecated...
	$Address['BuildingName']=array('label' => 'building', 
								   'table_db' => 'address', 
								   'field_db' => 'building',
								   'type_db' => 'varchar(60)', 
								   'value' => ''.$address['building']);
	$Address['StreetNo']=array('label' => 'streetno.', 
							   'table_db' => 'address', 
							   'field_db' => 'streetno',
							   'type_db' => 'varchar(10)', 
							   'value' => ''.$address['streetno']);
	*/
	$Address['Street']=array('label' => 'street',
						   'table_db' => 'address', 
						   'field_db' => 'street',
						   'type_db' => 'varchar(160)', 
						   'value' => ''.$address['street']);
	$Address['Neighbourhood']=array('label' => 'neighbourhood',
									'table_db' => 'address', 
									'field_db' => 'neighbourhood',
									'type_db' => 'varchar(160)', 
									'value' => ''.$address['neighbourhood']);
	$Address['Town']=array('label' => 'town/city', 
						   'table_db' => 'address', 
						   'field_db' => 'region',
						   'type_db' => 'varchar(160)', 
						   'value' => ''.$address['region']);
	$Address['Country']=array('label' => 'country', 
							 'table_db' => 'address', 
							 'field_db' => 'country',
							 'type_db' => 'enum',
							  'value_display' => 
							get_string(displayEnum($address['country'], 'country'),'infobook'), 
							 'value' => ''.$address['country']);
	$Address['Postcode']=array('label' => 'postcode',
							   'table_db' => 'address', 
							   'field_db' => 'postcode',
							   'type_db' => 'varchar(8)', 
							   'value' => ''.$address['postcode']);
	return $Address;
	}

/*******Incidents***/
function fetchIncidents($sid){
	$Incidents=array();
	$Incidents['Incident']=array();
	$d_incidents=mysql_query("SELECT * FROM incidents WHERE
		student_id='$sid' ORDER BY entrydate DESC");
	while($incident=mysql_fetch_array($d_incidents,MYSQL_ASSOC)){
		$incid=$incident['id'];
		$Incident=array();
		$Incident['id_db']=$incid;
		$pairs=explode(';',$incident['category']);
		list($catid, $rank)=split(':',$pairs[0]);
		$d_categorydef=mysql_query("SELECT name FROM categorydef
						WHERE id='$catid'");
		$Incident['Sanction']=array('label' => 'sanction', 
								  'type_db' => 'varchar(30)', 
								  'value_db' => ''.$catid,
								  'value' => ''.mysql_result($d_categorydef,0));
	   	$Incident['Detail']=array('label' => 'detail', 
								  'type_db' => 'text', 
								  'value' => ''.$incident['detail']);
	   	$Incident['Subject']=array('label' => 'subject', 
								   'type_db' => 'varchar(10)', 
								   'value' => ''.$incident['subject_id']);
	   	$Incident['Closed']=array('label' => 'closed', 
								   'type_db' => 'enum', 
								   'value' => ''.$incident['closed']);
	   	$Incident['EntryDate']=array('label' => 'date',
									 'type_db' => 'date',
									 'value' => ''.$incident['entrydate']);
	   	$Incident['YearGroup']=array('label' => 'yeargroup', 
									 'type_db' => 'enum', 
									 'value' => ''.$incident['yeargroup_id']);
		$tid=$incident['teacher_id'];
		$Incident['Teacher']=array('username' => ''.$tid,
								   'label' => 'teacher',
								   'value' => ''.get_teachername($tid));

		$d_history=mysql_query("SELECT * FROM incidenthistory
				WHERE incident_id='$incid' ORDER BY entryn");
		$Actions=array();
		while($action=mysql_fetch_array($d_history)){
			$Action=array();
			$Action['no_db']=$action['entryn'];
			$acttid=$action['teacher_id'];

			$Action['Teacher']=array('username' => ''.$acttid, 
									 'label' => 'teacher',
							  'value' => get_teachername($acttid));
			$Action['Comment']=array('value' => ''.$action['comment']);
			$Action['EntryDate']=array('label' => 'date', 
									 'type_db' => 'date', 
									 'value' => ''.$action['entrydate']);
			$pairs=explode(';',$action['category']);
			list($catid, $rank)=split(':',$pairs[0]);
			//trigger_error(''.$action['category']. ' pairs: '.$pairs[0],E_USER_WARNING);
			$d_categorydef=mysql_query("SELECT name FROM categorydef
						WHERE id='$catid'");
			$Action['Sanction']=array('label' => 'sanction', 
							'value_db' => ''.$catid,
							'value' => ''.mysql_result($d_categorydef,0));
			$Actions['Action'][]=nullCorrect($Action);
			}
		$Incident['Actions']=$Actions;
		$Incidents['Incident'][]=$Incident;
		}

	return nullCorrect($Incidents);
	}

function fetchComments($sid,$date=''){
	$Comments=array();
	$subtable=array();
	/*if no date set choose this academic year*/
	$crid='KS3';
	if($date==''){$date=get_curriculumyear($crid)-2;}
	$d_comments=mysql_query("SELECT * FROM comments WHERE
			student_id='$sid' AND entrydate > '$date' 
			ORDER BY yeargroup_id DESC, entrydate DESC, id DESC, subject_id");
	while($comment=mysql_fetch_array($d_comments,MYSQL_ASSOC)){
		$Comment=array();
		$Comment['id_db']=$comment['id'];
		$bid=$comment['subject_id'];
		$subjectname=get_subjectname($bid);
		$subtable[$bid]=$subjectname;
	   	$Comment['Subject']=array('label' => 'subject',
								  'value_db' => ''.$bid, 
								  'value' => ''.$subjectname);

		$tid=$comment['teacher_id'];
		$Comment['Teacher']=array('label' => 'teacher', 
								  'username' => ''.$tid, 
								  'value' => ''.get_teachername($tid));
		$Categories=array();
		$Categories=array('label' => 'category', 
						  'value' => ' ');
		$pairs=explode(';',$comment['category']);
		for($c3=0;$c3<sizeof($pairs)-1;$c3++){
			list($catid, $rank)=split(':',$pairs[$c3]);
			$Category=array();
			$d_categorydef=mysql_query("SELECT name FROM categorydef
				WHERE id='$catid'");
			$catname=mysql_result($d_categorydef,0);
			$Category=array('label' => $catname, 
							'value' => ''.$catid);
			$Category['rating']=array('label'=> 'type',
									  'value' => ''.$rank);
			$Categories['Category'][]=$Category;
			}
		if(!isset($Categories['Category'])){
			$Category=array('label' => ' ', 
							'type_db' => 'varchar(30)', 
							'value' => ' ');
			$Categories['Category'][]=$Category;
			}
		$Comment['Categories']=$Categories;
	   	$Comment['Detail']=array('label' => 'detail', 
								 'type_db' => 'text', 
								 'value' => ''.$comment['detail']);
	   	$Comment['EntryDate']=array('label' => 'date', 
									'type_db' => 'date', 
									'value' => ''.$comment['entrydate']);
	   	$Comment['YearGroup']=array('label' => 'yeargroup', 
									'type_db' => 'smallint', 
									'value' => ''.$comment['yeargroup_id']);
		$Comments['Comment'][]=$Comment;
		}

	$Comments['subtable']['subject']=array();
	while(list($bid,$name)=each($subtable)){$Comments['subtable']['subject'][]=array('value_db'=>$bid,'value'=>$name);}
	return nullCorrect($Comments);
	}

function commentDisplay($sid,$date='',$Comments=''){
	$commentdisplay=array();
	if($date==''){
		$date=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-31,date('Y')));
		}
	if($Comments==''){
		$Comments=fetchComments($sid,$date);
		}
	//	if(is_array($Comments['Comment'])){
	if(array_key_exists('Comment',$Comments)){
			if($Comments['Comment'][0]['Categories']['Category'][0]['rating']['value']==-1){
				$commentdisplay['class']='negative';
				}
			else{$commentdisplay['class']='positive';}
			$header=$Comments['Comment'][0]['Subject']['value']. 
								' ('.$Comments['Comment'][0]['EntryDate']['value'].')';
			$commentdisplay['body']=$header.'<br />'.$Comments['Comment'][0]['Detail']['value'];
			}
		else{$commentdisplay['class']='';$commentdisplay['body']='';}

	return $commentdisplay;
	}

/* Only used for tasks specific to enrolment and not to be part of the */
/* Student array (at least for now!)*/
function fetchEnrolment($sid='-1'){
	$comid=-1;
   	$d_info=mysql_query("SELECT * FROM info WHERE student_id='$sid'");
	if(mysql_num_rows($d_info)>0){
		$info=mysql_fetch_array($d_info,MYSQL_ASSOC);
		if($info['enrolstatus']=='EN'){
			$comtype='enquired';
			}
		elseif($info['enrolstatus']=='AC'){
			$comtype='accepted';
			}
		elseif($info['enrolstatus']=='C'){
			$comtype='year';
			}
		elseif($info['enrolstatus']=='P'){
			$comtype='alumni';
			}
		else{
			$comtype='applied';
			}
		$searchcom=array('id'=>'','name'=>'','type'=>$comtype);
		$coms=(array)list_member_communities($sid,$searchcom);
		while(list($index,$com)=each($coms)){
			if($comtype=='year'){
				$yid=$com['name'];
				$enrolstatus='C';
				/* current roll can only exist for the current year*/
				/* and so is not really logical to have displayed?*/
				$year=get_curriculumyear();
				}
			elseif($comtype=='alumni'){
				$yid=$com['name'];
				$enrolstatus='P';
				$year=$com['year'];
				}
			else{
				list($enrolstatus,$yid)=split(':',$com['name']);
				$year=$com['year'];
				}
			$comid=$com['id'];
			}
		}

	$Enrolment=array();
   	$Enrolment['Community']=array('id_db' => $comid, 
								  //'table_db' => 'info', 
								  //'value' => ''.$enrolstatus
								  );
   	$Enrolment['EnrolmentStatus']=array('label' => 'enrolstatus', 
										//'table_db' => 'info', 
										'field_db' => 'enrolstatus', 
										'type_db'=>'enum', 
										'value' => ''.$enrolstatus
										);
   	$Enrolment['EnrolmentNotes']=array('label' => 'enrolmentnotes', 
									   'table_db' => 'info', 
									   'field_db' => 'enrolnotes',
									   'type_db' => 'text', 
									   'value' => ''.$info['enrolnotes']
									   );
   	$Enrolment['YearGroup']=array('label' => 'yeargroup', 
								  //'table_db' => 'student', 
								  'field_db' => 'yeargroup_id', 
								  'type_db' => 'int', 
								  'value' => ''.$yid
								  );
   	$Enrolment['Year']=array('label' => 'year', 
							 //'table_db' => '', 
							 'field_db' => 'year', 
							 'type_db' => 'year', 
							 'value' => ''.$year
							 );
   	$Enrolment['EnrolNumber']=array('label' => 'enrolmentnumber', 
									'table_db' => 'info', 
									'field_db' => 'formerupn', 
									'type_db' =>'varchar(13)', 
									'value' => ''.$info['formerupn']
									);
	$Enrolment['EntryDate']=array('label' => 'schoolstartdate', 
								  'table_db' => 'info', 
								  'field_db' => 'entrydate', 
								  'type_db' =>'date', 
								  'value' => ''.$info['entrydate']
								  );
	$Enrolment['LeavingDate']=array('label' => 'schoolleavingdate', 
									'table_db' => 'info', 
									'field_db' => 'leavingdate', 
									'type_db' =>'date', 
									'value' => ''.$info['leavingdate']
									);
	return $Enrolment;
	}

/*Returns all residencial Stays where the dpearture date falls after $date*/
/*by defualt $date='' and only Stays returned are current or future ones.*/
function fetchStays($sid,$date=''){
	if($date==''){$todate=date("Y-m-d");}
	$Stays=array();
	$d_stays=mysql_query("SELECT * FROM accomodation WHERE
				student_id='$sid' AND 
				(departuredate>'$date' OR departuredate='0000-00-00') ORDER BY
				arrivaldate DESC, id DESC");
	while($stay=mysql_fetch_array($d_stays,MYSQL_ASSOC)){
		$Stays[]=fetchStay($stay);
		}
	return $Stays;
	}

function fetchStay($stay=array('id'=>'-1','bookingdate'=>'','invoice'=>'')){
	$Stay=array();
	$Stay['id_db']=$stay['id'];
   	$Stay['BookingDate']=array('label' => 'bookingdate', 
							'table_db' => 'accomodation', 
							'field_db' => 'bookingdate', 
							'type_db' => 'date', 
							'value' => ''.$stay['bookingdate']
							);
   	$Stay['Invoice']=array('label' => 'invoice', 
							'table_db' => 'accomodation', 
							'field_db' => 'invoice', 
							'type_db' => 'varchar(80)', 
							'value' => ''.$stay['invoice']
							);
  	$Stay['RoomCategory']=array('label' => 'roomcategory', 
							'table_db' => 'accomodation', 
							'field_db' => 'roomcategory', 
							'type_db'=> 'enum', 
							'value' => ''.$stay['roomcategory']
							);
  	$Stay['ResidenceBuilding']=array('label' => 'residencebuilding', 
							'table_db' => 'accomodation', 
							'field_db' => 'building', 
							'type_db'=> 'enum', 
							'value' => ''.$stay['building']
							);
  	$Stay['ResidenceBed']=array('label' => 'residencebed', 
							'table_db' => 'accomodation', 
							'field_db' => 'bed', 
							'type_db'=> 'varchar(4)', 
							'value' => ''.$stay['bed']
							);
	$Stay['ArrivalDate']=array('label' => 'arrivaldate', 
							'table_db' => 'accomodation', 
							'field_db' => 'arrivaldate', 
							'type_db' => 'date', 
							'value' => ''.$stay['arrivaldate']
							);
	$Stay['ArrivalTime']=array('label' => 'arrivaltime', 
							'table_db' => 'accomodation', 
							'field_db' => 'arrivaltime', 
							'type_db' => 'time', 
							'value' => ''.$stay['arrivaltime']
							);
	$Stay['ArrivalAirport']=array('label' => 'arrivalairport', 
							'table_db' => 'accomodation', 
							'field_db' => 'arrivalairport', 
							'type_db' => 'varchar(240)', 
							'value' => ''.$stay['arrivalairport']
							);
	$Stay['ArrivalFlight']=array('label' => 'arrivalflight', 
							'table_db' => 'accomodation', 
							'field_db' => 'arrivalflight', 
							'type_db' => 'varchar(240)', 
							'value' => ''.$stay['arrivalflight']
							);
	$Stay['DepartureDate']=array('label' => 'departuredate', 
							'table_db' => 'accomodation', 
							'field_db' => 'departuredate', 
							'type_db' => 'date', 
							'value' => ''.$stay['departuredate']
							);
	$Stay['DepartureTime']=array('label' => 'departuretime', 
							'table_db' => 'accomodation', 
							'field_db' => 'departuretime', 
							'type_db' => 'time', 
							'value' => ''.$stay['departuretime']
							);
	$Stay['DepartureAirport']=array('label' => 'departureairport', 
							'table_db' => 'accomodation', 
							'field_db' => 'departureairport', 
							'type_db' => 'varchar(240)', 
							'value' => ''.$stay['departureairport']
							);
	$Stay['DepartureFlight']=array('label' => 'departureflight', 
							'table_db' => 'accomodation', 
							'field_db' => 'departureflight', 
							'type_db' => 'varchar(240)', 
							'value' => ''.$stay['departureflight']
							);
	return $Stay;
	}

/*these are for compatibility with NCYear field as defined by the CBDS */
/*for state schools in England and Wales - they are needed for */
/* fetchStudent to work - but can otherwise be ignored*/
function fetchNCYear($sid){
	$d_student=mysql_query("SELECT yeargroup_id FROM student WHERE id='$sid'");
	$yid=mysql_result($d_student,0);
	$ncyear=getNCYear($yid);
	return $ncyear;
	}

function getNCYear($yid){
	$ncyears=array(''=>'','N' => 'Nursery', 'R' => 'Reception', '1' => '1',
	'2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' =>
	'7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' =>
	'12', '13' => '13', '14' => '14');	
	if(in_array($yid,$ncyears)){$ncyear=$ncyears[$yid];}
	else{$ncyear='';}
	return $ncyear;
	}


/******* Medical ****/
function fetchMedical($sid='-1'){
	$Medical=array();
	$Medical['Notes']=array();
	$Notes['Note']=array();
	$d_catdef=mysql_query("SELECT name, subtype, rating FROM categorydef WHERE 
				type='med' ORDER BY rating DESC, name");
	while($cat=mysql_fetch_array($d_catdef,MYSQL_ASSOC)){
		$Note=array();
		$cattype=$cat['subtype'];
		$d_background=mysql_query("SELECT * FROM background WHERE 
				student_id='$sid' AND type='$cattype';");
		$entry=mysql_fetch_array($d_background,MYSQL_ASSOC);
		$Note['id_db']=''.$entry['id'];
		$Note['MedicalCategory']=array('label' => 'medicalcategory', 
										'value_db' => ''.$cattype,
										'value' => ''.$cat['name']);
		$Note['MedicalRating']=array('label' => 'medicalcategory', 
										'value_db' => ''.$cattype,
										'value' => ''.$cat['rating']);
		$Note['Signature']=array('label' => 'signature', 
								  'type_db' => 'varchar(14)', 
								  'value' => ''.$entry['teacher_id']);
		$Note['LastReviewDate']=array('label' => 'lastreviewdate',
									   'type_db' => 'date', 
									   'value' => ''.$entry['entrydate']);
		$Note['Detail']=array('label' => 'details', 
							   'field_db'=> 'detail', 
							   'type_db'=> 'text', 
							   'value' => ''.$entry['detail']);
		$Notes['Note'][]=$Note;
		}
	$Medical['Notes']=$Notes;
	return $Medical;
	}

/** Returns the epfusername for that sid either from the database if */
/* the eportfolio is configured or generates a usable name based on */
/* the formula but not a unique username suitable for joining the portfolio!.*/
function get_epfusername($sid,$Student=array(),$type='student'){
	$epfusername='';
	$d_info=mysql_query("SELECT epfusername FROM info WHERE student_id='$sid';");
	$epfusername=mysql_result($d_info,0);
	setlocale(LC_CTYPE,'en_GB');
	if($epfusername==''){
		if(sizeof($Student)==0){$Student=fetchStudent_short($sid);}
		$surname=(array)split(' ',$Student['Surname']['value']);
		$start=iconv('UTF-8', 'ASCII//TRANSLIT', $Student['Forename']['value'][0]);
		$tail=iconv('UTF-8', 'ASCII//TRANSLIT', $surname[0]);
		$epfusername=good_strtolower($start. $tail);
		}
	return $epfusername;
	}
?>