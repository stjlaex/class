<?php	
/**												lib/fetch_student.php
 *
 *	Retrieves all infobook information about one student using only their sid.
 *	Returns the data in an array $Student and sets it as a session variable.
 *	
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2011
 *	@version	
 *	@since		
 */	


/**
 *
 * This short version avoids building the whole student array in
 * favour of a bare minimum of key fields with the intention of making
 * it more economical. The resulting Student array can not be used
 * when processing forms because it lacks field and table values.
 *
 * @params integer $sid
 * @return array
 */
function fetchStudent_short($sid){
   	$d_student=mysql_query("SELECT * FROM student WHERE id='$sid';");
	$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
   	$d_info=mysql_query("SELECT sen, medical, formerupn, boarder, epfusername, enrolstatus FROM info WHERE student_id='$sid';");
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
	if($student['middlenames']!=''){$displayspace=' ';}else{$displayspace='';}
	if($student['middlenamelast']=='Y'){
		$Student['DisplayFullName']=array('label' => 'fullname',  
										  'value' => $displaypfn. 
										  $student['forename'] . ' ' . $student['surname']
										  . $displayspace . $student['middlenames']);
		$Student['DisplayFullSurname']=array('label' => 'fullname',  
											 'value' => $student['surname'] . 
											 $displayspace. $student['middlenames'] .', '.  
											 $student['forename'] .' '. $displaypfn);
		}
	else{
		$Student['DisplayFullName']=array('label' => 'fullname',  
										  'value' => $displaypfn . 
										  $student['forename'] . $displayspace .$student['middlenames']
										  . ' ' .$student['surname']);
		$Student['DisplayFullSurname']=array('label' => 'fullname',  
										  'value' => $student['surname'] .', '. 
										  $student['forename'] . $displayspace .$student['middlenames']
										  . ' ' .$displaypfn);
		}

	$Student['Gender']=array('label' => 'gender', 
							 'field_db' => 'gender',//Need these for displayenum
							 'type_db' => 'enum',//Need these for displayenum
							 'value' => ''.$student['gender']);
   	$Student['DOB']=array('label' => 'dateofbirth', 
						  'type_db' => 'date',
						  'value' => ''.$student['dob']);

	$Student=array_merge($Student,fetchRegGroup($student));

   	$Student['YearGroup']=array('label' => 'yeargroup', 
								'value' => ''.$student['yeargroup_id']);
   	$Student['EnrolNumber']=array('label' => 'enrolmentnumber',  
								  'value' => ''.$info['formerupn']);
   	$Student['SENFlag']=array('label' => 'seninformation', 
							  'value' => ''.$info['sen']);
   	$Student['MedicalFlag']=array('label' => 'medicalinformation', 
								  'value' => ''.$info['medical']);
   	$Student['Boarder']=array('label' => 'boarder', 
							  'value' => ''.$info['boarder']
							  );
   	$Student['EPFUsername']=array('label' => 'epfusername', 
								  'value' => ''.$info['epfusername']
								  );
   	$Student['EnrolmentStatus']=array('label' => 'enrolstatus', 
									  'value' => ''.$info['enrolstatus']
									  );
	$Student['Photo']['url']='scripts/photo_display.php?sid='.$sid.'&size=maxi';

	return $Student;
	}


/**
 *
 * This is an ad-hoc function for use by student_list only at the moment
 * to quickly get at sid fields outside of the Student array.
 *
 * It returns a fragment of Student which can be folded into an
 * existing Student array with $Student=array_merge($Student,$Studentfield)
 *
 * @params integer $sid
 * @params string $tag
 * @return array
 */
