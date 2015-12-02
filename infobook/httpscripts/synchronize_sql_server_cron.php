#! /usr/bin/php -q
<?php
/**
 *								httpscripts/synchronize_sql_server_cron.php
 *
 */
$book='infobook';
$current='synchronize_sql_server_cron.php';

/* The path is passed as a command line argument. */
function arguments($argv) {
	$ARGS = array();
	foreach ($argv as $arg) {
		if (ereg('--([^=]+)=(.*)',$arg,$reg)) {
			$ARGS[$reg[1]] = $reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)) {
			$ARGS[$reg[1]] = 'true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);
require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/classnew/scripts/cron_head_options.php');

/*function fetchStudentByFormerUpn($formerUpn){
	$d_info=mysql_query("SELECT student_id FROM info WHERE formerupn='$formerUpn';");

	if($d_info){
		$info=mysql_fetch_array($d_info, MYSQL_ASSOC);
		$sid=$info["student_id"];

		$d_student=mysql_query("SELECT * FROM student WHERE id=$sid;");

		if($d_student){
			$student=mysql_fetch_array($d_student, MYSQL_ASSOC);
			return array_merge($info, $student);
			}
		}

	return null;
	}*/

/*function fetchStudentInfoByFormerUpn($formerUpn){
	$d_info=mysql_query("SELECT * FROM info WHERE formerupn='$formerUpn';");

	if($d_info){
		return mysql_fetch_array($d_info, MYSQL_ASSOC);
		}

	return null;
	}*/

class Extract {
	private $user;
	private $password;
	private $dsn;
	private $connection;

	/*public static $TABLES=array(
		// "STG-LM",
		// "STG-AL",
		// "STG-MA",
		// "STG-MD",
		"STG-SE",
		//"STG-MD-TEST"
		);*/

	function __construct($user, $password, $dsn="mssql"){
		$this->user=$user;
		$this->password=$password;
		$this->dsn=$dsn;
		$this->connection=odbc_connect($this->dsn, $this->user, $this->password);
		}

	/*public function allTables(){
		$tables=array();

		foreach(Extract::$TABLES as $name){
			$tables[$name]=$this->table($name);
			}

		return $tables;
		}*/

	public function table($name){
		$cursor=odbc_exec($this->connection, "select * from \"$name\"");
		$data=array();

		while(odbc_fetch_row($cursor)){
			$columns=array();

			for($i=1; $i<=odbc_num_fields($cursor); $i++){
				$column=odbc_field_name($cursor, $i);
				$columns[$column]=odbc_result($cursor, $i);
				}

			$data[]=$columns;
			}

		return $data;
		}
	}

function getFormId($courseCode) {
	$formId="";
	$splittedCourse=split(" ", $courseCode);

	if(count($splittedCourse)>1){
		$formId=$splittedCourse[1];
		}

	return $formId;
	}

function replace($old, $new){
	if($new==null or $new==""){
		return $old;
		}
	return $new;
	}

function getYearGroup($formId){
	$group=array(
		"NUR" => -1,
		"REC" => 0,
		"Y1" => 1,
		"Y2" => 2,
		"Y3" => 3,
		"Y4" => 4,
		"Y5" => 5,
		"Y6" => 6,
		"Y7" => 7,
		"Y8" => 8,
		"Y9" => 9,
		"Y10" => 10,
		"Y11" => 11,
		"Y12" => 12,
		"Y13" => 13,
		);

	return $group[$formId];
	}

function getRelationship($rel){
	$relationship=array(
		"PAD" => 'PAF',
		"MAD" => 'PAM'
		);

	return $relationship[$rel];
	}

function map_contacts($info){
	$Contacts=array();
	$contactsno=array("1","2");
	foreach($contactsno as $no){
		$Contact=(array)fetchContact();
		$Contact['Code']['value']=$info['TutorCode'.$no];
		$Contact['Order']['value']=$no-1;
		$Contact['Relationship']['value']=getRelationship($info['TUT'.$no.'_Relationship']);
		if($info['TUT'.$no.'_NoEmail']=='0'){$rmailing='1';}else{$rmailing='0';}
		$Contact['ReceivesMailing']['value']=$rmailing;//$info['TUT'.$no.'_NoEmail'];
		$Contact['Surname']['value']=$info['TUT'.$no.'_Surname'];
		$Contact['Forename']['value']=$info['TUT'.$no.'_Name'];
		$Contact['EmailAddress']['value']=$info['TUT'.$no.'_Email'];
		$Contact['Nationality']['value']=$info['TUT'.$no.'_Nationality'];
		for($i=1;$i<=2;$i++){
			$Phone=(array)fetchPhone();
			$Phone['PhoneNo']['value']=trim($info['TUT'.$no.'_Phone'.$i]);
			$Phone['PhoneType']['value']='H';
			$Phone['Private']['value']='N';
			$Contact['Phones'][]=$Phone;
			}
		$Phone=(array)fetchPhone();
		$Phone['PhoneNo']['value']=trim($info['TUT'.$no.'_Cell']);
		$Phone['PhoneType']['value']='M';
		$Phone['Private']['value']='N';
		$Contact['Phones'][]=$Phone;
		if($no==1){
			$Address=(array)fetchAddress();
			$Address['Street']['value']=$info['TUT'.$no.'_Addr'];
			$Address['Postcode']['value']=$info['TUT'.$no.'_Postcode'];
			$Contact['Addresses'][]=$Address;
			}
		$Contacts[]=$Contact;
		}
	return $Contacts;
	}

function import_contact($sid,$Contact){
	$code=$Contact['Code']['value'];

	mysql_query("DELETE gidsid FROM gidsid JOIN guardian ON guardian.id=gidsid.guardian_id
							WHERE student_id='$sid' AND code='';");

	if($code!=''){
		$d_g=mysql_query("SELECT id FROM guardian WHERE code='$code';");
		if(mysql_num_rows($d_g)>0){
			$gid=mysql_result($d_g,0,'id');
			echo "    Contact ".$gid.":".$code." was updated\n";
			}
		else{
			mysql_query("INSERT INTO guardian SET surname='';");
			$gid=mysql_insert_id();
			$fresh='yes';
			echo "    Contact ".$gid.":".$code." was created\n";
			}

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

                    $Phones=$Contact['Phones'];
                    $Addresses=$Contact['Addresses'];
                    while(list($phoneno,$Phone)=each($Phones)){
                            $phoneid=-1;
                            if($Phone['PhoneType']=='H'){$field_name='home phone';}
                            elseif($Phone['PhoneType']=='M'){$field_name='mobile phone';}
                            $format="varchar(22)";
                            $phoneno=checkEntry($Phone['PhoneNo']['value'], $format, $field_name);
                            if($phoneno!='' and strlen($phoneno)>5){
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
                            }
                    while(list($addressno,$Address)=each($Addresses)){
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
                                                            mysql_query("UPDATE address SET $field='$inval' WHERE id='$aid';");
                                                            }
                                                    elseif($val['table_db']=='gidaid' and isset($aid)){
                                                            mysql_query("UPDATE gidaid SET $field='$inval'
                                                                                            WHERE guardian_id='$gid' AND address_id='$aid';");
                                                            }
                                                    }
                                            }
                                    }
			}
		return $gid;
		}
	return 0;
	}

function createStudent($info){
	$GENDER=array(1 => "M", 2 => "F");
	$splittedName=split(',', $info["Name"]);

	$surname=$splittedName[0];
	$forename=trim($splittedName[1]);

	//$splittedCourse=split(" ", $info["CourseCode"]);
	$splittedCourse=split(" ", $info["YearGroup"]);

	$yeargroup_id="";

	if(count($splittedCourse)>1){
		$yeargroup_id=$splittedCourse[1];
		}
	//$form_id=str_replace("Y","",$yeargroup_id).$info["ClassCode"];
	$form_id=str_replace("Y","",$yeargroup_id).$info["FormGroup"];

	if(array_key_exists("BirthDate", $info)){
		$dob=replace($student["dob"], $info["BirthDate"]);
		}
	else {
		$dob=replace($student["dob"], "");
		}

	if(mysql_query("INSERT INTO student (forename, surname, gender, dob) VALUES ('$forename', '$surname', '" . $GENDER[$info["Sex"]] . "', '$dob');")){
		$sid=mysql_insert_id();

		/*$community=array("id" => '',"type"=> "form","name" => $form_id);
		join_community($sid,$community);*/
		$community=array("id" => '',"type"=> "year","name" => getYearGroup($yeargroup_id));
		join_community($sid,$community);

		return mysql_query("INSERT INTO info (student_id, formerupn, enrolstatus, nationality, birthplace, countryoforigin, language) VALUES ('$sid', '" . $info["CLI"] . "', 'C', '" . $info["Nationality"] . "', '" . $info["PlaceOfBirth"] . "', '" . $info["CountryOfBirth"] . "', '" . $info["NativeLanguage"] . "');");
		}

	return false;
	}

function updateStudent($student, $info) {
	$GENDER=array(1 => "M", 2 => "F");
	$splittedName=split(',', $info["Name"]);

	$surname=$splittedName[0];
	$forename=trim($splittedName[1]);

	//$yeargroup_id=getFormId($info["CourseCode"]);
	$yeargroup_id=getFormId($info["YearGroup"]);
	//$form_id=str_replace("Y","",$yeargroup_id).$info["ClassCode"];
	$form_id=str_replace("Y","",$yeargroup_id).$info["FormGroup"];

	$forename=replace($student["forename"], $forename);
	$surname=replace($student["surname"], $surname);
	$gender=replace($student["gender"], $GENDER[$info["Sex"]]);

	if(array_key_exists("BirthDate", $info)){
		$dob=replace($student["dob"], $info["BirthDate"]);
		}
	else{
		$dob=replace($student["dob"], "");
		}

	mysql_query("UPDATE student SET forename='$forename', surname='$surname',
				gender='$gender', dob='$dob' WHERE id=" . $student["id"] . ";");

	$formerUpn=$info["CLI"];
	$studentInfo=fetchStudentInfoByFormerUpn($formerUpn);

	$nationality=replace($studentInfo["nationality"], $info["Nationality"]);
	$birthPlace=replace($studentInfo["birthplace"], $info["PlaceOfBirth"]);
	$countryOrigin=replace($studentInfo["countryoforigin"], $info["CountryOfBirth"]);

	if(array_key_exists("NativeLanguage", $info)){
		$language=replace($studentInfo["language"], $info["NativeLanguage"]);
		}
	else {
		$language=replace($studentInfo["language"], "");
		}

	/*$community=array("id" => '',"type"=> "year","name" => $form_id);
	join_community($student['id'],$community);*/
	$community=array("id" => '',"type"=> "year","name" => getYearGroup($yeargroup_id));
	join_community($student['id'],$community);

	return mysql_query("UPDATE info SET formerupn='$formerUpn', nationality='$nationality',
				birthplace='$birthPlace', countryoforigin='$countryOrigin', language='$language'
				WHERE student_id=".$student["id"].";");
	}


if(isset($CFG->odbc_user) and $CFG->odbc_user!='' and isset($CFG->odbc_password) and $CFG->odbc_password!=''){
	$export=new Extract($CFG->odbc_user, $CFG->odbc_password);
	$table=$export->table($CFG->odbc_table);
	$db=db_connect();

	$updated=0;
	$created=0;
	$gmodified=0;

	foreach($table as $info){
		$student=fetchStudentByFormerUpn($info["CLI"]);

		if($student){
			if(updateStudent($student, $info)){
				$updated++;
				echo "Student " . $info["CLI"] . " was updated\n";
				}
			else{
				echo "Was not possible to update " . $info["CLI"] . "\n";
				}
			}
		else{
			if(createStudent($info)){
				$created++;
				echo "Student " . $info["CLI"] . " was created\n";
				}
			else{
				echo "Was not possible to create " . $info["CLI"] . "\n";
				}
			}

		$student=fetchStudentByFormerUpn($info["CLI"]);
		if($student){
			$sid=$student['id'];
			$Contacts=map_contacts($info);
			foreach($Contacts as $Contact){
				$gid=import_contact($sid,$Contact);
				$gmodified++;
				}
			}

		$enrolnos[$info["CLI"]]=$info["CLI"];
		}

	$previous=0;

	$d_i=mysql_query("SELECT student_id, formerupn, yeargroup_id, enrolstatus FROM info JOIN student ON student.id=info.student_id;");
	while($inforows=mysql_fetch_array($d_i,MYSQL_ASSOC)){
		if(count($enrolnos)>0 and !isset($enrolnos[$inforows['formerupn']]) and $inforows['enrolstatus']=='C'){
			$newcommunity=array('id'=>'','type'=>"alumni", 
					  'name'=>'P:'.$inforows['yeargroup_id'],'year'=>get_curriculumyear());
			join_community($inforows['student_id'],$newcommunity);
			echo "Student ".$inforows['student_id']." enrolstatus changed to previous ".$inforows['formerupn']."\n";
			$previous++;
			}
		}

	echo "Students - Created: $created - Updated: $updated - Previous: $previous\n";
	echo "Contacts - Modified: $gmodified\n";
	echo date('Y-m-d h:i:s')."\n\n-------------------------------------------------------\n\n";
	}

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>
