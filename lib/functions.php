<?php 
/**			   								functions.php   
 * General purpose ClaSS functions. 
 *
 * Notes on conventions for naming functions:
 * (0) First clause is indicative of the action the function performs
 * (1) Use underscores for clarity only (no meaning), capitals can
 *     have meaning so avoid javascript style constructions BUT older
 *     functions did use this!
 * (3) get_ refers to returning a single valued variable
 * (4) list_ refers to returning a plain array usually the result of
 *     a db query and the fields will be named accordingly
 * (5) fetch_ used instead of list to indicate the returned array is
 *     in the inernal class xmlarray format
 * (6) The last clause of the function generally indicates what is returned,
 *     capitalised will be an xmlarray, lowercase will be plain array, a
 *     plural will indicate multiple records
 * (7) For convenience some fetch functions have a _short alternative
 *	   which returns just the essential fields when the full version
 *	   is overly verbose.
 * (8) If unspecified the subject of a listin or countin style
 *     functions will be sids, anything else should be identified as
 *     a clause in the function name
 */


function emailHeader(){
	global $CFG;
	$headers = 'From: ClaSS@'.$CFG->siteaddress ."\r\n" . 
				'Reply-To: noreply@'.$CFG->siteaddress . "\r\n" .
			    'X-Mailer: PHP/' . phpversion();
	return $headers;
	}

/*Returns an array listing the cids for all classes associated with
 *this form where the class is actually populated by just this
 *form's sids
 */
