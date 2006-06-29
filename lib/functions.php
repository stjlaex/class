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
	$cids=array();
	$d_form=mysql_query("SELECT yeargroup_id FROM form WHERE id='$fid'");
	$yid=mysql_result($d_form,0);
	$d_classes=mysql_query("SELECT subject_id FROM classes 
		WHERE yeargroup_id='$yid' AND generate='forms'");
   	while($classes=mysql_fetch_array($d_classes, MYSQL_ASSOC)){
		$cids[] = $classes{'subject_id'}.$fid;
		/*WARNING:this only works so long as the naming is not sophisticated!!!*/
		/*WARNING:and only if form has a single yid value!!!*/
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
		  if($value=='' and $value!='0'){$array[$key]=' ';}
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
	/*blanks possible dodgy sql etc.*/
	//	$search=array('select ','insert ','delete ','drop ','SELECT ','INSERT ','DELETE ','DROP ');
	//	$value=str_replace($search,'',$value);
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
//		for the student table
	$gender=array('M' => 'male', 'F' => 'female');
	$ncyear=array('N' => 'Nursery', 'R' => 'Reception', '1' => '1',
	'2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' =>
	'7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' =>
	'12', '13' => '13', '14' => '14');	
	$yeargroup_id=array('N' => 'Nursery', 'R' => 'Reception', '1' => '1',
	'2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' =>
	'7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' =>
	'12', '13' => '13', '14' => '14');	

//		for the info table
	$boarder=array('N' => 'notaboarder','B' => 'boarder','6' =>
	'boarder,sixnightsorless', '7' => 'boarder,sevennights');
	$religion=array('NOT' => 'informationnotobtained', 
		'BU' => 'buddhist', 'CH' => 'christian', 'HI' =>
		'hindu', 'JE' => 'jewish', 'MU' => 'muslim', 'NO' => 'No
		religion', 'OT' => 'otherreligion', 'SI' => 'sikh');
	$reledu=array('A' => 'attendsreligiouseducation', 
		'W' => 'withdrawnfromreligiouseducation');
	$relwo=array('A' => 'attendscollectivewoship', 
		'W' => 'withdrawnfromcollectiveworthship');
	$parttime=array('N' => 'no', 'Y' => 'yes');
	$sen=array('N' => 'no', 'Y' => 'yes');
	$medical=array('N' => 'no', 'Y' => 'yes');
	$incare=array('N' => 'no', 'Y' => 'yes');
	$firstlanguage=array('ENG' => 'english', 
	'ENB' => 'believedtobeenglish', 'OTH' => 'other', 'NOT' => 'informationnotobtained');
	$enrolstatus=array('C' => 'current', 'P' => 'previous', 'G' =>
	'guestpupil', 'S' => 'currentsubsidary (dualregistration)', 'M'
	=> 'currentmain(dualregistration)');
	$transportmode=array('NOT' => 'informationnotobtained', 'F' =>
	'onfoot', 'C' => 'privatecar', 'T' => 'train', 'B' => 'bus', 'S'
	=> 'schoolbus',);

//			for the gidsid table
	$relationship=array('NOT' => 'informationnotobtained', 'CAR' =>
	'carer', 'DOC' => 'doctor', 'FAM' => 'otherfamilymember', 'PAM'
	=> 'mother', 'PAF' => 'father', 'OTH' => 'othercontact', 'STP' =>
	'stepparent', 'REL' => 'otherrelative', 'SWR' => 'socialworker', 
						'RLG' => 'religiouscontact');
	$responsibility=array('N' => 'noparentalresponsibility', 'Y' => 'parentalresponsibility');

//			for the phone table
	$phonetype=array('H' => 'homephone', 'W' => 'workphone', 'M' =>
	'mobilephone', 'F' => 'faxnumber', 'O' => 'otherphone');

//			for the gidaid table
	$addresstype=array('H' => 'home', 'W' => 'work', 'V' =>
	'holiday', 'O' => 'other');

//			for the report table
	$component=array('None' => 'Not Applied', 'N' => 'Non-Validating', 'V' =>
	'Validating', 'A' => 'All');

//			for the assessment tables
	$resultstatus=array('I' => 'Interim', 'R' => 'Result', 'T' =>
	'Target', 'P' => 'Provisional result', 'E' => 'Estimate');

	$resultqualifier=array('PC' => 'Percentage',
	'NV' => 'Numerical Value', 'KG' => 'Effort/Attainment A-D Grade', 
	'KN' => 'Effort/Attainment 1-5 Scale',
	'KC' => 'Cause for Concern Flag',
	 'EG' => 'Examination/Post-14 Grade', 'EL'
		=> 'EAL Assessment Level (Pre-NC level 2)', 'IA' =>
	'International Baccalaureate Assessment', 'IG' =>
	'International Baccalaureate Grade Point', 'IR' =>
	'International Baccalaureate Final Result', 'IS' =>
	'International Baccalaureate Aggregate Grad Points', 'NA' =>
	'National Curriculum Age Standardised Score', 'NF' =>
	'National Curriculum Level with Fine Grading', 'NL' =>
	'National Curriculum Level', 'NM' =>
	'National Curriculum Task/Test Mark', 'NR' =>
	'National Curriculum Test Raw Score', 'NS' =>
	'National Curriculum Summary (Aggregate) Mark');

	$method=array('DD' => 'Disapplied', 'NA' => 'Not Assessed', 'TA' =>
	'Teacher Assessment', 'TT' => 'Task/Test', '35' => 'SATS Tier
	3-5', '36' => 'SATS Tier 3-6', '46' =>
	'SATS Tier 4-6', '57' => 'SATS Tier 5-7', '68' => 'SATS Tier 6-8',
	'GF' => 'GCSE Full Course', 'GH' =>
	'GCSE Short Course', 'A2' => 'A2 Level', 'AS' => 'AS Level', 'AV'
	=> 'Advanced GNVQ', 'IB' =>
	'International Baccalaureate', 'NVQ' => 'NVQ', 'KS' => 'Key
	Skills', 'CE' => 'Certificate of Achievement');

	$season=array('S' => 'Summer', 'W' => 'Winter', 'M' =>
	'Modular/Continuous', '1' => 'January', '2' => 'Feburary', '3' =>
	'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' =>
	'July', '8' => 'August', '9' => 'September', 
	'a' => 'October', 'b' => 'November', 'c' => 'December');

//			for the sen table
	$senprovision=array('N' => 'notonregister', 'A'=> 'schoolaction',
	'P'=> 'schoolactionplus', 'Q'=>
	'schoolactionplusandstatutoryassessment', 'S'=> 'statemented');
	$senranking=array('1' => 'level 1', '2' => 'level 2', '3' => 'level 3');
	$sentype=array('SPLD' => 'specificlearningdifficulty(dyslexia)', 
				   'MLD' => 'moderatelearningdifficulty', 'SLD' =>
	'severelearningdifficulty', 'PMLD' =>
				'profoundanmultiplelearningdifficulty', 'EBD' =>
	'emotionalandbehaviouraldifficulty', 'SCD' =>
	'speechorcommunicationdifficulty', 
				   'HI' => 'hearingimpairment', 
		'VI' => 'visualimpairment', 'MSI' =>
	'multi-sensoryimpairment', 
				   'PD' => 'physicaldisability', 'AUT' => 'autism',
		'OTH' => 'otherdifficulty/disability');
	$sencurriculum=array('A' => 'allsubjects', 'M' =>
	'modifiedcurriculum', 'D' => 'curriculumdisapplied');

//			for the exclusions table
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
?>
