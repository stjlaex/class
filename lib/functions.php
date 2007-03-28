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

/*reutrns the subjectname for that bid from the database*/
function get_subjectname($bid){
	if($bid=='%' or $bid=='G' or $bid=='General'){
		/*this is a fix that should be fixed in future!*/
		$subjectname='General';
		}
	elseif($bid!=' ' and $bid!=''){
		$d_subject=mysql_query("SELECT name FROM subject WHERE id='$bid'");
		$subjectname=mysql_result($d_subject,0);
		}
	else{
		$subjectname=$bid;
		}
	return $subjectname;
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

/*Uses the enum $value for the enum $field_name to look up and return the $description*/
/*call this before displaying the lang string*/
function displayEnum($value, $field_name){
	$value=strtoupper($value);
	$enumarray=getEnumArray($field_name);
	$description=$enumarray[$value];
	return $description;
	}

/*Returns the array of valid enum values and their meanings for a field*/
function getEnumArray($field_name) {
	/*for the student table*/
	$gender=array('M' => 'male', 'F' => 'female');

	/*for the info table*/
	$boarder=array('N' => 'notaboarder',
				   'B' => 'boarder',
				   'H' => 'hostfamily',
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
					   'AT' => 'awaitingtesting', 
					   'ATD' => 'testingdelayed', 
					   'ACP' => 'acceptedpending', 
					   'AC' => 'accepted', 
					   'RE' => 'rejected', 
					   'CA' => 'cancelled', 
					   'WL' => 'waitinglist', 
					   'C' => 'current', 
					   'P' => 'previous', 
					   'G' => 'guestpupil'  
					   //'S' => 'currentsubsidary(dualregistration)', 
					   //'M' => 'currentmain(dualregistration)'
					   );
	$transportmode=array('NOT' => 'informationnotobtained', 
						 'F' => 'onfoot', 'C' => 'privatecar', 
						 'T' => 'train', 'B' => 'bus', 'S' => 'schoolbus');

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
				   'GT' => 'giftedandtalented',
				   'OTH' => 'otherdifficulty/disability');
	$sencurriculum=array('A'=>'allsubject',
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

function file_mimeinfo($element, $filename) {
    $mimeinfo = array (
        'xxx'  => array ('type'=>'document/unknown', 'icon'=>'unknown.gif'),
        '3gp'  => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'ai'   => array ('type'=>'application/postscript', 'icon'=>'image.gif'),
        'aif'  => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
        'aiff' => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
        'aifc' => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
        'applescript'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'asc'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'asm'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'au'   => array ('type'=>'audio/au', 'icon'=>'audio.gif'),
        'avi'  => array ('type'=>'video/x-ms-wm', 'icon'=>'avi.gif'),
        'bmp'  => array ('type'=>'image/bmp', 'icon'=>'image.gif'),
        'c'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'cct'  => array ('type'=>'shockwave/director', 'icon'=>'flash.gif'),
        'cpp'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'cs'   => array ('type'=>'application/x-csh', 'icon'=>'text.gif'),
        'css'  => array ('type'=>'text/css', 'icon'=>'text.gif'),
        'dv'   => array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
        'doc'  => array ('type'=>'application/msword', 'icon'=>'word.gif'),
        'dcr'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'dif'  => array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
        'dir'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'dxr'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'eps'  => array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
        'gif'  => array ('type'=>'image/gif', 'icon'=>'image.gif'),
        'gtar' => array ('type'=>'application/x-gtar', 'icon'=>'zip.gif'),
        'gz'   => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'gzip' => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'h'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'hpp'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'hqx'  => array ('type'=>'application/mac-binhex40', 'icon'=>'zip.gif'),
        'html' => array ('type'=>'text/html', 'icon'=>'html.gif'),
        'htm'  => array ('type'=>'text/html', 'icon'=>'html.gif'),
        'java' => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'jcb'  => array ('type'=>'text/xml', 'icon'=>'jcb.gif'),
        'jcl'  => array ('type'=>'text/xml', 'icon'=>'jcl.gif'),
        'jcw'  => array ('type'=>'text/xml', 'icon'=>'jcw.gif'),
        'jmt'  => array ('type'=>'text/xml', 'icon'=>'jmt.gif'),
        'jmx'  => array ('type'=>'text/xml', 'icon'=>'jmx.gif'),
        'jpe'  => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
        'jpeg' => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
        'jpg'  => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
        'jqz'  => array ('type'=>'text/xml', 'icon'=>'jqz.gif'),
        'js'   => array ('type'=>'application/x-javascript', 'icon'=>'text.gif'),
        'latex'=> array ('type'=>'application/x-latex', 'icon'=>'text.gif'),
        'm'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'mov'  => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'movie'=> array ('type'=>'video/x-sgi-movie', 'icon'=>'video.gif'),
        'm3u'  => array ('type'=>'audio/x-mpegurl', 'icon'=>'audio.gif'),
        'mp3'  => array ('type'=>'audio/mp3', 'icon'=>'audio.gif'),
        'mp4'  => array ('type'=>'video/mp4', 'icon'=>'video.gif'),
        'mpeg' => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
        'mpe'  => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
        'mpg'  => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),

        'odt'  => array ('type'=>'application/vnd.oasis.opendocument.text', 'icon'=>'odt.gif'),
        'ott'  => array ('type'=>'application/vnd.oasis.opendocument.text-template', 'icon'=>'odt.gif'),
        'oth'  => array ('type'=>'application/vnd.oasis.opendocument.text-web', 'icon'=>'odt.gif'),
        'odm'  => array ('type'=>'application/vnd.oasis.opendocument.text-master', 'icon'=>'odt.gif'),
        'odg'  => array ('type'=>'application/vnd.oasis.opendocument.graphics', 'icon'=>'odt.gif'),
        'otg'  => array ('type'=>'application/vnd.oasis.opendocument.graphics-template', 'icon'=>'odt.gif'),
        'odp'  => array ('type'=>'application/vnd.oasis.opendocument.presentation', 'icon'=>'odt.gif'),
        'otp'  => array ('type'=>'application/vnd.oasis.opendocument.presentation-template', 'icon'=>'odt.gif'),
        'ods'  => array ('type'=>'application/vnd.oasis.opendocument.spreadsheet', 'icon'=>'odt.gif'),
        'ots'  => array ('type'=>'application/vnd.oasis.opendocument.spreadsheet-template', 'icon'=>'odt.gif'),
        'odc'  => array ('type'=>'application/vnd.oasis.opendocument.chart', 'icon'=>'odt.gif'),
        'odf'  => array ('type'=>'application/vnd.oasis.opendocument.formula', 'icon'=>'odt.gif'),
        'odb'  => array ('type'=>'application/vnd.oasis.opendocument.database', 'icon'=>'odt.gif'),
        'odi'  => array ('type'=>'application/vnd.oasis.opendocument.image', 'icon'=>'odt.gif'),

        'pct'  => array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'pdf'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
        'php'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'pic'  => array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'pict' => array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'png'  => array ('type'=>'image/png', 'icon'=>'image.gif'),
        'pps'  => array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
        'ppt'  => array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
        'ps'   => array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
        'qt'   => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'ra'   => array ('type'=>'audio/x-realaudio', 'icon'=>'audio.gif'),
        'ram'  => array ('type'=>'audio/x-pn-realaudio', 'icon'=>'audio.gif'),
        'rhb'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'rm'   => array ('type'=>'audio/x-pn-realaudio', 'icon'=>'audio.gif'),
        'rtf'  => array ('type'=>'text/rtf', 'icon'=>'text.gif'),
        'rtx'  => array ('type'=>'text/richtext', 'icon'=>'text.gif'),
        'sh'   => array ('type'=>'application/x-sh', 'icon'=>'text.gif'),
        'sit'  => array ('type'=>'application/x-stuffit', 'icon'=>'zip.gif'),
        'smi'  => array ('type'=>'application/smil', 'icon'=>'text.gif'),
        'smil' => array ('type'=>'application/smil', 'icon'=>'text.gif'),
        'sqt'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'swa'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'swf'  => array ('type'=>'application/x-shockwave-flash', 'icon'=>'flash.gif'),
        'swfl' => array ('type'=>'application/x-shockwave-flash', 'icon'=>'flash.gif'),

        'sxw'  => array ('type'=>'application/vnd.sun.xml.writer', 'icon'=>'odt.gif'),
        'stw'  => array ('type'=>'application/vnd.sun.xml.writer.template', 'icon'=>'odt.gif'),
        'sxc'  => array ('type'=>'application/vnd.sun.xml.calc', 'icon'=>'odt.gif'),
        'stc'  => array ('type'=>'application/vnd.sun.xml.calc.template', 'icon'=>'odt.gif'),
        'sxd'  => array ('type'=>'application/vnd.sun.xml.draw', 'icon'=>'odt.gif'),
        'std'  => array ('type'=>'application/vnd.sun.xml.draw.template', 'icon'=>'odt.gif'),
        'sxi'  => array ('type'=>'application/vnd.sun.xml.impress', 'icon'=>'odt.gif'),
        'sti'  => array ('type'=>'application/vnd.sun.xml.impress.template', 'icon'=>'odt.gif'),
        'sxg'  => array ('type'=>'application/vnd.sun.xml.writer.global', 'icon'=>'odt.gif'),
        'sxm'  => array ('type'=>'application/vnd.sun.xml.math', 'icon'=>'odt.gif'),

        'tar'  => array ('type'=>'application/x-tar', 'icon'=>'zip.gif'),
        'tif'  => array ('type'=>'image/tiff', 'icon'=>'image.gif'),
        'tiff' => array ('type'=>'image/tiff', 'icon'=>'image.gif'),
        'tex'  => array ('type'=>'application/x-tex', 'icon'=>'text.gif'),
        'texi' => array ('type'=>'application/x-texinfo', 'icon'=>'text.gif'),
        'texinfo'  => array ('type'=>'application/x-texinfo', 'icon'=>'text.gif'),
        'tsv'  => array ('type'=>'text/tab-separated-values', 'icon'=>'text.gif'),
        'txt'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'wav'  => array ('type'=>'audio/wav', 'icon'=>'audio.gif'),
        'wmv'  => array ('type'=>'video/x-ms-wmv', 'icon'=>'avi.gif'),
        'asf'  => array ('type'=>'video/x-ms-asf', 'icon'=>'avi.gif'),
        'xls'  => array ('type'=>'application/vnd.ms-excel', 'icon'=>'excel.gif'),
        'xml'  => array ('type'=>'application/xml', 'icon'=>'xml.gif'),
        'xsl'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'zip'  => array ('type'=>'application/zip', 'icon'=>'zip.gif')
    );

    if (eregi('\.([a-z0-9]+)$', $filename, $match)) {
        if (isset($mimeinfo[strtolower($match[1])][$element])) {
            return $mimeinfo[strtolower($match[1])][$element];
        } else {
            return $mimeinfo['xxx'][$element];   // By default
        }
    } else {
        return $mimeinfo['xxx'][$element];   // By default
    }
}

/**
 * Send an email (with attachments)
 *
 * Taken from moodlelib and adapted for ClaSS
 *
 * @uses $CFG
 * @uses $_SERVER
 * @uses SITEID
 * @param recipient 
 * @param from 
 * @param string $subject plain text subject line of the email
 * @param string $messagetext plain text version of the message
 * @param string $messagehtml complete html version of the message (optional)
 * @param string $attachments array of files on the filesystem (extension indicates MIME)
 * @param boolean $usetrueaddress determines whether $from email address should
 *          be sent out. Will be overruled by user profile setting for maildisplay
 * @return boolean|string Returns "true" if mail was sent OK, "emailstop" if email
 *          was blocked by user and "false" if there was another sort of error.
 */
function send_email_to($recipient, $from, $subject, $messagetext, $messagehtml='', $attachments='', $usetrueaddress=true, $repyto='', $replytoname=''){

    global $CFG;
    include_once('libphp-phpmailer/class.phpmailer.php');//this works for Debian 

    if(empty($recipient)){
        return false;
		}
    if($CFG->emailoff=='yes'){
        return 'emailstop';
		}
	/*    if (over_bounce_threshold($user)) {
        error_log("User $user->id (".fullname($user).") is over bounce threshold! Not sending.");
        return false;
    }
	*/

    $mail = new phpmailer;
    $mail->Version = $CFG->version;
    //$mail->PluginDir = $CFG->libdir .'/libphp-phpmailer/';// plugin directory (eg smtp plugin)

	/*    if(current_language()!='en'){
        $mail->CharSet = get_string('thischarset');
		}
	*/

    if($CFG->emailsmtphosts=='qmail'){
        $mail->IsQmail();                              // use Qmail system
		} 
	else if (empty($CFG->emailsmtphosts)){
        $mail->IsMail();                               // use PHP mail() = sendmail
		} 
	else{
        $mail->IsSMTP();                               // use SMTP directly
        if($CFG->debug=='on'){
            echo '<pre>' . "\n";
            $mail->SMTPDebug = true;
			}
        $mail->Host = $CFG->emailsmtphosts;               // specify main and backup servers
        if($CFG->smtpuser){                          // Use SMTP authentication
            $mail->SMTPAuth = true;
            $mail->Username = $CFG->smtpuser;
            $mail->Password = $CFG->smtppass;
			}
		}

    $adminuser='admin@classforschools.com';

    // for handling bounces
    if(!empty($CFG->emailhandlebounces)){
        $mail->Sender = $CFG->emailhandlebounces;
		}
    else{
        $mail->Sender=$adminuser;
		}

    if(is_string($from)){
        $mail->From     = $CFG->emailnoreply;
        $mail->FromName = $from;
		}
	else{
        $mail->From     = $CFG->emailnoreply;
        $mail->FromName = 'ClaSS';
        if(empty($replyto)){
            $mail->AddReplyTo($CFG->emailnoreply,'ClaSS');
			}
		}

    if(!empty($replyto)){
        $mail->AddReplyTo($replyto,$replytoname);
		}

    $mail->Subject = substr(stripslashes($subject), 0, 900);
    $mail->AddAddress($recipient,'');
    $mail->WordWrap = 79;                              // set word wrap

	/*
    if(!empty($from->customheaders)){                 // Add custom headers
        if(is_array($from->customheaders)){
            foreach ($from->customheaders as $customheader) {
                $mail->AddCustomHeader($customheader);
            }
        } else {
            $mail->AddCustomHeader($from->customheaders);
        }
    }
    if (!empty($from->priority)) {
        $mail->Priority = $from->priority;
    }
	*/

    if($messagehtml){
        $mail->IsHTML(true);
        $mail->Encoding='quoted-printable';// Encoding to use
        $mail->Body=$messagehtml;
        $mail->AltBody="\n$messagetext\n";
		}
	else{
        $mail->IsHTML(false);
        $mail->Body="\n$messagetext\n";
		}

    if(is_array($attachments)){
		while(list($index,$attachment)=each($attachments)){
			if(is_file($attachment['filepath'])){ 
				$mimetype=file_mimeinfo('type', $attachment['filename']);
				$mail->AddAttachment($attachment['filepath'], $attachment['filename'], 'base64', $mimetype);
				}
			}
		}

    if($mail->Send()){
		return true;
		}
	else{
        //mtrace('ERROR: '. $mail->ErrorInfo);
        //add_to_log(SITEID, 'library', 'mailer', $FULLME, 'ERROR: '. $mail->ErrorInfo);
        return false;
		}
	}
?>