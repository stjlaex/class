<?php 
/**									   			new_import_action.php
 *
 *	Reads the import file into an array.
 */

//$action='new_import_action1.php';

$action='new_import_list.php';
$action_post_vars=array('sids','news');

include('scripts/sub_action.php');

if($sub=='Submit'){


	include('scripts/file_import_xml.php');

	$inrecords=sizeof($xml['form']['submissions']['submission']);
	$result[]='Succesfully uploaded	'.$inrecords.' records.';

	$sids=array();
	$news=array();
	$date_format='european';//european or us or iso

	/**
	 * This is going to fill out any blank xml array with imported values.
	 */
	function import_xml_read_fields($import,$Current,$fields,$date_format){
		foreach($fields as $studentfield => $inputfield){

			if(is_array($import[$inputfield])){
				foreach($import[$inputfield] as $junk){
					trigger_error('WHAT HAPPENED! '.$inputfield.' = '.$junk,E_USER_WARNING);
					}
				}
			else{
				$value=trim($import[$inputfield]);
				if($value!=''){
					$Current[$studentfield]['value']=checkEntry($value,$Current[$studentfield]['type_db'],$Current[$studentfield]['field_db'],$date_format);
					}
				unset($value);
				}
			}

		return $Current;
		}

	/**
	 * Each table has a mapping between xml tags in the import file
	 * and xml tags in the ClaSS array.
	 */
	$tables=array();
	/* Must cycle through the tables in the following sequence. */
	$tables['student']=array(
							 'Surname'=>'childfamilyname'
							 ,'Forename'=>'firstname'
							 ,'Gender'=>'gender'
							 ,'EntryDate'=>'proposeddateofentry'
							 ,'DOB'=>'dateofbirth'
							 ,'PreferredForename'=>'preferredname'
							 ,'Nationality'=>'nationality'
							 ,'Language'=>'homelanguage'
							 );
	/* usualy the father is contact1 */
	$tables['contact1']=array(
							 'Surname'=>'parentguardianlastname'
							 ,'Forename'=>'parentguardianfirstname'
							 ,'Relationship'=>'relationshiptochild'
							 ,'Profession'=>'occupation'
							 ,'CompanyName'=>'companyname'
							 ,'Relationship'=>'relationshiptochild'
							 ,'EmailAddress'=>'email'
							 );
	/* usualy the mother is contact2 */
	$tables['contact2']=array(
							 'Surname'=>'parentguardianlastname2'
							 ,'Forename'=>'parentguardianfirstname2'
							 ,'Relationship'=>'relationshiptochild2'
							 ,'Profession'=>'occupation2'
							 ,'Relationship'=>'relationshiptochild2'
							 );
	$tables['address1']=array(
							 'Street'=>'street'
							 ,'Town'=>'city'
							 ,'Postcode'=>'postalcode'
							 ,'Country'=>'country'
							 );
	$tables['homephone']=array(
							 'PhoneNo'=>'hometelephone'
							 );
	/* If there is more than one phone then it will need a separate table array for each
	$tables['mobilephone']=array(
							 'PhoneNo'=>'mobiletelephone'
							 );
	*/
	$tables['enrolment']=array(
							   'PreviousSchool'=>'currentschool'
							   ,'ApplicationDate'=>'datesubmitted'
							   ,'ApplicationNotes'=>'howdidyoufindoutaboutthebritishschoolofamsterdam'
							   );

	$blank_Student=(array)fetchStudent();
	$blank_Contact=(array)fetchContact();
	$blank_Address=(array)fetchAddress();
	$blank_Phone=(array)fetchPhone();
	$blank_Enrolment=(array)fetchEnrolment();

	foreach($xml['form']['submissions']['submission'] as $index => $import){

		foreach($tables as $table => $fields){
			if($table=='student'){
				$Current=$blank_Student;
				$Student=(array)import_xml_read_fields($import,$Current,$fields,$date_format);
				}
			elseif(strpos($table,'contact')!==false){
				$Current=$blank_Contact;
				$Contact=(array)import_xml_read_fields($import,$Current,$fields,$date_format);
				$Contact['id_db']=-100;
				$Contact['ReceivesMailing']['value']=1;
				$Student['Contacts'][]=$Contact;
				}
			elseif(strpos($table,'address')!==false){
				$Current=$blank_Address;
				$Address=(array)import_xml_read_fields($import,$Current,$fields,$date_format);
				$Address['id_db']=-100;
				$Student['Contacts'][0]['Addresses'][]=$Address;
				}
			elseif(strpos($table,'enrolment')!==false){
				$Current=$blank_Enrolment;
				/* date submitted is a timestamp so not the same date_format */
				$Enrolment=(array)import_xml_read_fields($import,$Current,$fields,'');
				$Student['PreviousSchool']=$Enrolment['PreviousSchool'];
				$Student['ApplicationNotes']=$Enrolment['ApplicationNotes'];
				$Student['ApplicationDate']=$Enrolment['ApplicationDate'];
				}
			elseif(strpos($table,'phone')!==false){
				$Current=$blank_Phone;
				$Phone=(array)import_xml_read_fields($import,$Current,$fields,$date_format);
				$Phone['id_db']=-100;
				if(strpos($table,'home')!==false){
					$Phone['PhoneType']['value']='H';
					}
				elseif(strpos($table,'mobile')!==false){
					$Phone['PhoneType']['value']='M';
					}
				elseif(strpos($table,'work')!==false){
					$Phone['PhoneType']['value']='W';
					}
				$Student['Contacts'][0]['Phones'][]=$Phone;
				}
			}


		list($sid,$news[])=import_xml_student($Student);
		$sids[]=$sid;

		}
	}