function fetchStudent_singlefield($sid,$tag,$privfilter=''){
	$fieldtype='';
	$separator='';
	$Student=array();
	if($tag=='Nationality'){$fieldname='nationality';$fieldtype='enum';}
	elseif($tag=='SecondNationality'){$fieldname='secondnationality';$fieldtype='enum';}
	elseif($tag=='Birthplace'){$fieldname='birthplace';}
	elseif($tag=='CountryOfOrigin'){$fieldname='countryoforigin';$fieldtype='enum';}
	elseif($tag=='Language'){$fieldname='language';$fieldtype='enum';}
	elseif($tag=='SecondLanguage'){$fieldname='language2';$fieldtype='enum';}
	elseif($tag=='ThirdLanguage'){$fieldname='language3';$fieldtype='enum';}
	elseif($tag=='EnrolNumber'){$fieldname='formerupn';}
	elseif($tag=='EnrolmentNotes'){$fieldname='appnotes';}
	elseif($tag=='EnrolmentApplicationDate'){$fieldname='appdate';}
	elseif($tag=='EnrolmentApplicationMethod'){$fieldname='appmethod';}
	elseif($tag=='EnrolmentPreviousSchool'){$fieldname='previousschool';}
	elseif($tag=='EnrolmentStatus'){$fieldname='enrolstatus';}
	elseif($tag=='EnrolmentLeavingReason'){$fieldname='leavingreason';}
	elseif($tag=='Boarder'){$fieldname='boarder';$fieldtype='enum';}
	elseif($tag=='EntryDate'){$fieldname='entrydate';$fieldtype='date';}
	elseif($tag=='LeavingDate'){$fieldname='leavingdate';$fieldtype='date';}
	elseif($tag=='StaffChild'){$fieldname='staffchild';}
	elseif($tag=='EmailAddress'){$fieldname='email';}
	elseif($tag=='MobilePhone'){$fieldname='phonenumber';}
	elseif($tag=='EPFUsername'){$fieldname='epfusername';}
	elseif($tag=='SENFlag'){$fieldname='sen';}
	elseif($tag=='MedicalFlag'){$fieldname='medical';}
   	elseif($tag=='PersonalNumber'){$fieldname='upn';}
   	elseif($tag=='OtherNumber'){$fieldname='otherpn1';}
   	elseif($tag=='IdExpiryDate'){$fieldname='passportdate';$fieldtype='date';}
   	elseif($tag=='AnotherNumber'){$fieldname='otherpn2';}
   	elseif($tag=='CandidateID'){$fieldname='candidaten1';}
   	elseif($tag=='CandidateNumber'){$fieldname='candidaten2';}
	elseif(substr_count($tag,'FirstContact')){$contactno=0;}
	elseif(substr_count($tag,'SecondContact')){$contactno=1;}
	elseif(substr_count($tag,'ThirdContact')){$contactno=2;}
	elseif(substr_count($tag,'Medical')){$medtype=substr($tag,-3);}
	elseif(substr_count($tag,'Assessment')){$eid=substr($tag,10);}
   	elseif($tag=='Postcode'){
		$d_add=mysql_query("SELECT DISTINCT postcode FROM address JOIN gidaid ON gidaid.address_id=address.id
							WHERE address.postcode!='' AND gidaid.guardian_id=ANY(SELECT guardian_id FROM gidsid 
							WHERE gidsid.student_id='$sid' ORDER BY gidsid.priority ASC);");
		$Student[$tag]=array('label'=>'',
							 'value'=>'');
		while($add=mysql_fetch_array($d_add,MYSQL_ASSOC)){
			$Student[$tag]['value'].=$separator . $add['postcode'];
			$separator=' : ';
			}
		}
   	elseif($tag=='House'){
		$Student[$tag]=array('label'=>'',
							 'value'=>''.get_student_house($sid));
		}
   	elseif($tag=='Transport'){
		$transport=get_student_transport($sid);
		$Student[$tag]=array('label'=>'',
							 'value'=>''.display_student_transport($sid));
		$Student[$tag]['value_db']=$transport['I']['direction'].':::'. $transport['I']['busname'].':::'. $transport['I']['stopname'].':::'. $transport['I']['stoptime'].':::'. $transport['O']['direction'].':::'. $transport['O']['busname'].':::'. $transport['O']['stopname'].':::'. $transport['O']['stoptime'];
		}
   	elseif($tag=='Club'){
		$Student[$tag]=array('label'=>'',
							 'value'=>''.get_student_club($sid));
		}
   	elseif($tag=='Age'){
		$d_s=mysql_query("SELECT dob FROM student WHERE id='$sid';");
		$dob=mysql_result($d_s,0);
		$Student[$tag]=array('label'=>'',
							 'value'=>''.get_age($dob));
		}
	elseif($tag=='EnrolmentYearGroup'){
		$Enrolment=fetchEnrolment($sid);
		$Student[$tag]=array('label'=>'course',
							 'value'=>''.$Enrolment['YearGroup']['value']);
		}
	elseif($tag=='Siblings'){
		$Contacts=(array)fetchContacts($sid);
		$Siblings=array();
		$sibs=array();
		$display='';
		foreach($Contacts as $cindex => $Contact){
			$Dependents=(array)fetchDependents($Contact['id_db']);
			/* Only do siblings who are current students of the school. */
			$Siblings=array_merge($Siblings,$Dependents['Dependents']);
			}
		foreach($Siblings as $Sibling){
			$r=$Sibling['Relationship']['value'];
			if(!in_array($Sibling['Student']['id_db'],$sibs) and $Sibling['Student']['id_db']!=$sid and ($r=='PAF' or $r=='PAM' or $r=='STP')){
				$sibs[]=$Sibling['Student']['id_db'];
				$display.=$Sibling['Student']['DisplayFullName']['value'].' ('.$Sibling['Student']['TutorGroup']['value'].') ';
				}
			}
		$Student[$tag]=array('label'=>'siblings',
							 'no'=>sizeof($sibs),
							 'value'=>''.$display);
		}
   	elseif($tag=='Course'){
		$courses='';
		$display='';
		$crids=list_student_courses($sid);
		foreach($crids as $crid){
			$classes=(array)list_student_course_classes($sid,$crid);
			$courses.=' '.$crid;
			$title='';
			foreach($classes as $c){
				$title.=$c['name'].' - '.$c['teachers'].'<br />';
				}
			$display.='<span style="margin-right:4px;" title="'.$title.'">'.$crid.'</span>';
			}
		$Student[$tag]=array('label'=>'course',
							 'value_db'=>''.$courses,
							 'value'=>''.$display);
		}

	if(isset($contactno)){
		if(substr_count($tag,'Phone')){
			/*
			 * NOT a part of the xml def for Student but useful here.
			 * Important that the contacts are ordered by priority
			 * because contactno as an index for Contacts loses its
			 * meaning otherwise.
			 */
			$Contacts=(array)fetchContacts($sid);
			$Phones=(array)$Contacts[$contactno]['Phones'];
			$Student[$tag]=array('label'=>'',
								 'private'=>'',
								 'value'=>'');
			foreach($Phones as $phoneno => $Phone){
				if(substr_count($tag,'Mobile')>0){
					if($Phone['PhoneType']['value']=='M'){
						$Student[$tag]['value']=$Phone['PhoneNo']['value'];
						$Student[$tag]['private']=$Phone['Private']['value'];
						if($privfilter=='hidden' and $Phone['Private']['value']=='Y'){
							$Student[$tag]['value']='private';
							}
						}
					}
				else{
					if($privfilter=='hidden' and $Phone['Private']['value']=='Y'){
						$Student[$tag]['value'].=$Phone['PhoneType']['value'].':'.'private';
						}
					else{
						$Student[$tag]['value'].=$Phone['PhoneType']['value'].':'.$Phone['PhoneNo']['value'].' ';
						}
					$Student[$tag]['value_db'].=$separator. ' '.$Phone['PhoneNo']['value'];
					$Student[$tag]['private'].=$separator. ''.$Phone['Private']['value'];
					$separator=':::';
					}
				}
			}
		elseif(substr_count($tag,'PostalAddress')){
			/*
			 * NOT a part of the xml def for Student but useful here.
			 */
			$Contacts=(array)fetchContacts($sid);
			$Student[$tag]=array('label'=>'',
								 'value_db'=>'',
								 'value'=>'');
			if(array_key_exists($contactno,$Contacts) and sizeof($Contacts[$contactno]['Addresses'])>0){
				$Add=$Contacts[$contactno]['Addresses'][0];
				if($Contacts[$contactno]['ReceivesMailing']['value']==0){$displayclass='class="lowlite"';}else{$displayclass='';}
				$Student[$tag]['value']='<div '.$displayclass.'>'.$Add['Street']['value'].' '. $Add['Neighbourhood']['value'].' '. $Add['Town']['value'].' '. $Add['Country']['value']. ' '. $Add['Postcode']['value'].'</div>';

				$Student[$tag]['value_db']=$Add['Street']['value'].':::'. $Add['Neighbourhood']['value'].':::'. $Add['Town']['value']. ':::'. $Add['Postcode']['value'].':::'. $Add['Country']['value'];
				$Student[$tag]['private']=$Add['Private']['value'];
				if($Add['Private']['value']=='Y' and $privfilter=='hidden'){
					$Student[$tag]['value']='<div>private</div>';
					}
				}
			}
		elseif(substr_count($tag,'EmailAddress')){
			/*NOT a part of the xml def for Student but useful here*/
			$Contacts=(array)fetchContacts($sid);
			if(array_key_exists($contactno,$Contacts)){
				/* This is to high-light contacts who have requested not to receive mailings. 
				 * The address will not be exported. 
				 */
				if($Contacts[$contactno]['ReceivesMailing']['value']==0){$displayclass='class="lowlite"';$email='';}
				else{$displayclass='';$email=$Contacts[$contactno]['EmailAddress']['value'];}
				$Student[$tag]=array('label'=>'',
									 'private'=>$Contacts[$contactno]['Private']['value'],
									 'value_db'=>$email,
									 'value'=>'<div '.$displayclass.'>'.$Contacts[$contactno]['EmailAddress']['value'].'</div>');
				if($Contacts[$contactno]['Private']['value']=='Y' and $privfilter=='hidden'){
					$Student[$tag]['value']='<div>private</div>';
					}

				}
			else{
				$Student[$tag]=array('label'=>'',
									 'value'=>'');
				}
			}
		elseif(substr_count($tag,'EPFUsername')){
			/*NOT a part of the xml def for Student but useful here*/
			$Contacts=(array)fetchContacts($sid);
			$Student[$tag]=array('label'=>'',
								 'value'=>''.$Contacts[$contactno]['EPFUsername']['value']);
			}
		elseif(substr_count($tag,'Profession')){
			/*NOT a part of the xml def for Student but useful here*/
			$Contacts=(array)fetchContacts($sid);
			$Student[$tag]=array('label'=>'',
								 'value'=>$Contacts[$contactno]['Profession']['value']);
			}
		elseif(substr_count($tag,'Company')){
			$Contacts=(array)fetchContacts($sid);
			$Student[$tag]=array('label'=>'',
								 'value'=>$Contacts[$contactno]['CompanyName']['value']);
			}
		elseif(substr_count($tag,'Note')){
			/*NOT a part of the xml def for Student but useful here*/
			$Contacts=(array)fetchContacts($sid);
			$Student[$tag]=array('label'=>'',
								 'value'=>$Contacts[$contactno]['Note']['value']);
			}
		elseif(substr_count($tag,'Private')){
			/*NOT a part of the xml def for Student but useful here*/
			$Contacts=(array)fetchContacts($sid);
			$Student[$tag]=array('label'=>'',
								 'value'=>$Contacts[$contactno]['Private']['value']);
			}
		elseif(substr_count($tag,'Code')){
			/*NOT a part of the xml def for Student but useful here*/
			$Contacts=(array)fetchContacts($sid);
			$Student[$tag]=array('label'=>'',
								 'value'=>$Contacts[$contactno]['Code']['value']);
			}
		elseif(substr_count($tag,'AddressTitle')){
			$Contacts=(array)fetchContacts($sid);
			$Student[$tag]=array('label'=>'',
								 'value'=>$Contacts[$contactno]['AddressTitle']['value']);
			}
		elseif(substr_count($tag,'Title')){
			/*NOT a part of the xml def for Student but useful here*/
			$Contacts=(array)fetchContacts($sid);
			$Student[$tag]=array('label'=>'',
								 'value'=>get_string(displayEnum($Contacts[$contactno]['Title']['value'], 'title'),'infobook'));
			}
		elseif(substr_count($tag,'Relationship')){
			/*NOT a part of the xml def for Student but useful here*/
			$Contacts=(array)fetchContacts($sid);
			$rel=displayEnum($Contacts[$contactno]['Relationship']['value'], 'relationship'); 
			$mail=displayEnum($Contacts[$contactno]['ReceivesMailing']['value'], 'mailing'); 
			$Student[$tag]=array('label'=>'',
								 'value'=>''.get_string($rel,'infobook'). ' ('.get_string($mail,'infobook').')');
			}
		elseif(substr_count($tag,'Surname')){
			$Contacts=(array)fetchContacts($sid);
			$Student[$tag]=array('label'=>'',
								 'value'=>$Contacts[$contactno]['Surname']['value']);
			}
		elseif(substr_count($tag,'Forename')){
			$Contacts=(array)fetchContacts($sid);
			$Student[$tag]=array('label'=>'',
								 'value'=>$Contacts[$contactno]['Forename']['value']);
			}
		else{
			/*NOT a part of the xml def for Student but useful here*/
			$Contacts=(array)fetchContacts($sid);
			$firstcontact=$Contacts[$contactno]['Forename']['value']. ' '.$Contacts[$contactno]['Surname']['value'];
			$Student[$tag]=array('label'=>'',
								 'value_db'=>$firstcontact,
								 'value'=>''.$firstcontact);
			$Student[$tag]['value']=''.$firstcontact; 
			}
		}
	elseif(isset($medtype)){
		/*NOT a part of the xml def for Student but useful here*/
		$d_b=mysql_query("SELECT detail FROM background WHERE 
				student_id='$sid' AND type='$medtype';");
		$medentry=mysql_result($d_b,0);
		$Student[$tag]=array('label'=>'',
							 'value'=>'');
		$Student[$tag]['value']=''.$medentry; 
		}
	elseif(isset($eid)){
		/*NOT a part of the xml def for Student but useful here*/
		$Assessments=(array)fetchAssessments_short($sid,$eid,'G');		
		if(sizeof($Assessments)>0){
			$value=$Assessments[0]['Result']['value'];
			}
		else{$value='';}
		$Student[$tag]=array('label'=>'',
							 'value'=>'');
		$Student[$tag]['value']=''.$value; 
		}

	/*
	 * The easiest of all because they just come from the info table
	 * and are part of the full Student def anyway. 
	 */
	if(isset($fieldname)){
		$d_info=mysql_query("SELECT $fieldname FROM info WHERE student_id='$sid';");
		$info=mysql_fetch_array($d_info,MYSQL_ASSOC);
		$Student[$tag]=array('label'=>'',
							 'field_db'=>''.$fieldname,
							 'type_db'=>''.$fieldtype,
							 'value'=>''.$info[$fieldname]
							 );
		}
	return $Student;
	}

/**
 *	Student is an xml compliant array designed for use with Serialize
 *	to generate xml from the values in the database. Each value from
 *	the database is stored in an array element identified by its
 *	xmltag. Various useful accompanying attributes are also stored. Of
 *	particular use are Label for displaying the value and _db
 *	attributes facilitating updates to the database when values are
 *	changed (the type_db for instance facilitates validation).
 *
 *	$Student['xmltag']=array('label' => 'Display label', 'field_db' =>
 *				'ClaSSdb field name', 'type_db'=>'ClaSSdb data-type', 
 *					'value' => $student['field_db']);
 *
 *
 * @params integer $sid
 * @return array
 *
 */
function fetchStudent($sid='-1'){
	global $CFG;

   	$d_student=mysql_query("SELECT * FROM student WHERE id='$sid';");
	$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
   	$d_info=mysql_query("SELECT * FROM info WHERE student_id='$sid';");
	$info=mysql_fetch_array($d_info,MYSQL_ASSOC);

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
	if($student['middlenames']!=''){$displayspace=' ';}else{$displayspace='';}
	if($student['middlenamelast']=='Y'){
		$Student['DisplayFullName']=array('label' => 'fullname',  
										  'value' => $displaypfn . 
										  $student['forename'] . ' ' .$student['surname']
										  . $displayspace .$student['middlenames']);
		}
	else{
		$Student['DisplayFullName']=array('label' => 'fullname',  
										  'value' => $displaypfn . 
										  $student['forename'] . $displayspace .$student['middlenames']
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

	$Student=array_merge($Student,fetchRegGroup($student));


   	$Student['YearGroup']=array('label' => 'yeargroup',
   								'table_db' => 'student',
   								'field_db' => 'yeargroup_id',
   								'type_db' => 'smallint(6)',
								'value' => ''.$student['yeargroup_id']);
	/*
	$Student['NCyearActual']=array('label' => 'ncyear',  
								   'id_db' => ''.$student['yeargroup_id'], 
								   'value' => ''.getNCyear($student['yeargroup_id'])
								   );
	*/
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
								  'field_db' => 'medical', 
								  'value' => ''.$info['medical']
								  );
   	$Student['SENFlag']=array('label' => 'seninformation', 
							  'field_db' => 'sen', 
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
   	$Student['SecondLanguage']=array('label' => 'secondlanguage', 
									 'table_db' => 'info', 
									 'field_db' => 'language2',
									 'type_db' => 'enum', 
									 'value' => ''.$info['language2']
									 );
   	$Student['ThirdLanguage']=array('label' => 'thirdlanguage', 
									 'table_db' => 'info', 
									 'field_db' => 'language3',
									 'type_db' => 'enum', 
									 'value' => ''.$info['language3']
									 );
   	$Student['MobilePhone']=array('label' => 'mobilephone',
								  'table_db' => 'info', 
								  'field_db' => 'phonenumber',
								  'type_db' => 'varchar(22)', 
								  'value' => ''.$info['phonenumber']
								  );
   	$Student['EmailAddress']=array('label' => 'email',
								   'table_db' => 'info', 
								   'field_db' => 'email',
								   'type_db' => 'varchar(240)', 
								   'value' => ''.$info['email']
								   );

	if($CFG->enrol_number_generate=='yes'){
		$Student['EnrolNumber']=array('label' => 'enrolmentnumber', 
										'field_db' => 'formerupn', 
										'type_db' =>'char(20)', 
										'value' => ''.$info['formerupn']
										);
		}
	else{
		$Student['EnrolNumber']=array('label' => 'enrolmentnumber', 
										'table_db' => 'info', 
										'field_db' => 'formerupn', 
										'type_db' =>'char(20)', 
										'value' => ''.$info['formerupn']
										);
		}
   	$Student['EnrolmentStatus']=array('label' => 'enrolstatus', 
									  //'table_db' => 'info', 
									  'field_db' => 'enrolstatus', 
									  'type_db' => 'enum', 
									  'value' => ''.$info['enrolstatus']
									  );
	$Student['EntryDate']=array('label' => 'schoolstartdate', 
								'inputtype'=> 'required',
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
   	$Student['PersonalNumber']=array('label' => 'personalnumber', 
									 'table_db' => 'info', 
									 'field_db' => 'upn', 
									 'type_db' => 'char(20)', 
									 'value' => ''.$info['upn']
									 );
   	$Student['OtherNumber']=array('label' => 'othernumber', 
									 'table_db' => 'info', 
									 'field_db' => 'otherpn1', 
									 'type_db' => 'char(20)', 
									 'value' => ''.$info['otherpn1']
									 );
   	$Student['IdExpiryDate']=array('label' => 'expirydate', 
								   'table_db' => 'info', 
								   'field_db' => 'passportdate', 
								   'type_db' => 'date', 
								   'value' => ''.$info['passportdate']
								   );
   	$Student['AnotherNumber']=array('label' => 'anothernumber', 
									 'table_db' => 'info', 
									 'field_db' => 'otherpn2', 
									 'type_db' => 'char(20)', 
									 'value' => ''.$info['otherpn2']
									 );
   	$Student['CandidateID']=array('label' => 'candidateid', 
								  'table_db' => 'info', 
								  'field_db' => 'candidaten1', 
								  'type_db' => 'varchar(40)', 
								  'value' => ''.$info['candidaten1']
								  );
   	$Student['CandidateNumber']=array('label' => 'candidatenumber', 
									  'table_db' => 'info', 
									  'field_db' => 'candidaten2', 
									  'type_db' => 'varchar(40)', 
									  'value' => ''.$info['candidaten2']
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
   	$Student['EnrolmentNotes']=array('label' => 'applicationnotes', 
									 'table_db' => 'info', 
									 'field_db' => 'appnotes',
									 'type_db' => 'text', 
									 'value' => ''.$info['appnotes']
									 );
	$Student['EnrolmentMethod']=array('label' => 'applicationmethod', 
									 'table_db' => 'info', 
									 'field_db' => 'appmethod',
									 'type_db' => 'enum', 
									 'value' => ''.$info['appmethod']
									 );
	if($info['epfusername']==''  and trim($student['forename'])!=''){
		/* If we can set the epfusername now. */
		$info['epfusername']=new_epfusername($Student,'student');
		}
   	$Student['EPFUsername']=array('label' => 'epfusername',
								  //'table_db' => 'info',
								   'field_db' => 'epfusername',
								   'type_db' => 'varchar(128)', 
								   'value' => ''.$info['epfusername']
								   );

	/*******Contacts****/

	$Student['Contacts']=fetchContacts($sid);


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


	if(file_exists($CFG->installpath.'/schoolarrays.php')){include($CFG->installpath.'/schoolarrays.php');}

	return $Student;
	}



/**
 *
 * Returns both the Registration Group and the Tutor Group for a
 * student. Usually these are both the same and are synonymous with
 * the form group. But the Registration Group can be configured to be
 * a different group, either likned to an ad hoc community (type=reg)
 * for that purpose or to the student's House.
 *
 * @params integer $sid
 * @return array
 */
function fetchRegGroup($student){
	global $CFG;

	$sid=$student['id'];
	$tutors=array();

   	$Student['TutorGroup']=array('label' => 'formgroup', 
								 'value' => ''.$student['form_id']
								 );

	if(isset($CFG->regtypes)){
		if(sizeof($CFG->regtypes)>1){
			$secid=get_student_section($sid);
			if(!isset($CFG->regtypes[$secid])){$regtype=$CFG->regtypes[1];}
			else{$regtype=$CFG->regtypes[$secid];}
			}
		else{
			$regtype=$CFG->regtypes[1];
			}
		}
	else{$regtype='form';}

	if($regtype=='form' or $regtype=='year'){
		$reggroup=$student['form_id'];
		}
	elseif($regtype=='house'){
		$house=get_student_house($sid);
		$reggroup=$student['yeargroup_id']. '-'.$house;
		$tutors=array_merge($tutors,list_community_users(array('id'=>'','name'=>$house,'type'=>'house'),array('r'=>1,'w'=>1,'x'=>1),$student['yeargroup_id']));
		}
	elseif($regtype=='reg'){
		$checkcommunity=array('id'=>'','type'=>'reg','name'=>'');
		$regcom=(array)list_member_communities($sid,$checkcommunity);
		if(isset($regcom[0]['name'])){$reggroup=$regcom[0]['name'];}
		else{$reggroup='';}
		}

	$Student['RegistrationGroup']=array('label' => 'formgroup', 
										'value' => ''.$reggroup
										);

	$tutors=array_merge($tutors,list_community_users(array('id'=>'','name'=>$student['form_id'],'type'=>'form'),array('r'=>1,'w'=>1,'x'=>1),$student['yeargroup_id']));

	foreach($tutors as $tutor){
		if($CFG->teachername=='formal'){
			$teachername=get_string(displayEnum($tutor['title'], 'title'),'infobook').' '.$tutor['forename'][0].' '.$tutor['surname'];
			}
		elseif($CFG->teachername=='informal'){
			$teachername=$tutor['forename'];
			}
		else{
			$teachername=get_string(displayEnum($tutor['title'], 'title'),'infobook').' '.$tutor['forename'].' '.$tutor['surname'];
			}

		$Student['RegistrationTutor'][]=array('label' => 'formtutor', 
											  'email' => ''.$tutor['email'],
											  'value' => ''.$teachername
											  );
		}

	return $Student;
	}


/**
 *
 *
 * @params integer $sid
 * @return array
 */
function fetchContacts($sid='-1'){
	$Contacts=array();
	$d_gidsid=mysql_query("SELECT * FROM gidsid WHERE student_id='$sid' ORDER BY priority;");
	while($gidsid=mysql_fetch_array($d_gidsid,MYSQL_ASSOC)){
		$Contacts[]=fetchContact($gidsid);
		}
	return $Contacts;
	}


/**
 *
 *
 * @params integer $sid
 * @return array
 */
function fetchContacts_emails($sid='-1'){
	$Contacts=array();
	$d_gidsid=mysql_query("SELECT * FROM gidsid JOIN guardian ON
				guardian.id=gidsid.guardian_id WHERE guardian.email IS
				NOT NULL AND gidsid.student_id='$sid' AND gidsid.mailing='1';");
	while($gidsid=mysql_fetch_array($d_gidsid,MYSQL_ASSOC)){
		$Contacts[]=fetchContact($gidsid);
		}
	return $Contacts;
	}


/**
 *
 * Returns two arrays one for current students in the school and one for others (alumni, applied, wahtever )
 * the dependents in each will be ordered by DOB from youngest to eldest.
 *
 * @params integer $gid
 * @return array
 */
function fetchDependents($gid='-1'){
	$Dependents=array();
	$Others=array();
	$deps=array();
	$oths=array();

	$d_gidsid=mysql_query("SELECT student_id, priority, mailing, relationship FROM gidsid 
								JOIN student ON student.id=gidsid.student_id WHERE gidsid.guardian_id='$gid' ORDER BY student.dob ASC;");
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
		$EnrolStatus=fetchStudent_singlefield($gidsid['student_id'],'EnrolmentStatus');
		$Dependent['Student']['EnrolmentStatus']['value']=$EnrolStatus['EnrolmentStatus']['value'];
		$dob=$Dependent['Student']['DOB']['value'];
		if($EnrolStatus['EnrolmentStatus']['value']=='C'){
			$Dependents[]=$Dependent;
			$deps[]=$dob;
			}
		else{
			$Others[]=$Dependent;
			$oths[]=$dob;
			}
		}

	if(sizeof($deps>0)){array_multisort($deps, SORT_ASC, $Dependents);}
	if(sizeof($oths>0)){array_multisort($oths, SORT_ASC, $Others);}

	return array('Others'=>$Others,'Dependents'=>$Dependents);
	}


/**
 * Receives a gidsid record and returns full Contact for a sid or a
 * blank Contact for gid=-1,sid=-1. Will be none sid specific if sid
 * is not set (which is used by contact search in the InfoBook)
 *
 * @params array $gidsid
 * @return array
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
										  'default_value' => '1',
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
	/*TODO: remove this for contacts completely
	$Contact['MiddleNames']=array('label' => 'middlenames', 
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
	$Contact['AddressTitle']=array('label' => 'addresstitle', 
							'table_db' => 'guardian', 
							'field_db' => 'addresstitle', 
							'type_db' => 'varchar(120)', 
							'default_value' => '',
							'value' => ''.$guardian['addresstitle']);
	$title=get_string(displayEnum($Contact['Title']['value'], 'title'),'infobook');
	$Contact['DisplayFullName']=array('label' => 'fullname',  
									  'value' => $title .' ' . $guardian['forename'] . ' ' 
									  . ' ' . $guardian['surname']);
    if($Contact['AddressTitle']['value']!=''){
		$Contact['DisplayAddressName']=array('label' => 'labelname',  
											 'value' => ''.$Contact['AddressTitle']['value']);
		}
	else{
		$Contact['DisplayAddressName']=array('label' => 'labelname',
											 'value' => $title. ' ' . $guardian['surname']);
		}
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
   	$Contact['Code']=array('label' => 'contactid', 
						   'table_db' => 'guardian', 
						   'field_db' => 'code', 
						   'type_db'=> 'varchar(120)', 
						   'value' => ''.$guardian['code']);
   	$Contact['Note']=array('label' => 'notes', 
						   //'table_db' => 'guardian', 
						   'field_db' => 'note', 
						   'type_db' => 'text', 
						   'value' => ''.$guardian['note']);
	$Contact['Private']=array('label' => 'private', 
							'table_db' => 'guardian', 
							'field_db' => 'private', 
							'type_db' => 'enum', 
							'default_value' => 'N',
							'value' => ''.$guardian['private']);
	if($guardian['epfusername']=='' and trim($guardian['surname'])!=''){
		/* If we can set the epfusername now. */
		$guardian['epfusername']=new_epfusername($Contact,'contact');
		}
   	$Contact['EPFUsername']=array('label' => 'epfusername', 
								  // 'table_db' => 'guardian', 
								  'field_db' => 'epfusername', 
								  'type_db'=> 'varchar(128)', 
								  'value' => ''.$guardian['epfusername']);


	/*******ContactsAddresses****/
	$Addresses=array();
	$d_gidaid=mysql_query("SELECT * FROM gidaid WHERE guardian_id='$gid' ORDER BY priority;");
	while($gidaid=mysql_fetch_array($d_gidaid,MYSQL_ASSOC)){
		$Addresses[]=fetchAddress($gidaid);
		}
	$Contact['Addresses']=$Addresses;

	/*******ContactsPhones****/
	$Phones=array();
	$d_phone=mysql_query("SELECT * FROM phone WHERE some_id='$gid' ORDER BY phonetype;");
	while($phone=mysql_fetch_array($d_phone,MYSQL_ASSOC)){
		$Phones[]=fetchPhone($phone);
		}
	$Contact['Phones']=$Phones;

	if(file_exists($CFG->installpath.'/schoolarrays.php')){include($CFG->installpath.'/schoolarrays.php');}

	return $Contact;
	}

/**
 *
 *
 * @params array $phone
 * @return array
 */
function fetchPhone($phone=array('id'=>'-1','number'=>'','phonetype'=>'','privatephone'=>'no')){
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
	$Phone['Private']=array('label' => 'private', 
							'table_db' => 'phone', 
							'field_db' => 'privatephone', 
							'type_db' => 'enum', 
							'default_value' => 'N',
							'value' => ''.$phone['privatephone']);
	return $Phone;
	}

/**
 *
 *
 * @params array $gidaid
 * @return array
 */
function fetchAddress($gidaid=array('address_id'=>'-1','addresstype'=>'')){
	$Address=array();
	$aid=$gidaid['address_id'];
	$d_address=mysql_query("SELECT * FROM address WHERE id='$aid';");
	$address=mysql_fetch_array($d_address,MYSQL_ASSOC);
	$Address['id_db']=$aid;
	/*Only makes sense if multiple addresses are implemented
	 $Address['Order']=array('label' => 'priority', 
									'table_db' => 'gidaid', 'field_db' => 'priority',
									'type_db' => 'enum', 'value' => $gidaid['priority']);
	$Address['AddressType']=array('label' => 'type', 
								  //'inputtype'=> 'required',
								  //'table_db' => 'gidaid', 
								  'field_db' => 'addresstype',
								  'type_db' => 'enum', 
								  //'default_value' => 'H',
								  'value' => ''.$gidaid['addresstype']);
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
							 'value_display' => ''.get_string(displayEnum($address['country'], 'country'),'infobook'), 
							 'value' => ''.$address['country']);
	$Address['Postcode']=array('label' => 'postcode',
							   'table_db' => 'address', 
							   'field_db' => 'postcode',
							   'type_db' => 'varchar(8)', 
							   'value' => ''.$address['postcode']);
	$Address['Private']=array('label' => 'private', 
							  'table_db' => 'address', 
							  'field_db' => 'privateaddress', 
							  'type_db' => 'enum', 
							  'default_value' => 'N',
							  'value' => ''.$address['privateaddress']);
	$Address['Latitude']=array('label' => 'latitude',
							   'value' => ''.$address['lat']);
	$Address['Longitude']=array('label' => 'longitude',
							   'value' => ''.$address['lng']);
	return $Address;
	}

/**
 * Incidents
 *
 *
 * @params integer $sid
 * @params date $startdate
 * @params date $enddate
 * @return array
 */
function fetchIncidents($sid,$startdate='',$enddate=''){
	$Incidents=array();
	$Incidents['Incident']=array();
	/*if no startdate set choose this academic year*/
	if($startdate==''){$startdate=get_curriculumyear()-2;}
	if($enddate==''){$enddate=date('Y-m-d');}
	$d_incidents=mysql_query("SELECT * FROM incidents WHERE
		student_id='$sid' AND entrydate >= '$startdate' AND entrydate <= '$enddate'
			ORDER BY entrydate DESC;");
	while($incident=mysql_fetch_array($d_incidents,MYSQL_ASSOC)){
		$Incidents['Incident'][]=fetchIncident($incident);
		}
	return $Incidents;
	}




/**
 *
 *
 * @params array $incident
 * @return array
 */
function fetchIncident($incident){
	$Incident=array();

	if($incident['id']>-1 and (!isset($incident['student_id']) or $incident['student_id']<1)){
		$id=$incident['id'];
		$d_c=mysql_query("SELECT * FROM incidents WHERE id='$id';");
		$incident=mysql_fetch_array($d_c,MYSQL_ASSOC);
		}

	$incid=$incident['id'];
	$Incident=array();
	$Incident['id_db']=$incid;
	list($ratingnames,$catdefs)=fetch_categorydefs('inc');
	$pairs=explode(';',$incident['category']);
	list($catid, $rank)=explode(':',$pairs[0]);
	if(array_key_exists($catid,$catdefs)){$sanction=$catdefs[$catid]['name'];}
	else{$sanction='';}
	$Incident['Sanction']=array('label' => 'sanction', 
								'type_db' => 'varchar(30)', 
								'value_db' => ''.$catid,
								'value' => ''.$sanction);
	$Incident['Detail']=array('label' => 'detail', 
							  'type_db' => 'text', 
							  'value' => ''.$incident['detail']);
	$Incident['Subject']=array('label' => 'subject', 
							   'type_db' => 'varchar(10)', 
							   'value_db' => ''.$incident['subject_id'],
							   'value' => ''.get_subjectname($incident['subject_id']));
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
		list($catid, $rank)=explode(':',$pairs[0]);
		$d_categorydef=mysql_query("SELECT name FROM categorydef
						WHERE id='$catid'");
		$Action['Sanction']=array('label' => 'sanction', 
								  'value_db' => ''.$catid,
								  'value' => ''.mysql_result($d_categorydef,0));
		$Actions['Action'][]=$Action;
		}
	if(sizeof($Actions)==0){$Actions='';}
	$Incident['Actions']=$Actions;
	
	return $Incident;
	}




/**
 * All backgrounds for this sid.
 *
 *
 * @params integer $sid
 * @return array
 */
function fetchBackgrounds($sid='-1'){
	$Backgrounds=array();
	$d_catdef=mysql_query("SELECT name AS tagname, subtype AS background_type FROM categorydef 
							WHERE type='ent' ORDER BY rating;");
	while($back=mysql_fetch_array($d_catdef,MYSQL_ASSOC)){
		$type=$back['background_type'];
		$tagname=ucfirst($back['tagname']);
		$Backgrounds["$tagname"]=fetchBackgrounds_Entries($sid,$type);
		}
	return $Backgrounds;
	}




/**
 * All of the background entries of one type for this sid.
 *
 *
 * @params integer $sid
 * @params string $type
 * @return array
 */
function fetchBackgrounds_Entries($sid,$type){
	$Entries=array();
	$d_background=mysql_query("SELECT * FROM background WHERE 
				student_id='$sid' AND type='$type' ORDER BY entrydate DESC, yeargroup_id DESC, id;");
	while($entry=mysql_fetch_array($d_background,MYSQL_ASSOC)){
		$Entries[]=fetchBackgrounds_Entry($entry);
		}
	return $Entries;
	}



/**
 *
 *
 * @params array $ent
 * @return array
 */
function fetchBackgrounds_Entry($entry){
	$Entry=array();

	if($entry['id']>-1 and (!isset($entry['student_id']) or $entry['student_id']<1)){
		$id=$entry['id'];
		$d_c=mysql_query("SELECT * FROM background WHERE id='$id';");
		$entry=mysql_fetch_array($d_c,MYSQL_ASSOC);
		}

	$Entry=array();
	$Entry['id_db']=$entry['id'];
	$Entry['Teacher']=array('label' => 'teacher', 
							'field_db' => 'teacher_id', 
							'type_db' => 'varchar(14)', 
							'username' => ''.$entry['teacher_id'], 
							'value' => ''.get_teachername($entry['teacher_id']));
	$Categories=array();
	$Categories=array('label' => 'category', 
					  'field_db' => 'category', 
					  'type_db' => 'varchar(100)', 
					  'value_db' => ''.$entry['category'],
					  'value' => ' ');
	$pairs=explode(';',$entry['category']);
	for($c3=0; $c3<sizeof($pairs)-1; $c3++){
		list($catid, $rank)=explode(':',$pairs[$c3]);
		$Category=array();
		$d_categorydef=mysql_query("SELECT name FROM categorydef WHERE id='$catid'");
		if(mysql_num_rows($d_categorydef)>0){
			$catname=mysql_result($d_categorydef,0);
			}
		else{$catname='';}
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
							  'field_db' => 'entrydate', 
							  'type_db' => 'date', 
							  'value' => ''.$entry['entrydate']);
	unset($comment_html);
	$comment_html['div'][]=$entry['detail'];
	
	$Entry['Detail']=array('label' => 'details', 
						   'field_db' => 'detail', 
						   'type_db'=> 'text', 
						   'value'=> $comment_html,
						   'value_db' => ''.$entry['detail']);
	$bid=$entry['subject_id'];
	$subjectname=get_subjectname($bid);
	$Entry['Subject']=array('label' => 'subject', 
							'field_db' => 'subject_id', 
							'type_db' => 'varchar(15)', 
							'value_db' => ''.$bid, 
							'value' => ''.$subjectname);
	$Entry['YearGroup']=array('label' => 'yeargroup', 
							  'field_db' => 'yeargroup_id', 
							  'type_db' => 'smallint', 
							  'value' => ''.$entry['yeargroup_id']);
	return $Entry;
	}






/**
 * Returns all targets for a given $sid. Limited to their current section by default.
 *
 */
function fetchTargets($sid,$secid=''){
	$Targets=array();
	$Targets['Target']=array();

	if($secid==''){$secid=get_student_section($sid);}

	$d_catdef=mysql_query("SELECT name, subtype, rating FROM categorydef WHERE 
				type='tar' AND section_id LIKE '$secid' ORDER BY rating DESC, name;");
	while($cat=mysql_fetch_array($d_catdef,MYSQL_ASSOC)){
		$cattype=$cat['subtype'];
		$d_background=mysql_query("SELECT * FROM background WHERE 
				student_id='$sid' AND type='$cattype' AND (category='' OR category='0') ORDER BY entrydate DESC, id;");
		$entry=mysql_fetch_array($d_background,MYSQL_ASSOC);

			$Entry=array();
			$Entry['id_db']=$entry['id'];
			$Entry['Teacher']=array('label' => 'teacher', 
									'field_db' => 'teacher_id', 
									'type_db' => 'varchar(14)', 
									'username' => ''.$entry['teacher_id'], 
									'value' => ''.get_teachername($entry['teacher_id']));
			$Entry['Category']=array('label' => 'category', 
									 'value_db' => ''.$cattype,
									 'value' => ''.$cat['name']);
			$Entry['EntryDate']=array('label' => 'date',
									  'field_db' => 'entrydate', 
									  'type_db' => 'date', 
									  'value' => ''.$entry['entrydate']);
			$Entry['Success']=array('label' => 'success',
									'field_db' => 'category', 
									'type_db' => 'varchar', 
									'value' => ''.$entry['category']);
			unset($comment_html);
			$comment_html['div'][]=$entry['detail'];

			$Entry['Detail']=array('label' => 'details', 
								   'field_db' => 'detail', 
								   'type_db'=> 'text', 
								   'value'=> $comment_html,
								   'value_db' => ''.$entry['detail']);
			$Entry['YearGroup']=array('label' => 'yeargroup', 
									  'field_db' => 'yeargroup_id', 
									  'type_db' => 'smallint', 
									  'value' => ''.$entry['yeargroup_id']);
			$Targets['Target'][]=$Entry;

		}

	return $Targets;
	}



/**
 * Returns all targets for a given $sid. Limited to their current section by default.
 *
 */
function fetchPreviousTargets($sid){
	$Targets=array();
	$Targets['Target']=array();
	$entids=array();

	$d_b=mysql_query("SELECT DISTINCT b.id, b.teacher_id, b.detail, b.entrydate, b.category, b.yeargroup_id,
						  c.type AS cattype, c.name AS catname FROM background AS b JOIN categorydef AS c ON c.subtype=b.type
						  WHERE b.student_id='$sid' AND (b.category='1' OR b.category='2') ORDER BY b.entrydate DESC, b.id;");

	while($entry=mysql_fetch_array($d_b,MYSQL_ASSOC)){

		if(!in_array($entry['id'],$entids)){
			$entids[]=$entry['id'];
			$Entry=array();
			$Entry['id_db']=$entry['id'];
			$Entry['Teacher']=array('label' => 'teacher', 
									'field_db' => 'teacher_id', 
									'type_db' => 'varchar(14)', 
									'username' => ''.$entry['teacher_id'], 
									'value' => ''.get_teachername($entry['teacher_id']));
			$Entry['Category']=array('label' => 'category', 
									 'value_db' => ''.$entry['cattype'],
									 'value' => ''.$entry['catname']);
			$Entry['EntryDate']=array('label' => 'date',
									  'field_db' => 'entrydate', 
									  'type_db' => 'date', 
									  'value' => ''.$entry['entrydate']);
			$Entry['Success']=array('label' => 'success',
									'field_db' => 'category', 
									'type_db' => 'varchar', 
									'value' => ''.$entry['category']);
			unset($comment_html);
			$comment_html['div'][]=$entry['detail'];
			
			$Entry['Detail']=array('label' => 'details', 
								   'field_db' => 'detail', 
								   'type_db'=> 'text', 
								   'value'=> $comment_html,
								   'value_db' => ''.$entry['detail']);
			$Entry['YearGroup']=array('label' => 'yeargroup', 
									  'field_db' => 'yeargroup_id', 
									  'type_db' => 'smallint', 
									  'value' => ''.$entry['yeargroup_id']);
			$Targets['Target'][]=$Entry;
			}
		}

	return $Targets;
	}



/**
 *
 *
 * @params integer $sid
 * @params date $startdate
 * @params date $enddate
 * @return array
 */
function fetchComments($sid,$startdate='',$enddate=''){
	$Comments=array();
	$subtable=array();
	/*if no startdate set choose this academic year*/
	if($startdate==''){$startdate=get_curriculumyear()-2;}
	if($enddate==''){$enddate=date('Y-m-d');}
	$Comments['Enddate']=array('label' => 'enddate',
							   'value' => ''.$enddate);
	$Comments['Startdate']=array('label' => 'startdate',
								 'value' => ''.$startdate);
	$d_comments=mysql_query("SELECT * FROM comments WHERE
			student_id='$sid' AND entrydate>='$startdate' AND entrydate<='$enddate' AND eidsid_id='0'
			ORDER BY yeargroup_id DESC, entrydate DESC, id DESC, subject_id");
	while($comment=mysql_fetch_array($d_comments,MYSQL_ASSOC)){
		$subtable[$comment['subject_id']]=get_subjectname($comment['subject_id']);
		$Comments['Comment'][]=fetchComment($comment);
		}

	/*The subtable is needed by the xsl templates for sorting*/
	$Comments['subtable']['subject']=array();
	while(list($bid,$name)=each($subtable)){
		$Comments['subtable']['subject'][]=array('value_db'=>$bid,'value'=>$name);
		}

	return $Comments;
	}


/**
 *
 *
 * @params array $comment
 * @return array
 */
function fetchComment($comment){
	$Comment=array();

	if($comment['id']>-1 and (!isset($comment['student_id']) or $comment['student_id']<1)){
		$id=$comment['id'];
		$d_c=mysql_query("SELECT * FROM comments WHERE id='$id';");
		$comment=mysql_fetch_array($d_c,MYSQL_ASSOC);
		}


	$Comment=array();
	$Comment['id_db']=$comment['id'];
	if($comment['incident_id']>0){$Comment['incident_id_db']=$comment['incident_id'];}
	if($comment['merit_id']>0){$Comment['merit_id_db']=$comment['merit_id'];}
	$Comment['Subject']=array('label' => 'subject',
							  'field_db' => 'subject_id', 
							  'value_db' => ''.$comment['subject_id'], 
							  'value' => ''.get_subjectname($comment['subject_id']));
	$tid=$comment['teacher_id'];
	$Comment['Teacher']=array('label' => 'teacher', 
							  'field_db' => 'teacher_id', 
							  'username' => ''.$tid, 
							  'value' => ''.get_teachername($tid));
	$Categories=array();
	$Categories=array('label' => 'category', 
					  'field_db' => 'category', 
					  'type_db' => 'varchar(100)', 
					  'value_db' => ''.$comment['category'],
					  'value' => ' ');
	$pairs=explode(';',$comment['category']);
	for($c3=0;$c3<sizeof($pairs)-1;$c3++){
		list($catid,$rank)=explode(':',$pairs[$c3]);
		$Category=array();
		$d_categorydef=mysql_query("SELECT name FROM categorydef WHERE id='$catid';");
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
							 'field_db' => 'detail', 
							 'type_db' => 'text', 
							 'value' => ''.$comment['detail']);
	$Comment['EntryDate']=array('label' => 'date', 
								'field_db' => 'entrydate', 
								'type_db' => 'date', 
								'value' => ''.$comment['entrydate']);
	$Comment['YearGroup']=array('label' => 'yeargroup', 
								'field_db' => 'yeargroup_id', 
								'type_db' => 'smallint', 
								'value' => ''.$comment['yeargroup_id']);
	$Comment['Shared']=array('label' => 'shared', 
							 'field_db' => 'guardians', 
							 'type_db' => 'enum', 
							 'value' => ''.$comment['guardians']);

	return $Comment;
	}


/**
 * 
 *
 *
 *
 * @params integer $sid
 * @params date $date
 * @params array $Comments
 * @return array
 */
function comment_display($sid,$date='',$Comments=''){
	$commentdisplay=array();
	if($date==''){
		/* Search the last ten days. */
		$date=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-10,date('Y')));
		}
	if($Comments==''){
		$Comments=fetchComments($sid,$date);
		}
	if(array_key_exists('Comment',$Comments)){
		$freshdate=explode('-',$Comments['Comment'][0]['EntryDate']['value']);
		$diff=mktime(0,0,0,date('m'),date('d'),date('Y')) - mktime(0,0,0,$freshdate[1],$freshdate[2],$freshdate[0]);
		if(round($diff/(60*60*24))<2){$commentdisplay['class']='checked';}	
		else{$commentdisplay['class']='';}
		if($Comments['Comment'][0]['Categories']['Category'][0]['rating']['value']==-1){
			$commentdisplay['class'].=' negative';
			}
		elseif($Comments['Comment'][0]['Categories']['Category'][0]['rating']['value']==1){
			$commentdisplay['class'].=' positive';
			}
		else{$commentdisplay['class'].=' neutral';}
		$header=$Comments['Comment'][0]['Subject']['value']. 
			' ('.$Comments['Comment'][0]['EntryDate']['value'].')';
		$commentdisplay['body']=$header.'<br />'.$Comments['Comment'][0]['Detail']['value'];
		}
	else{$commentdisplay['class']='';$commentdisplay['body']='';}
	
	return $commentdisplay;
	}


/**
 *
 * Only used for tasks specific to application and enrolment and not
 * to be part of the Student array.
 *
 *
 * @params integer $sid
 * @return array
 */
function fetchEnrolment($sid='-1'){
	global $CFG;


	$comid=-1;
   	$d_info=mysql_query("SELECT * FROM info WHERE student_id='$sid';");
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
		foreach($coms as $com){
			if($comtype=='year'){
				$yid=$com['name'];
				$enrolstatus='C';
				/* current roll can only exist for the current year*/
				/* and so is not really logical to have displayed?*/
				$year=get_curriculumyear();
				}
			else{
				list($enrolstatus,$yid)=explode(':',$com['name']);
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
										'type_db' => 'enum', 
										'value' => ''.$enrolstatus
										);
   	$Enrolment['Year']=array('label' => 'enrolmentyear', 
							 //'table_db' => '', 
							 'field_db' => 'year', 
							 'type_db' => 'year', 
							 'value' => ''.$year
							 );
   	$Enrolment['YearGroup']=array('label' => 'yeargroup', 
								  //'table_db' => 'student', 
								  'field_db' => 'yeargroup_id', 
								  'type_db' => 'int', 
								  'value' => ''.$yid
								  );
	$Enrolment['ApplicationDate']=array('label' => 'applicationdate', 
								  'table_db' => 'info', 
								  'field_db' => 'appdate', 
								  'type_db' => 'date', 
								  'value' => ''.$info['appdate']
								  );
	$Enrolment['ApplicationMethod']=array('label' => 'applicationmethod', 
								  'table_db' => 'info', 
								  'field_db' => 'appmethod', 
								  'type_db' => 'enum', 
								  'value' => ''.$info['appmethod']
								  );
   	$Enrolment['ApplicationNotes']=array('label' => 'applicationnotes', 
									   'table_db' => 'info', 
									   'field_db' => 'appnotes',
									   'type_db' => 'text', 
									   'value' => ''.$info['appnotes']
									   );
   	$Enrolment['PreviousSchool']=array('label' => 'previousschool', 
									   'table_db' => 'info', 
									   'field_db' => 'previousschool',
									   'type_db' => 'text', 
									   'value' => ''.$info['previousschool']
									   );
	/* TODO: enable this for using categorydef
   	$Enrolment['ApplicationCategory']=array('label' => 'applicationcategory', 
									   'table_db' => 'info', 
									   'field_db' => 'appcat',
									   'type_db' => 'varchar(240)', 
									   'value' => ''.$info['appcat']
									   );
	*/
	$Enrolment['Siblings']=array('label' => 'siblings', 
								  'table_db' => 'info', 
								  'field_db' => 'siblings', 
								  'type_db' => 'enum', 
								  'value' => ''.$info['siblings']
								  );
	$Enrolment['StaffChild']=array('label' => 'staffchild', 
								  'table_db' => 'info', 
								  'field_db' => 'staffchild', 
								  'type_db' => 'enum', 
								  'value' => ''.$info['staffchild']
								  );
	$Enrolment['EntryDate']=array('label' => 'schoolstartdate', 
								  'inputtype'=> 'required',
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
	$Enrolment['LeavingReason']=array('label' => 'leavingreason', 
									  'table_db' => 'info', 
									  'field_db' => 'leavingreason', 
									  'type_db' => 'text', 
									  'value' => ''.$info['leavingreason']
									  );
	if($CFG->enrol_number_generate=='yes'){
		$Enrolment['EnrolNumber']=array('label' => 'enrolmentnumber', 
										'field_db' => 'formerupn', 
										'type_db' =>'char(20)', 
										'value' => ''.$info['formerupn']
										);
		}
	else{
		$Enrolment['EnrolNumber']=array('label' => 'enrolmentnumber', 
										'table_db' => 'info', 
										'field_db' => 'formerupn', 
										'type_db' =>'char(20)', 
										'value' => ''.$info['formerupn']
										);
		}

	return $Enrolment;
	}

/**
 * Returns all residencial Stays where the dpearture date falls after $date
 * by defualt $date='' and only Stays returned are current or future
 * ones.
 *
 * @params integer $sid
 * @params date $date
 * @return array
 */
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

/**
 *
 *
 * @params array $stay
 * @return array
 */
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


/**
 * These are for compatibility with NCYear field as defined by the CBDS 
 * for state schools in England and Wales - they are needed for 
 * fetchStudent to work - but can otherwise be ignored
 *
 * @params string $sid
 * @return string
 */
function fetchNCYear($sid){
	$d_student=mysql_query("SELECT yeargroup_id FROM student WHERE id='$sid'");
	$yid=mysql_result($d_student,0);
	$ncyear=getNCYear($yid);
	return $ncyear;
	}


/**
 *
 * @params string $yid
 * @return string
 */
function getNCYear($yid){
	$ncyears=array('-2'=>'Pre-Nursery','-1' => 'Nursery', '0' => 'Reception', '1' => '1',
				   '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' =>
				   '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' =>
				   '12', '13' => '13', '14' => '14');	
	if(in_array($yid,$ncyears)){$ncyear=$ncyears[$yid];}
	else{$ncyear='';}
	return $ncyear;
	}



/**
 * Medical
 *
 *
 * @param string $sid
 * @return array
 */
function fetchMedical($sid='-1'){
	$Medical=array();
	$Medical['Notes']=array();
	$Notes['Note']=array();
	$d_catdef=mysql_query("SELECT name, subtype, rating FROM categorydef WHERE 
				type='med' ORDER BY rating DESC, name;");
	while($cat=mysql_fetch_array($d_catdef,MYSQL_ASSOC)){
		$Note=array();
		$cattype=$cat['subtype'];
		$d_background=mysql_query("SELECT * FROM background WHERE 
				student_id='$sid' AND type='$cattype';");
		$entry=mysql_fetch_array($d_background,MYSQL_ASSOC);
		if(!empty($entry['id'])){
			$Note['id_db']=''.$entry['id'];
			}
		else{
			$Note['id_db']=-1;
			}
		$Note['MedicalCategory']=array('label' => 'medicalcategory', 
										'value_db' => ''.$cattype,
										'value' => ''.$cat['name']);
		$Note['MedicalRating']=array('label' => 'medicalcategory', 
										'value_db' => ''.$cattype,
										'value' => ''.$cat['rating']);
		$Note['Signature']=array('label' => 'signature', 
								 'field_db'=> 'teacher_id', 
								  'type_db' => 'varchar(14)', 
								  'value' => ''.$entry['teacher_id']);
		$Note['LastReviewDate']=array('label' => 'lastreviewdate',
									  'field_db'=> 'entrydate', 
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

/**
 * Medical
 *
 *
 * @param string $sid
 * @return array
 */
function fetchMedicalLog($id='-1',$sid='-1',$rowsno='-1'){
	$Medical=array();

	if($sid!='-1'){$where=' WHERE student_id='.$sid.' ';}
	else{$where='';}

	if($sid=='-1' and $id!='-1'){$where=' WHERE id='.$id.' ';}
	elseif($sid!='-1' and $id!='-1'){$where.=' AND id='.$id.' ';}

	if($rowsno!='-1'){$limit=' LIMIT '.$rowsno;}
	else{$limit='';}
	$d_v=mysql_query("SELECT * FROM medical_log $where ORDER BY student_id ASC, date DESC $limit;");
	while($visits=mysql_fetch_array($d_v,MYSQL_ASSOC)){
		$Logs=array();
		$Log=array();

		$Log['id_db']=''.$visits['id'];

		$Log['StudentId']=array('label' => 'studentid', 
									'field_db'=> 'student_id', 
									'value_db' => ''.$visits['student_id'],
									'value' => ''.$visits['student_id']);
		$Log['Date']=array('label' => 'date', 
										'value_db' => ''.$visits['date'],
										'type_db' => 'date',
										'value' => ''.$visits['date']);
		$Log['Details']=array('label' => 'details', 
									'type_db' => 'text',
									'value_db' => ''.$visits['details'],
									'value' => ''.$visits['details']);
		$Log['Time']=array('label' => 'time', 
								 'field_db'=> 'time', 
								  'type_db' => 'time', 
								  'value' => ''.$visits['time']);
		$Log['Category']=array('label' => 'category',
									'field_db'=> 'category', 
									'type_db' => 'varchar(10)', 
									'value' => ''.$visits['category']);
		$Log['UserId']=array('label' => 'userid', 
							   'field_db'=> 'user_id', 
							   'type_db'=> 'varchar(14)', 
							   'value' => ''.$visits['user_id']);
		$Logs[]=$Log;
		}
	$Medical=$Logs;
	return $Medical;
	}


/**
 *
 *
 * @param string $sid
 * @param integer $year
 * @return array
 */
function fetchHouseMeritsTotal($house,$year=''){
	$Total=array();
	if($year==''){$year=get_curriculumyear();}
	$d_c=mysql_query("SELECT id FROM community WHERE name='$house' AND type='house';");
	$comid=mysql_result($d_c,0);
   	$d_m=mysql_query("SELECT SUM(value) FROM merits JOIN comidsid ON comidsid.student_id=merits.student_id WHERE merits.year='$year' AND comidsid.community_id='$comid';");
	$sum=mysql_result($d_m,0);

	//$Total['Negative']=array('value'=>''.$negno);
	//$Total['Positive']=array('value'=>''.$posno);

	$Total['Sum']=array('value'=>''.$sum);
	return $Total;
	}


/**
 *
 *
 * @param string $sid
 * @param integer $year
 * @return array
 */
function fetchMeritsTotal($sid,$year){
	$Total=array();

   	$d_m=mysql_query("SELECT COUNT(id) FROM merits WHERE student_id='$sid' AND year='$year' AND value='-1';");
	$negno=mysql_result($d_m,0);
   	$d_m=mysql_query("SELECT COUNT(id) FROM merits WHERE student_id='$sid' AND year='$year' AND value>'0' AND result!='Commendation';");
	$posno=mysql_result($d_m,0);
   	$d_m=mysql_query("SELECT SUM(value) FROM merits WHERE student_id='$sid' AND year='$year' AND result!='Commendation';");
	$sum=mysql_result($d_m,0);

	/* Commendations have the special value of 4 and counted per term*/
	$date1=$year.'-01-01';
	$date2=$year.'-04-01';
	$date3=$year.'-08-01';
   	$d_m=mysql_query("SELECT COUNT(id) FROM merits WHERE student_id='$sid' AND year='$year' AND result='Commendation' AND date<'$date1';");
	$com1=mysql_result($d_m,0);
   	$d_m=mysql_query("SELECT COUNT(id) FROM merits WHERE student_id='$sid' AND year='$year' AND result='Commendation' AND date>='$date1'  AND date<'$date2';");
	$com2=mysql_result($d_m,0);
   	$d_m=mysql_query("SELECT COUNT(id) FROM merits WHERE student_id='$sid' AND year='$year' AND result='Commendation' AND date>='$date2' AND date<'$date3';");
	$com3=mysql_result($d_m,0);
	//trigger_error($comno,E_USER_WARNING);

	$Total['Commendations']=array('value1'=>''.$com1,'value2'=>''.$com2,'value3'=>''.$com3,);
	$Total['Negative']=array('value'=>''.$negno);
	$Total['Positive']=array('value'=>''.$posno);
	$Total['Sum']=array('value'=>''.$sum);
	return $Total;
	}

/**
 *
 *
 *
 * @param string $sid
 * @param integer $limit
 * @param string $bid
 * @param string $pid
 * @param integer $year
 * @return array
 */
function fetchMerits($sid,$limit=-1,$bid='%',$pid='%',$year='0000'){
	if($bid==' ' or $bid==''){$bid='%';}
	if($pid==' ' or $pid==''){$pid='%';}

	$Merits=array();
	$Total=fetchMeritsTotal($sid,$year);
	$Merits['Total']=$Total;

   	$d_m=mysql_query("SELECT * FROM merits WHERE student_id='$sid' AND year='$year' AND
				subject_id LIKE '$bid' AND component_id LIKE '$pid' ORDER BY date DESC LIMIT $limit;");
  	while($m=mysql_fetch_array($d_m,MYSQL_ASSOC)){
		/* Don't print commendations as they get special certificate of their own. */
		if($m['result']!='Commendation'){
			$Merits['Merit'][]=fetchMerit($m);
			}
		}

	return $Merits;
	}

/**
 *
 * @param array $m
 * @return array
 */
function fetchMerit($m=array('id'=>-1,'subject_id'=>'','component_id'=>'','result'=>'','value'=>'','activity'=>'','detail'=>'','year'=>'','date'=>'','teacher_id'=>'','corevalue'=>'')){


	if($m['id']>-1 and (!isset($m['student_id']) or $m['student_id']<1)){
		$id=$m['id'];
		$d_m=mysql_query("SELECT * FROM merits WHERE id='$id';");
		if(mysql_numrows($d_m)>0){$m=mysql_fetch_array($d_m,MYSQL_ASSOC);}
		}

	$Merit=array();
	$Merit['id_db']=$m['id'];
	if(mysql_numrows($d_m)==0){$Merit['exists']='false';}
	else{$Merit['exists']='true';}
	if($m['subject_id']=='%'){$subject='';}
	else{$subject=$m['subject_id'];}
	if($m['component_id']=='%'){$component='';}
	else{$component=$m['component_id'];}
	$Merit['Subject']=array('label'=>'',
							//'table_db'=>'merits',
						   'field_db'=>'subject_id',
						   'type_db'=>'',
						   'value'=>''.$subject);
	$Merit['Component']=array('label'=>'', 
							  //'table_db'=>'merits', 
							  'field_db'=>'component_id',
							  'type_db'=>'',
							  'value'=>''.$component);
	list($ratingnames,$catdefs)=fetch_categorydefs('mer');
	if(array_key_exists($m['activity'],$catdefs)){$activity=$catdefs[$m['activity']]['name'];}
	else{$activity='';}
	$Merit['Activity']=array('label'=>'activity', 
							 'table_db'=>'merits', 
							 'field_db'=>'activity',
							 'type_db'=>'enum',
							 'value_db' => ''.$m['activity'],
							 'value'=>''.$activity);
	list($ratingnames,$catdefs)=fetch_categorydefs('cor');
	if(array_key_exists($m['core_value'],$catdefs)){$corevalue=$catdefs[$m['core_value']]['name'];}
	else{$corevalue='';}
	$Merit['CoreValue']=array('label'=>'corevalue', 
							 'table_db'=>'merits', 
							 'field_db'=>'core_value',
							 'type_db'=>'enum',
							 'value_db' => ''.$m['core_value'],
							 'value'=>''.$corevalue);
	$Merit['Points']=array('label'=>'points', 
						   'table_db'=>'merits', 
						   'field_db'=>'result',
						   'type_db'=>'varchar(30)',
						   'value_db' =>''.$m['value'],
						   'value'=>''.$m['result']);
	$Merit['Detail']=array('label'=>'detail', 
						   'table_db'=>'detail', 
						   'field_db'=>'detail',
						   'type_db'=>'text',
						   'value'=>''.$m['detail']);
	$Merit['Year']=array('label'=>'year', 
						 'field_db'=>'year',
						 'type_db'=>'year',
						 'value'=>''.$m['year']);
	$Merit['Teacher']=array('username'=>''.$m['teacher_id'],
							'label'=>'teacher',
							'field_db'=>'teacher', 
							'type_db'=>'varchar(14)', 
							'value'=>''.get_teachername($m['teacher_id']));
	$Merit['Date']=array('label'=>'date', 
						 //'table_db'=>'merits', 
						 'field_db'=>'date', 
						 'type_db'=>'date', 
						 'value'=>''.$m['date']);
						 $Merit=$Merit;
	return $Merit;
	}


/**
 * Optional for schools with house systems.
 *
 * For a given sid returns the name of the house to which they belong
 * (and they can only be in one house).
 *
 * @param string $sid
 * @return string
 */
function get_student_house($sid){
	$house='';
	$housecommunity=array('id'=>'','type'=>'house','name'=>'');
	$coms=(array)list_member_communities($sid,$housecommunity);
	foreach($coms as $com){
		$house=$com['name'];
		}
	return $house;
	}


/**
 * Returns AM and PM transport for a given date
 *
 * @param string $sid
 * @return string
 */
function get_student_transport($sid,$todate=''){
	global $CFG;

	$transport=array('I'=>'','O'=>'');
	if($todate==''){
		$todate=date('Y-m-d');
		$today=date('N');
		}

	$bookings=(array)list_student_journey_bookings($sid,$todate,$today);
	foreach($bookings as $booking){
		$bus=get_bus($booking['bus_id']);
		$stops=list_bus_stops($booking['bus_id']);
		if(array_key_exists($booking['stop_id'],$stops)){$stop=$stops[$booking['stop_id']];}
		else{$stop=array('name'=>'');}
		if($transport[$bus['direction']]==''){
			$transport[$bus['direction']]=array('busname'=>$bus['name'],'direction'=>$bus['direction'],'stopname'=>$stop['name'],'stoptime'=>$stop['departuretime']);
			}
		}

	return $transport;
	}


/**
 * Returns AM and PM transport for a given date
 *
 * @param string $sid
 * @return string
 */
function display_student_transport($sid,$todate=''){
	global $CFG;
	$display='';

	$transport=(array)get_student_transport($sid,$todate);

	$divin='';$divout='';
	foreach($transport as $direction => $journey){
		if(array_key_exists('busname',$journey)){
			if($direction=='I'){$divname='divin';$divclass='AM';}
			else{$divname='divout';$divclass='PM';}
			$$divname.='<div>'.$journey['busname'].' ('.$divclass.'):'.$journey['stopname'].'</div>';
			}
		}

	return $divin. ' '. $divout;
	}


/**
 *
 * @param string $sid
 * @return string
 */
function get_student_club($sid){
	$club='';
	$ccom=array('id'=>'','type'=>'tutor','name'=>'');
	$coms=(array)list_member_communities($sid,$ccom);
	foreach($coms as $com){
		$club.=$com['name'].'<br />';
		}
	return $club;
	}



/**
 * Will list contacts sharing the address identified by aid.
 *
 * @param integer $sid
 * @return array
 */
function list_address_guardians($aid){
	$guardians=array();
	$d_gidaid=mysql_query("SELECT * FROM gidaid WHERE address_id='$aid'");
	while($gidaid=mysql_fetch_array($d_gidaid,MYSQL_ASSOC)){
		$gid=$gidaid['guardian_id'];
		$d_guardian=mysql_query("SELECT * FROM guardian WHERE id='$gid'");
		$guardians[]=mysql_fetch_array($d_guardian,MYSQL_ASSOC);
		}
	return $guardians;
	}


/**
 * Returns the approximate age in years and months given dob.
 *
 * @param date $dob
 * @return string
 */
function get_age($dob){

	$dob_bits=explode('-',$dob);

	if($dob_bits[0]>0){
		/* WARNING: stupidly this needs month-day-year !!!!*/
		$start_date=gregoriantojd($dob_bits[1], $dob_bits[2], $dob_bits[0]);
		$end_date=gregoriantojd(date('m'), date('d'), date('Y'));

		$days=$end_date - $start_date;
		$years=$days/365;
		$months=$years*12-floor($years)*12;
		}
	else{
		$years=0;$months=0;
		}

	return floor($years).' / '.floor($months);
	}

/**
 * Fuzzy student search
 */
function search_student_fulltext($searchstring,$formid='',$yeargroup='',$table='student'){
	/*It requires Perl Combinatorics*/
	require_once 'Math/Combinatorics.php';
	/*Sets locale to utf8 for last option in strings comparation (accented letters)*/
	setlocale(LC_ALL, "en_GB.UTF-8");
	if($searchstring!=''){
		/*Add yeargroup or formgroup to the mysql query*/
		if($yeargroup!=''){$yearg=" yeargroup_id LIKE '$yeargroup' ";}
		if($formid!=''){
			$d_f=mysql_query("SELECT name FROM community WHERE id='$formid';");
			$formgroup=mysql_result($d_f,0);
			$formg=" form_id LIKE '$formgroup' ";
			}
		if($formid!='' and $yeargroup==''){$query=' AND '.$formg;}
		elseif($formid=='' and $yeargroup!=''){$query=' AND '.$yearg;}
		elseif($formid!='' and $yeargroup!=''){$query=' AND ('.$formg.' OR '.$yearg.')';}
		else{$query='';}
		$d_s=mysql_query("SELECT student_id,surname,forename,middlenames,preferredforename,
							form_id,yeargroup_id,formerupn,epfusername
							FROM student JOIN info ON student.id=info.student_id 
							WHERE form_id NOT LIKE '%alumni%' AND form_id!='' $query 
							ORDER BY surname, forename, student_id;");
		/*$d_g=mysql_query("SELECT DISTINCT guardian_id,surname,forename,middlenames,epfusername,relationship,student_id
							FROM guardian JOIN gidsid ON guardian.id=gidsid.guardian_id
							ORDER BY student_id ASC, guardian_id ASC;");
		  $keys=array("surname","forename","relationship");
							*/
		/*$d_a=mysql_query("SELECT DISTINCT gidsid.guardian_id,address_id,street,neighbourhood,country,postcode,student_id
							FROM address, gidaid, gidsid 
							WHERE address.id=gidaid.address_id AND gidaid.guardian_id=gidsid.guardian_id
							ORDER BY student_id ASC, gidsid.guardian_id ASC;");
		  $keys=array("street","neighbourhood","country","postcode");
							*/
		/*Array for replacing all numbers by string numbers*/
		$nums=array("0"=>"zero"
					,"1"=>"one"
					,"2"=>"two"
					,"3"=>"three"
					,"4"=>"four"
					,"5"=>"five"
					,"6"=>"six"
					,"7"=>"seven"
					,"8"=>"eight"
					,"9"=>"nine"
					);
		if(!preg_match('/(\d+)/', $searchstring) and !is_numeric($searchstring)){
			$array_num=0;
			$keys=array("surname"
						,"forename"
						,"middlenames"
						,"preferredforename"
						);
			}
		/*If there isn't any number in the string it ignores it*/
		elseif(preg_match('/(\d+)/', $searchstring) and !is_numeric($searchstring)){
			$array_num=1;
			$keys=array("surname"
						,"forename"
						,"middlenames"
						,"preferredforename"
						,"form_id"
						//,"yeargroup_id"
						);
			/*foreach($nums as $key=>$num){
				$searchstring=preg_replace('/'.$key.'/', $num, $searchstring, 5);
				}*/
			}
		/*Combinatorics does a full combinations between all keys*/
		$searchstring=trim(strtolower($searchstring));
		$searchstring_transformed=iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$searchstring);
		$combinatorics = new Math_Combinatorics;
		for($i=count($keys);$i>=1;$i--){
			$combinations[]=$combinatorics->permutations($keys, $i);
			}
		while($stds=mysql_fetch_array($d_s,MYSQL_ASSOC)){
			$students[]=$stds;
			}
		/*Starts the search comparing each student's fields combinations*/
		foreach($students as $student){
			/*If the search string is not a number*/
			if(!is_numeric($searchstring)){
				foreach($combinations as $combination){
					foreach($combination as $fields){
						$string='';
						foreach($fields as $field){
							$sub=$student[$field];
							/*Replace numbers in db string*/
							/*if(preg_match('/(\d+)/', $student[$field]) and $array_num==1){
								foreach($nums as $key=>$num){
									$sub=preg_replace('/'.$key.'/', $num, $sub, 5);
									}
								}*/
								$string.=$sub." ";
							}
						/*Compare the distance between search string and db string*/
						$string=trim(strtolower($string));
						$lev=levenshtein($searchstring, $string);
						/*Exact match*/
						if($lev==0){
							$exact_id=$student['student_id'];
							$ids[$exact_id]=$exact_id;
							}
						/*Closest matches*/
						elseif($lev>0 and $lev<25){
							$closest_id=$student['student_id'];
							/*By percentage*/
							$percent=1-levenshtein($searchstring, $string)/max(strlen($searchstring), strlen($string));
							/*More than 70% or the distance is 1 or the plain string is contained in plain searchstring or vice versa*/
							if($percent*100>=70 or $lev==1){
								$closest_ids[$closest_id]=$closest_id;
								$levels[$closest_id]=$lev;
								}
							/*Between 20% and 70%*/
							elseif($percent*100>=20 and $percent*100<70){
								/*The last option is to search if the string is contained in the other*/
								$string_transformed=strtolower(iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$string));
								if(strpos($searchstring_transformed,$string_transformed) or strpos($string_transformed,$searchstring_transformed)){
									$closest_ids2[$student['student_id']]=$student['student_id'];
									$levels2[$closest_id]=$lev;
									}
								/*elseif(preg_match('/'.metaphone($searchstring_transformed).'/',metaphone($string_transformed)) or preg_match('/'.metaphone($string_transformed).'/',metaphone($searchstring_transformed))){
									$closest_ids2[$student['student_id']]=$student['student_id'];
									$levels2[$closest_id]=$lev;
									}*/
								}
							}
						}
					}
				}
			/*If search string is a number it displays the student id or the enrolment number*/
			else{
				$enrol_number=$student['formerupn'];
				$student_id=$student['student_id'];
				$yeargroupid=$student['yeargroup_id'];
				if($enrol_number==$searchstring or $student_id==$searchstring){
					$ids[$student_id]=$student_id;
					}
				elseif($yeargroupid==$searchstring){$ids[$student_id]=$student_id;}
				ksort($ids);
				}
			}
		/*Returns only a specific array of ids if exact matches weren't found and sorts it by levels*/
		if(count($ids)==0 or !isset($ids)){
			//$result[]="No results matched exactly. Similar results listed below (".count($closest_ids).").";
			if(count($closest_ids)!=0){
				array_multisort($levels,SORT_ASC,$closest_ids);
				$ids=$closest_ids;
				}
			elseif(count($closest_ids)==0 and count($closest_ids2)!=0){
				array_multisort($levels2,SORT_ASC,$closest_ids2);
				$ids=$closest_ids2;
				}
			}
		}
	return $ids;
	}



/**
 *
 * Only used by the year end roll over to process transfers from feeder schools.
 * TODO: Its messy. Could do with re-writing.
 * 
 */
function import_student($Student){

	/* Use the student enrolment number to check this student hasn't already been entered. */
	if(!empty($Student['enrolnumber']['value'])){
		$existing_no=$Student['enrolnumber']['value'];
		$d_s=mysql_query("SELECT student_id FROM info WHERE formerupn='$existing_no' AND student_id!='';");
		if(mysql_num_rows($d_s)==1){
			$sid=mysql_result($d_s,0);
			}
		}

	if(!isset($sid)){
		mysql_query("INSERT INTO student SET surname='';");
		$sid=mysql_insert_id();
		mysql_query("INSERT INTO info SET student_id='$sid';");
		}

	$Comments=(array)$Student['comments'];unset($Student['comments']);
	if(!isset($Comments['comment']) or !is_array($Comments['comment'])){
		$Comments['comment']=array();
		}

	$Backgrounds=(array)$Student['backgrounds'];unset($Student['backgrounds']);
	if(!isset($Backgrounds['background']) or !is_array($Backgrounds['background'])){
		$Backgrounds['background']=array();
		}

	$Assessments=(array)$Student['assessments'];unset($Student['assessments']);
	if(!isset($Assessments['assessment']) or !is_array($Assessments['assessment'])){
		$Assessments['assessment']=array();
		}


	$MedNotes=(array)$Student['medical']['notes'];unset($Student['medical']);
	if(!isset($MedNotes['note']) or !is_array($MedNotes['note'])){
		$MedNotes['note']=array();
		}

	$Contacts=(array)$Student['contacts'];unset($Student['contacts']);
	if(!isset($Contacts[0])){$temp=$Contacts;$Contacts=array();$Contacts[]=$temp;unset($temp);}

	while(list($key,$val)=each($Student)){
		if(isset($val['value']) and is_array($val) and isset($val['field_db'])){
			$field=$val['field_db'];
			$inval=clean_text($val['value']);
			if(isset($val['table_db']) and $val['table_db']=='student'){
				mysql_query("UPDATE student SET $field='$inval'	WHERE id='$sid';");
				}
			else{
				mysql_query("UPDATE info SET $field='$inval' WHERE student_id='$sid';");
				}
			}
		}

	/* Transfer teacher comments*/
	if(!isset($Comments['comment'][0]) and isset($Comments['coment']['id_db'])){$temp=$Comments['comment'];$Comments['comment']=array();$Comments['comment'][]=$temp;}
	foreach($Comments['comment'] as $Comment){
		if(is_array($Comment)){
			mysql_query("INSERT INTO comments SET student_id='$sid';");
			$id=mysql_insert_id();
			while(list($key,$val)=each($Comment)){
				if(is_array($val) and isset($val['value']) and isset($val['field_db'])){
					$field=$val['field_db'];
					if(isset($val['value_db'])){
						$inval=$val['value_db'];
						}
					else{
						$inval=$val['value'];
						}
					$inval=clean_text($inval);
					$inval=mysql_real_escape_string($inval);
					mysql_query("UPDATE comments SET $field='$inval' WHERE id='$id';");
					unset($inval);
					}
				}
			$fixcat='';
			$Category=$Comment['categories']['category'];
			$catname=$Category['label'];
			$rank=$Category['rating']['value'];
			$d_cat=mysql_query("SELECT id FROM categorydef WHERE name='$catname' AND type='con';");
			if(mysql_num_rows($d_cat)>0){
				$fixcatid=mysql_result($d_cat,0);
				$fixcat=$fixcatid.':'.$rank.';';
				}
			mysql_query("UPDATE comments SET category='$fixcat' WHERE id='$id';");
			mysql_query("UPDATE comments SET teacher_id='' WHERE id='$id';");
			}
		}

	/* Transfer background entries */
	foreach($Backgrounds as $tagname => $Ents){
		if(is_array($Ents)){
			$d_cat=mysql_query("SELECT subtype FROM categorydef WHERE name='$tagname' AND type='ent';");
			if(mysql_num_rows($d_cat)>0){
				$entcat=mysql_result($d_cat,0);
				}
			else{$entcat='bac';}

			if(!isset($Ents[0]) and isset($Ents['id_db'])){$temp=$Ents;$Ents=array();$Ents[]=$temp;}
			foreach($Ents as $Ent){
				if(is_array($Ent)){
					mysql_query("INSERT INTO background SET student_id='$sid', type='$entcat';");
					$id=mysql_insert_id();
					while(list($entkey,$val)=each($Ent)){
						if(is_array($val) and isset($val['value']) and isset($val['field_db'])){
							$field=$val['field_db'];
							if(isset($val['value_db'])){
								$inval=$val['value_db'];
								}
							else{
								$inval=$val['value'];
								}
							$inval=clean_text($inval);
							mysql_query("UPDATE background SET $field='$inval' WHERE id='$id';");
							unset($inval);
							}
						}
					$fixcat='';
					$Category=$Ent['categories']['category'];
					$catname=$Category['label'];
					$rank=$Category['rating']['value'];
					$d_cat=mysql_query("SELECT id FROM categorydef WHERE name='$catname' AND type='con';");
					if(mysql_num_rows($d_cat)>0){
						$fixcatid=mysql_result($d_cat,0);
						$fixcat=$fixcatid.':'.$rank.';';
						}
					mysql_query("UPDATE background SET category='$fixcat' WHERE id='$id';");
					mysql_query("UPDATE background SET teacher_id='' WHERE id='$id';");
					}
				}
			}
		}


	/* Transfer Assessment results */
	foreach($Assessments as $Assessment){
		if(is_array($Assessment)){

			$element=$Assessment['element']['value'];
			$crid=$Assessment['course']['value'];
			$assyear=$Assessment['year']['value'];
			$stage=$Assessment['stage']['value'];
			$gradingscheme=$Assessment['gradingscheme']['value'];
			$description=$Assessment['description']['value'];

			$d_a=mysql_query("SELECT id FROM assessment WHERE element='$element' AND course_id='$crid' AND year='$assyear' AND grading_name='$gradingscheme';");
			if(mysql_num_rows($d_a)>0){
				$eid=mysql_result($d_a,0);
				$score=array('result'=>$Assessment['result']['value'],'value'=>$Assessment['value']['value'],'date'=>$Assessment['date']['value']);
				update_assessment_score($eid,$sid,$Assessment['subject']['value'],$Assessment['subjectcomponent']['value'],$score);
				}
			else{
				$d_a=mysql_query("SELECT id FROM assessment WHERE description='$description' AND course_id='$crid' AND year='$assyear' AND grading_name='$gradingscheme';");
				if(mysql_num_rows($d_a)>0){
					$eid=mysql_result($d_a,0);
					$score=array('result'=>$Assessment['result']['value'],'value'=>$Assessment['value']['value'],'date'=>$Assessment['date']['value']);
					update_assessment_score($eid,$sid,$Assessment['subject']['value'],$Assessment['subjectcomponent']['value'],$score);
					}
				}
			}
		}


	/* Transfer medical entries */
	if(!isset($MedNotes['note'][0]) and isset($MedNotes['note']['id_db'])){$temp=(array)$MedNotes['note'];$MedNotes['note']=array();$MedNotes['note'][]=$temp;}
	foreach($MedNotes['note'] as $Note){
		if(is_array($Note) and isset($Note['id_db']) and $Note['id_db']>0){
			$medcat=-1;
			$cat=$Note['medicalcategory']['value'];
			$d_cat=mysql_query("SELECT subtype FROM categorydef WHERE name='$cat' AND type='med';");
			if(mysql_num_rows($d_cat)>0){
				$medcat=mysql_result($d_cat,0);
				}


			if($medcat!=-1){
				trigger_error('MED: '.$cat.' : '.$medcat.' :' .$sid,E_USER_WARNING);
				mysql_query("INSERT INTO background SET student_id='$sid', type='$medcat';");
				$id=mysql_insert_id();
				while(list($key,$val)=each($Note)){
					if(is_array($val) and isset($val['value']) and isset($val['field_db'])){
						$field=$val['field_db'];
						if(isset($val['value_db'])){
							$inval=$val['value_db'];
							}
						else{
							$inval=$val['value'];
							}
						$inval=clean_text($inval);
						mysql_query("UPDATE background SET $field='$inval' WHERE id='$id';");
						unset($inval);
						}
					}
				mysql_query("UPDATE background SET teacher_id='' WHERE id='$id';");
				}
			}
		}

	/* Do the contacts */
	foreach($Contacts as $Contact){
		if(isset($Contact['id_db']) and $Contact['id_db']!=-1){
			$epfu=$Contact['epfusername']['value'];
			if($epfu!='' and $epfu!=' '){
				/* Check for existing contact. */
				$d=mysql_query("SELECT id FROM guardian WHERE epfusername='$epfu';");
				if(mysql_num_rows($d)>0){
					$gid=mysql_result($d,0);
					$fresh='no';
					}
				else{
					/* Make sure the epfu goes into new record. */
					$Contact['epfusername']['table_db']='guardian';
					}
				}

			if(!isset($gid)){
				mysql_query("INSERT INTO guardian SET surname='';");
				$gid=mysql_insert_id();
				$fresh='yes';
				}

			/* Link student and guardian. */
			mysql_query("INSERT INTO gidsid SET guardian_id='$gid', student_id='$sid';");
	
			while(list($key,$val)=each($Contact)){
				if(isset($val['value']) and is_array($val) and isset($val['field_db'])){
					$field=$val['field_db'];
					if(isset($val['value_db'])){
						$inval=$val['value_db'];
						}
					else{
						$inval=$val['value'];
						}
					if($val['table_db']=='guardian' and $fresh=='yes'){
						mysql_query("UPDATE guardian SET $field='$inval' WHERE id='$gid'");
						}
					elseif($val['table_db']=='gidsid'){
						mysql_query("UPDATE gidsid SET $field='$inval'
											WHERE guardian_id='$gid' AND student_id='$sid'");
						}
					}
				}
	
			/* Only import new details if the contact does not already exist. */
			if($fresh=='yes'){
				/*All to get around problem with xmlreader!*/
				$Phones=(array)$Contact['phones'];
				if(!isset($Phones[0])){$temp=$Phones;$Phones=array();$Phones[]=$temp;}
				while(list($phoneno,$Phone)=each($Phones)){
					//Don't want the id_db from previous school
					$phoneid=-1;
					while(list($key,$val)=each($Phone)){
						if(isset($val['value']) and is_array($val) and isset($val['field_db'])){	
							$field=$val['field_db'];
							if(isset($val['value_db'])){
								$inval=$val['value_db'];
								}
							else{
								$inval=$val['value'];
								}
							if($phoneid=='-1' and $inval!=''){
								mysql_query("INSERT INTO phone SET some_id='$gid';");
								$phoneid=mysql_insert_id();
								}
							mysql_query("UPDATE phone SET $field='$inval'
												WHERE some_id='$gid' AND id='$phoneid';");
							}
						}
					}

				$Addresses=$Contact['addresses'];
				if(!isset($Addresses[0])){$temp=$Addresses;$Addresses=array();$Addresses[]=$temp;}				
				while(list($addressno,$Address)=each($Addresses)){
					//Don't want the id_db from previous school
					$aid=-1;
					if(is_array($Address)){
						while(list($key,$val)=each($Address)){
							if(isset($val['value']) and is_array($val) and isset($val['field_db'])){
								$field=$val['field_db'];
								if(isset($val['value_db'])){
									$inval=$val['value_db'];
									}
								else{
									$inval=$val['value'];
									}
								if($inval!='' and $aid=='-1'){
									mysql_query("INSERT INTO address SET country='';");
									$aid=mysql_insert_id();
									mysql_query("INSERT INTO gidaid SET guardian_id='$gid', address_id='$aid';");
									}
								if($val['table_db']=='address' and isset($aid)){
									mysql_query("UPDATE address SET $field='$inval'	WHERE id='$aid';");
									}
								elseif($val['table_db']=='gidaid' and isset($aid)){
									mysql_query("UPDATE gidaid SET $field='$inval'
													WHERE guardian_id='$gid' AND address_id='$aid';");
									}
								}
							}
						}
					}
				}
			unset($gid);
			}
		/*End of Contacts*/
		}

	return $sid;
	}

/**
 *
 *
 */
function set_update_event($sid){
	$todate=date('Y-m-d');
	$d_u=mysql_query("SELECT id FROM update_event WHERE student_id='$sid' AND export='0';");
	if(mysql_num_rows($d_u)>0){
		mysql_query("UPDATE update_event SET updatedate='$todate' WHERE student_id='$sid' AND export='0';");
		}
	else{
		mysql_query("INSERT INTO update_event SET export='0', updatedate='$todate',student_id='$sid';");
		}
	}


/**
 *
 * Based on a student date of birth, figure out the likely yeargroup they
 * will join upon enrolment.
 *
 */
function lookup_enrolyear_yid($dob){

	/* Do we start at Nursery or PreNursery for the youngest children.*/
	$d_y=mysql_query("SELECT id FROM yeargroup WHERE id=-2;");
	if(mysql_num_rows($d_y)>0){
		$firstyear=2;//PreNursery
		}
	else{
		$firstyear=3;//Nursery
		}

	/* year-month-day of course */
	$dob_bits=explode('-',$dob);

	/* Try to guess which year group the student might join */
	$enrolyear=get_curriculumyear()+1;

	/* Born after August counts as a year younger */
	if($dob_bits[1]>8){
		$adjustment=1;
		}
	else{
		$adjustment=0;
		}

	$lookup_index=$enrolyear - $dob_bits[0] - 1 - $adjustment;
	while($lookup_index < $firstyear){
		$enrolyear++;
		$lookup_index++;
		}

	$yid_lookups=array('2'=>'-2'
					  ,'3'=>'-1'
					  ,'4'=>'0'
					  ,'5'=>'1'
					  ,'6'=>'2'
					  ,'7'=>'3'
					  ,'8'=>'4'
					  ,'9'=>'5'
					  ,'10'=>'6'
					  ,'11'=>'7'
					  ,'12'=>'8'
					  ,'13'=>'9'
					  ,'14'=>'10'
					  ,'15'=>'11'
					  ,'16'=>'12'
					  ,'17'=>'13'
					  );

	return array('0'=>$enrolyear,'1'=>$yid_lookups[$lookup_index]);
	}

?>