function list_forms_classes($fid){
	$cids=array();
	$cohorts=list_community_cohorts(array('id'=>'','type'=>'form','name'=>$fid));

   	while(list($index,$cohort)=each($cohorts)){
		$currentyear=get_curriculumyear($cohort['course_id']);
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

function file_putcsv($handle, $row, $fd=',', $quot='"'){
	$str='';
	foreach($row as $cell){
		$cell=str_replace(Array($quot,        "\n"),
                         Array($quot.$quot,  ''),
                         $cell);
		if(strchr($cell, $fd)!==FALSE || strchr($cell, $quot)!==FALSE){
           $str.=$quot. $cell. $quot. $fd;
		   } 
		else {
           $str.=$cell. $fd;
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

/*For compatibility with utf8*/
function good_strtolower($value){
	$value=mb_strtolower($value, mb_detect_encoding($value));
	return $value;
	}

/*Should only be used when writing a string for use by javascript*/
function js_addslashes($value){
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

/*Attempts to get rid of any nasties before a mysql insert*/
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


/*Does some simple data validation for input*/
function checkEntry($value, $format='', $field_name=''){
	$value=trim($value);
	$value=good_strtolower($value);
	$value=ucwords($value);	
	$field_type=split('[()]', $format);

	if($field_name=='form_id'){$value=strtoupper($value);}

	if($field_type[0]=='date'){
		/*assumes date order day-month-year, php wants year-month-day*/
		$date=split('[/]',$value);
		$value=$date[0].'-'.$date[1].'-'.$date[2];
		}
	elseif($field_type[0]=='enum'){
		$value=strtoupper($value);
		$value=checkEnum($value, $field_name);
		}
	return $value;
	}

/*Validates a value which claims to be compatible with a enum field*/
function checkEnum($value, $field_name) {
	$enumarray=getEnumArray($field_name);
	if(array_key_exists($value,$enumarray)){
		}
	else{
		$value='';
		}
	return $value;
	}

/**/
function displayEnum($value, $field_name) {
	$value=strtoupper($value);
	$enumarray=getEnumArray($field_name);
	$description=$enumarray[$value];
	return $description;
	}

/*Retursn the array of valid enum values and their meanings for a field*/
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
				   'PMLD' => 'profoundandmultiplelearningdifficulty', 
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

	/*for the community table, does not list special types like
	yeargroup, formgroup, family etc*/
	$community_type=array('' => '', 
						  'ACADEMIC' => 'academic', 
						  'TUTOR' => 'tutorgroup', 
						  'TRIP' => 'trip', 
						  'REG' => 'registrationgroup', 
						  'STOP' => 'travelstop', 
						  'EXTRA' => 'other'
						  );
	/*for the list_studentfield script, not an enumarray at all!*/
	$studentfield=array(
						'' => '',
						'Surname' => 'surname', 
						'Gender' => 'gender', 
						'YearGroup' => 'yeargroup', 
						'RegistrationGroup' => 'formgroup', 
						'DOB' => 'dateofbirth',
						'Nationality' => 'nationality',
						'EmailAddress' => 'email',
						'MobilePhone' => 'mobilephone',
						'EnrolNumber' => 'enrolmentnumber',
						'FirstLanguage' => 'firstlanguage',
						'EntryDate' => 'schoolstartdate',
						'FirstContact' => 'firstcontact',
						'FirstContactPhone' => 'firstcontactphone',
						'FirstContactEmailAddress' => 'firstcontactemailaddress',
						'SecondContact' => 'secondcontact',
						'SecondContactPhone' => 'secondcontactphone',
						'SecondContactEmailAddress' => 'secondcontactemailaddress'
						);
	/*for the register*/
	$absencecode=array(
						'O' => 'unauthorisedabsence',
						'I' => 'illness',
						'M' => 'medicaldentalappointments',
						'P' => 'approvedsportingactivity',
						'S' => 'studyleave',
						'V' => 'educationalvisitortrip',
						'B' => 'educatedoffsite',
						'E' => 'excluded',
						'F' => 'extendedfamilyholidayagreed',
						'G' => 'familyholidaynotagreeded',
						'H' => 'familyholidayagreed',
						'J' => 'interview',
						'R' => 'religiousobservance',
						'T' => 'travellerabsence',
						'W' => 'workexperience',
						'C' => 'otherauthorisedcircumstances',
						'D' => 'dualregistrationattendingother',
						'N' => 'noreasonyetprovided',
						'U' => 'lateafterregisterclosed',
						'X' => 'untimetabledsessions',
						'Y' => 'enforcedclosure',
						'Z' => 'pupilnotonrole',
						'#' => 'schoolclosedtopupils'
						);

	return $$field_name;
	}

/*Sorts an array but is not utf8 friendly*/
function sortx(&$array,$sort=array()){
   $function='';
   while(list($key)=each($sort)){
     if(isset($sort[$key]['case'])&&($sort[$key]['case']==TRUE)){
       $function .= 'if (good_strtolower($a["' . $sort[$key]['name'] . '"])<>good_strtolower($b["' . $sort[$key]['name'] . '"])) { return (good_strtolower($a["' . $sort[$key]['name'] . '"]) ';
     } else {
       $function .= 'if ($a["' . $sort[$key]['name'] . '"]<>$b["' . $sort[$key]['name'] . '"]) { return ($a["' . $sort[$key]['name'] . '"] ';
     }
     if(isset($sort[$key]['sort'])&&($sort[$key]['sort']=='DESC')){
       $function .= '<';
     } else {
       $function .= '>';
     }
     if (isset($sort[$key]['case'])&&($sort[$key]['case'] == TRUE)) {
       $function .= ' good_strtolower($b["' . $sort[$key]['name'] . '"])) ? 1 : -1; } else';
     } else {
       $function .= ' $b["' . $sort[$key]['name'] . '"]) ? 1 : -1; } else';
     }
   }
   $function .= ' { return 0; }';
   usort($array,create_function('$a, $b', $function));
   }

/*Lists the contents of a directory on the server, can limit by extension*/
/*Currently used only for the templates*/
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

/*Reads content of csv file into array flines*/
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

/*Function to open a file*/
function fileOpen($path){
   	$file = fopen ($path, 'r');
   	if (!$file){
		$error[]='Unable to open remote file '.$path.'!'; 
		include('scripts/results.php');
		exit;
		}
	return $file;
	}

?>