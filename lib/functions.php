<?php 
/**			   								functions.php   
 * General purpose ClaSS functions. 
 */


function emailHeader(){
	global $CFG;
	$headers = 'From: ClaSS@'.$CFG->siteaddress ."\r\n" . 
				'Reply-To: noreply@'.$CFG->siteaddress . "\r\n" .
			    'X-Mailer: PHP/' . phpversion();
	return $headers;
	}

function formsClasses($fid){
	/*returns an array listing the cids for all classes associated with
	 *this form where the class is actually populated by just this
	 *form's sids
	 */
	$cids=array();
	$d_form=mysql_query("SELECT yeargroup_id FROM form WHERE id='$fid'");
	$yid=mysql_result($d_form,0);
	$comid=updateCommunity(array('type'=>'year','name'=>$yid));
	$d_cohort=mysql_query("SELECT * FROM cohort JOIN
						cohidcomid ON cohidcomid.cohort_id=cohort.id WHERE
						cohidcomid.community_id='$comid' ORDER BY course_id");
   	while($cohort=mysql_fetch_array($d_cohort, MYSQL_ASSOC)){
		$currentyear=getCurriculumYear($cohort['course_id']);
		$currentseason='S';
		if($cohort['year']==$currentyear and $cohort['season']==$currentseason){
			$stage=$cohort['stage'];
			$crid=$cohort['course_id'];
			$d_classes=mysql_query("SELECT subject_id, naming FROM classes 
				WHERE stage='$stage' AND course_id='$crid' AND generate='forms'");
			while($classes=mysql_fetch_array($d_classes, MYSQL_ASSOC)){
				$bid=$classes['subject_id'];
				$name=array();
				if($classes['naming']==''){
					$name['root']=$bid;
					$name['stem']='-';
					$name['branch']='';
					}
				else{
					list($name['root'],$name['stem'],$name['branch'],$name_counter)
								= split(';',$classes['naming'],4);
					while(list($index,$namecheck)=each($name)){
						if($namecheck=='subject'){$name["$index"]=$bid;}
						if($namecheck=='stage'){$name["$index"]=$stage;}
						if($namecheck=='course'){$name["$index"]=$crid;}
						if($namecheck=='year'){$name["$index"]=$yid;}
						}
					}
				$cids[]=$name['root'].$name['stem'].$name['branch'].$fid;;
				}
			}
		}
	return $cids;
	}

function fputcsv($handle, $row, $fd=',', $quot='"'){
	$str='';
	foreach ($row as $cell) {
		$cell=str_replace(Array($quot,        "\n"),
                         Array($quot.$quot,  ''),
                         $cell);
		if (strchr($cell, $fd)!==FALSE || strchr($cell, $quot)!==FALSE){
           $str.=$quot.$cell.$quot.$fd;
		   } 
		else {
           $str.=$cell.$fd;
		   }
		}
	fputs($handle, substr($str, 0, -1)."\n");
	return strlen($str);
	}

function nullCorrect($array){
	if(sizeof($array)>0 and is_array($array)){
		foreach($array as $key => $value){
			if(sizeof($value)>0 and is_array($value)){
				$array[$key]=nullCorrect($value);
				}
			elseif($value=='' and $value!='0'){$array[$key]=' ';}
		  //		  if(!$value){$array[$key]=' ';}
			}
		}
	else{$array=' ';}
	return $array;
	}

function good_strtolower($value){
	/*for compatibility with utf8*/
	$value=mb_strtolower($value, mb_detect_encoding($value));
	return $value;
	}

function js_addslashes($value){
	/*should only be used when writing a string for use by javascript*/
	$o='';
	$l=strlen($value);
	for($i=0;$i<$l;$i++){
			$c=$value[$i];
			switch($c){
					case '<': $o.='\\x3C'; break;
					case '>': $o.='\\x3E'; break;
					case '\'': $o.='\\\''; break;
					case '\\': $o.='\\\\'; break;
					case '"':  $o.='\\"'; break;
					case "\n": $o.='\\n'; break;
					case "\r": $o.='\\r'; break;
					default:
						$o.=$c;
				}
		}
	return $o;
	}

function clean_text($value){
	$value=trim($value);
	$value=stripslashes($value);
	/*replaces all MS Word smart quotes, EM dashes and EN dashes*/
	$search=array(chr(145),chr(146),chr(147),chr(148),chr(150),chr(151));
	$replace=array("'","'",'"','"','-','-');
	$value=str_replace($search,$replace,$value);
	/*blanks possible dodgy sql injection attempt*/
	$search=array('SELECT ','INSERT ','DELETE ','DROP ');
	$value=str_replace($search,'',$value);
	$search=array('*','<','>');
	$value=str_replace($search,'',$value);
	//   	$value=eregi_replace('[^-.?,!;()+:[:digit:][:space:][:alpha:]]','', $value);
	$value=addslashes($value);
	return $value;
 	}


function checkEntry($value, $format='', $field_name=''){
	$value=trim($value);
	$value=good_strtolower($value);
	$value=ucwords($value);	
	$field_type=split('[()]', $format);

	if($field_name=='form_id'){$value=strtoupper($value);}

	if($field_type[0]=='date'){
		/*assumes date order day-month-year, php wants year-month-day*/
		$date=split('[/]',$value);
		$value=$date[2].'-'.$date[1].'-'.$date[0];
		print $value;
		}
	elseif($field_type[0]=='enum'){
		$value=strtoupper($value);
		$value=checkEnum($value, $field_name);
		}
	return $value;
	}

function checkEnum($value, $field_name) {
	$enumarray=getEnumArray($field_name);
	if(array_key_exists($value,$enumarray)){
		}
	else{
		$value='';
		}
	return $value;
	}

function displayEnum($value, $field_name) {
	$value=strtoupper($value);
	$enumarray=getEnumArray($field_name);
	$description=$enumarray[$value];
	return $description;
	}

function getEnumArray($field_name) {
	/*for the student table*/
	$gender=array('M' => 'male', 'F' => 'female');

	/*for the info table*/
	$boarder=array('N' => 'notaboarder',
				   'B' => 'boarder',
				   '6' => 'boardersixnightsorless', 
				   '7' => 'boardersevennights');
	$religion=array('NOT' => 'informationnotobtained', 
					'BU' => 'buddhist', 'CH' => 'christian', 'HI' =>
					'hindu', 'JE' => 'jewish', 'MU' => 'muslim', 
					'NO' => 'noreligion', 
					'OT' => 'otherreligion', 'SI' => 'sikh');
	$reledu=array('A' => 'attendsreligiouseducation', 
				  'W' => 'withdrawnfromreligiouseducation');
	$relwo=array('A' => 'attendscollectivewoship', 
				 'W' => 'withdrawnfromcollectiveworthship');
	$parttime=array('N' => 'no', 'Y' => 'yes');
	$sen=array('N' => 'no', 'Y' => 'yes');
	$medical=array('N' => 'no', 'Y' => 'yes');
	$incare=array('N' => 'no', 'Y' => 'yes');
	$firstlanguage=array('ENG' => 'english', 
						 'ENB' => 'believedtobeenglish', 
						 'OTH' => 'other', 
						 'NOT' => 'informationnotobtained');
	$enrolstatus=array('EN' => 'enquired', 
					   'AP' => 'applied', 
					   'AC' => 'accepted', 
					   'C' => 'current', 
					   'P' => 'previous', 
					   'G' => 'guestpupil',  
					   'S' => 'currentsubsidary(dualregistration)', 
					   'M' => 'currentmain(dualregistration)');
	$transportmode=array('NOT' => 'informationnotobtained', 
						 'F' => 'onfoot', 'C' => 'privatecar', 
						 'T' => 'train', 'B' => 'bus', 'S' => 'schoolbus',);

	/*for the gidsid table*/
	$priority=array('0' => 'first', '1' => 'second', '2' => 'third', '3' => 'fourth');
	$mailing=array('0' => 'nomailing', '1' => 'allmailing', '2' => 'reportsonly');
	$relationship=array('NOT' => 'informationnotobtained', 'CAR' =>
						'carer', 'DOC' => 'doctor', 'FAM' => 'otherfamilymember', 'PAM'
						=> 'mother', 'PAF' => 'father', 'OTH' => 'othercontact', 'STP' =>
						'stepparent', 'REL' => 'otherrelative', 'SWR' => 'socialworker', 
						'RLG' => 'religiouscontact', 'AGN' => 'agent', 'HFA' => 'hostfamily');
	$responsibility=array('N' => 'noparentalresponsibility', 'Y' => 'parentalresponsibility');

	/*for the phone table*/
	$phonetype=array('H' => 'homephone', 'W' => 'workphone', 
					 'M' => 'mobilephone', 'F' => 'faxnumber', 'O' => 'otherphone');

	/*for the gidaid table*/
	$addresstype=array('H' => 'home', 'W' => 'work', 'V' =>
					   'holiday', 'O' => 'other');

	/*for the report table*/
	$component=array('None' => 'notapplied', 
					 'N' => 'non-validating', 
					 'V' => 'validating', 
					 'A' => 'all');

	/*for the assessment tables*/
	$resultstatus=array('I' => 'interim', 'R' => 'result', 'T' =>
						'target', 'P' => 'provisionalresult', 'E' => 'estimate');

	$season=array('S' => 'summer', 'W' => 'winter', 'M' =>
				  'modular/continuous', '1' => 'january', '2' => 'feburary', '3' =>
				  'march', '4' => 'april', '5' => 'may', '6' => 'june', '7' =>
				  'july', '8' => 'august', '9' => 'september', 
				  'a' => 'october', 'b' => 'november', 'c' => 'december');

	/*for the sen table*/
	$senprovision=array('N' => 'notonregister', 
						'A'=> 'schoolaction',
						'P'=> 'schoolactionplus', 
						'Q'=> 'schoolactionplusandstatutoryassessment', 
						'S'=> 'statemented');
	$senranking=array('1' => 'level 1', '2' => 'level 2', '3' => 'level 3');
	$sentype=array('SPLD' => 'specificlearningdifficulty(dyslexia)', 
				   'MLD' => 'moderatelearningdifficulty', 
				   'SLD' => 'severelearningdifficulty', 
				   'PMLD' => 'profoundanmultiplelearningdifficulty', 
				   'EBD' => 'emotionalandbehaviouraldifficulty', 
				   'SCD' => 'speechorcommunicationdifficulty', 
				   'HI' => 'hearingimpairment', 
				   'VI' => 'visualimpairment', 
				   'MSI' => 'multi-sensoryimpairment', 
				   'PD' => 'physicaldisability', 
				   'AUT' => 'autism',
				   'OTH' => 'otherdifficulty/disability');
	$sencurriculum=array('A' => 'allsubjects', 
						 'M' => 'modifiedcurriculum', 
						 'D' => 'curriculumdisapplied');

	/*for the exclusions table*/
	$exclusionscategory=array('F' => 'fixed-term', 'P' => 'permanent', 'L' => 'lunchtime');

	$appeal=array('R' => 'appealrejected', 'S' => 'appealsuccesful');

	$session=array('NA' => 'NA', 'AM' => 'AM', 'PM' => 'PM');

	return $$field_name;
	}


function list_directory_files($directory,$extension='*'){
    $results=array();
    $handler=opendir($directory);
    while($file=readdir($handler)){
        if($file!='.' and $file!='..'){
			$fileparts=explode('.',$file);
            if($fileparts[1]==$extension or $extension=='*'){$results[]=$fileparts[0];}
			}
		}
    closedir($handler);
    return $results;
	}

/*reads content of csv file into array flines*/
function fileRead($file){
	$flines=array();
   	while($in=fgetcsv($file,1000,',')){
		if($in[0]!=''){
			if($in[0]{0}!='#' & $in[0]{0}!='/'){$flines[]=$in;}
			}
		}
   	fclose($file);
	return $flines;
}

/*function to open a file*/
function fileOpen($path){
   	$file = fopen ($path, 'r');
   	if (!$file){
		$error[]='Unable to open remote file '.$path.'!'; 
		include('scripts/results.php');
		exit;
		}
	return $file;
	}

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
	while($scabstudents[]=mysql_fetch_array($d_student, MYSQL_ASSOC)){}

  	$d_student=mysql_query("SELECT a.student_id, a.forename, a.middlenames,
					a.surname, a.form_id FROM
					com2students AS a LEFT JOIN com1students AS b ON
					a.student_id=b.student_id WHERE
					b.student_id IS NOT NULL ORDER BY a.form_id, a.surname");
	$unionstudents=array();
	while($unionstudents[]=mysql_fetch_array($d_student, MYSQL_ASSOC)){}
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
	while($students[]=mysql_fetch_array($d_student, MYSQL_ASSOC)){}
	return $students;
	}

/*Remove a sid from a commmunity*/
function leaveCommunity($sid,$community){
	$todate=date("Y-m-d");
	$type=$community['type'];
	$name=$community['name'];
	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=updateCommunity($community);}
	mysql_query("UPDATE comidsid SET leavingdate='$todate' WHERE
							community_id='$comid' AND student_id='$sid'");
	if($type=='year'){$studentfield='yeargroup_id';}
	elseif($type=='form'){$studentfield='form_id';}
	if($studentfield!=''){
		mysql_query("UPDATE student SET $studentfield='' WHERE id='$sid'");
		}
	}

/*Add a sid to a community*/
function joinCommunity($sid,$community){
	$todate=date("Y-m-d");
	$type=$community['type'];
	$name=$community['name'];
	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=updateCommunity($community);}

	/*membership of a form or yeargroup is exclusive - need to remove
	from old group first - and where student progresses through
	application procedure form enquired to year*/
	$oldtypes=array();
	if($type=='year'){
		$studentfield='yeargroup_id';
		$oldtypes[]=$type;
		$oldtypes[]='accepted';
		}
	elseif($type=='form'){
		$studentfield='form_id';
		$oldtypes[]=$type;
		}
	elseif($type=='alumni' ){
		$studentfield='yeargroup_id';
		$oldtypes[]='year';
		}
	elseif($type=='applied'){
		$studentfield='yeargroup_id';
		$oldtypes[]='accepted';
		}
	elseif($type=='accepted'){
		$studentfield='yeargroup_id';
		$oldtypes[]='enquired';
		}

	if($studentfield!=''){
	   	$d_student=mysql_query("SELECT $studentfield FROM student WHERE id='$sid'");
		$oldname=mysql_result($d_student,0);

   		if($oldname!='' and $name!=$oldname){
			/*first remove sid from their old group*/
			while(list($index,$oldtype)=each($oldtypes)){
				$oldcommunity=array('type'=>$oldtype,'name'=>$oldname);
				leaveCommunity($sid,$oldcommunity);
				}
			}
		mysql_query("UPDATE student SET $studentfield='$name' WHERE id='$sid'");
		}

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

	return $oldname;
	}

/*checks for a cohort and creates if it doesn't exist*/
/*expects an array with at least course_id and stage set*/
/*returns the cohort_id*/
function updateCohort($cohort){
	$crid=$cohort['course_id'];
	$stage=$cohort['stage'];
	if(isset($cohort['year'])){$year=$cohort['year'];}
	else{$year=getCurriculumYear($crid);}
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

function getCurriculumYear($crid=''){
	/*the calendar year that the academic year ends */
	/*to sophisticate in future*/
	$d_course=mysql_query("SELECT endmonth FROM course WHERE id='$crid'");
	$endmonth=mysql_result($d_course,0);
	if($endmonth==''){$endmonth='6';/*defaults to June*/}
	$thismonth=date('m');
	$thisyear=date('Y');
	if($thismonth>$endmonth){$thisyear++;}
	return $thisyear;
	}

function getcurrentCohortId($crid,$stage,$year='',$season='S'){
	/*returns the id for the cohort specified by crid and stage*/
	/*will default to the current active cohort unless year is specified*/
	if($year==''){$year=getCurriculumYear($crid);}
	$d_cohort=mysql_query("SELECT id FROM cohort WHERE
						course_id='$crid' AND stage='$stage' AND
						year='$year' AND season='$season'");
	$cohid=mysql_result($d_cohort,0);
	return $cohid;
	}
?>