/**
 *
 * Only used by the year end roll over to process transfers from feeder schools.
 * TODO: Its messy. Could do with re-writing.
 * 
 */
function import_xml_student($Student){

	/* Try to identify the student by enrolment number. */
	if(!empty($Student['EnrolNumber']['value'])){
		$existing_no=$Student['EnrolNumber']['value'];
		$d_s=mysql_query("SELECT student_id FROM info WHERE formerupn='$existing_no' AND student_id!='';");
		if(mysql_num_rows($d_s)==1){
			$sid=mysql_result($d_s,0);
			$status='notnew';
			}
		}
	/* Try to identify an existing student by name and date of birth. */
	if(!isset($sid) and !empty($Student['Surname']['value']) and !empty($Student['Forename']['value']) and !empty($Student['DOB']['value'])){
		$existing_surname=$Student['Surname']['value'];
		$existing_forename=$Student['Forename']['value'];
		$existing_dob=$Student['DOB']['value'];
		$d_s=mysql_query("SELECT id FROM student WHERE forename='$existing_forename' 
							AND surname='$existing_surname' AND dob='$existing_dob';");
		if(mysql_num_rows($d_s)==1){
			$sid=mysql_result($d_s,0);
			$status='notnew';
			}
		}

	if(!isset($sid)){
		mysql_query("INSERT INTO student SET surname='';");
		$sid=mysql_insert_id();
		mysql_query("INSERT INTO info SET student_id='$sid';");
		$status='new';
		}

	/* Only update the fields which have a value set - this was a
	   blank Student record prior to import so most stuff is blank. */
	foreach($Student as $key => $val){
		if(isset($val['value']) and is_array($val) and isset($val['field_db'])){
			$field=$val['field_db'];
			$inval=clean_text($val['value']);
			if($inval!=''){
				if(isset($val['table_db']) and $val['table_db']=='student'){
					mysql_query("UPDATE student SET $field='$inval'	WHERE id='$sid';");
					}
				else{
					mysql_query("UPDATE info SET $field='$inval' WHERE student_id='$sid';");
					}
				}
			}
		}

	/* 
	 * Change the enrolment status from Enquired to Applied because
	 * we are importing an application form.
	 */
	if($status=='notnew'){
		$Enrolment=(array)fetchEnrolment($sid);
		if($Enrolment['EnrolmentStatus']['value']=='EN'){
			$enrolstatus='AP';
			$comtype='applied';
			$comname=$enrolstatus.':'.$Enrolment['YearGroup']['value'];
			$community=array('id'=>'','type'=>$comtype,'name'=>$comname,'year'=>$Enrolment['Year']['value']);
			join_community($sid,$community);
			}
		}
	elseif($status=='new'){
		$enrolstatus='AP';
		$comtype='applied';
		list($enrolyear,$yid)=lookup_enrolyear_yid($Student['DOB']['value']);
		$comname=$enrolstatus.':'.$yid;
		$community=array('id'=>'','type'=>$comtype,'name'=>$comname,'year'=>$enrolyear);
		join_community($sid,$community);
		}



	/* Do the contacts */
	$Contacts=(array)$Student['Contacts'];unset($Student['Contacts']);
	if(!isset($Contacts[0])){$temp=$Contacts;$Contacts=array();$Contacts[]=$temp;unset($temp);}
	foreach($Contacts as $Contact){

		$fresh='yes';

		/* -1 would indicate this is a blank record so ignore it. */
		if(isset($Contact['id_db']) and $Contact['id_db']!=-1){

			$epfu=$Contact['EPFUsername']['value'];
			if($epfu!='' and $epfu!=' '){
				/* Check for existing contact. */
				$d=mysql_query("SELECT id FROM guardian WHERE epfusername='$epfu';");
				if(mysql_num_rows($d)==1){
					$gid=mysql_result($d,0);
					$fresh='no';
					}
				else{
					/* Make sure the epfu goes into new record. */
					$Contact['EPFUsername']['table_db']='guardian';
					}
				}

			$email=$Contact['EmailAddress']['value'];
			if($fresh=='yes' and trim($email)!=''){
				/* Check for existing contact. */
				$d=mysql_query("SELECT id FROM guardian WHERE email='$email';");
				if(mysql_num_rows($d)==1){
					$gid=mysql_result($d,0);
					$fresh='no';
					}
				}
			unset($email);

			
			if($fresh=='yes' and trim($Contact['Surname']['value'])!='' and trim($Contact['Forename']['value'])!=''){
				/* Check for existing contact. */
				$surname=good_strtolower(trim($Contact['Surname']['value']));
				$forename=good_strtolower(trim($Contact['Forename']['value']));
				$d=mysql_query("SELECT id FROM guardian WHERE LOWER(surname)='$surname' AND LOWER(forename)='$forename';");
				if(mysql_num_rows($d)==1){
					$gid=mysql_result($d,0);
					$fresh='no';
					}
				unset($surname);unset($forename);
				}



			if(!isset($gid)){
				mysql_query("INSERT INTO guardian SET surname='';");
				$gid=mysql_insert_id();
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
					if($inval!=''){
						if(isset($val['table_db']) and $val['table_db']=='guardian' and $fresh=='yes'){
							mysql_query("UPDATE guardian SET $field='$inval' WHERE id='$gid'");
							}
						elseif(isset($val['table_db']) and $val['table_db']=='gidsid'){
							mysql_query("UPDATE gidsid SET $field='$inval'
											WHERE guardian_id='$gid' AND student_id='$sid'");
							}
						}
					}
				}
	
			/* Only import new details if the contact does not already exist. */
			if($fresh=='yes'){
				/*All to get around problem with xmlreader!*/
				$Phones=(array)$Contact['Phones'];
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

				$Addresses=$Contact['Addresses'];
				if(!isset($Addresses[0])){$temp=$Addresses;$Addresses=array();$Addresses[]=$temp;}
				while(list($addressno,$Address)=each($Addresses)){
					/* Don't want the id_db from previous school */
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

	return array($sid,$status);
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
