<?php
/**			   								lib/functions.php   
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
 *
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2008
 *	@version	
 *	@since				
 */



/**
 * Generic email header for automatic emails sent by ClaSS.
 *
 *	@return string
 */
function emailHeader(){
	global $CFG;
	$headers = 'From: ClaSS@'.$CFG->siteaddress ."\r\n" . 
	  'Reply-To: '.$CFG->emailnoreply . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	return $headers;
	}

/**
 * Needs a file handle and then prepares and writes a single row of csv
 *
 *	@param stream[$handle] 
 *  @param array[$row]
 *	@param string[$fd]
 *	@param string[$quot]
 *	@return integer 
 */
function file_putcsv($handle, $row, $fd=',', $quot='"'){
	$str='';
	foreach($row as $cell){
		$cell=str_replace(Array($quot,"\n"),
						  Array($quot. $quot,''),
						  $cell);
		if(strchr($cell, $fd)!==FALSE || strchr($cell, $quot)!==FALSE){
			$str.=$quot. $cell. $quot. $fd;
			}
		else{
			$str.=$cell. $fd;
			}
		}
	fputs($handle, substr($str, 0, -1)."\n");
	return strlen($str);
	}


/**
 * This is because the PEAR xml stuff deals with empty strings by
 * closing a tag < /> like so in stead of <></> like so and the xslt
 * chokes. So, this is called before an array is transformed to xml to
 * turn all empty strings to a single space to get <> </>.
 *
 *	@param array[$array] 
 *	@return array modified value
 */
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


/**
 * For compatibility with utf8
 *
 *	@param string[$value] a string to be made lowercase
 *	@return string new alfabetic string to lowercase
 *
 */
function good_strtolower($value){
	$value=mb_strtolower($value, mb_detect_encoding($value));
	return $value;
	}


/**
 *	This takes international accented characters - have only bothered
 *	to cover spanish ones in the list - and transliterates them to
 *	their nearest ascii equivalent, making them safe for email
 *	addresses and urls. Needed for the eportfolio functions.
 *  This can be done properly with:
 *				 iconv('UTF-8', 'ASCII//TRANSLIT', $surname);
 * But it seems hyper-sensitive to the locales setting on the server
 * and can't be relied on.
 *
 * TODO: complete this list of codes.
 *
 *	@param string[$str] input as UTF-8 encoded string
 *	@return string output as ASCII encoded string
 *
 */
function utf8_to_ascii($str){
	$codes=array(
				 chr(0x00C8)=>'E',
				 chr(0x00E8)=>'e',
				 chr(0x00C9)=>'E',
				 chr(0x00E9)=>'e',
				 chr(0x00CD)=>'I',
				 chr(0x00ED)=>'i',
				 chr(0x00D1)=>'N',
				 chr(0x00F1)=>'n',
				 chr(0x00D3)=>'O',
				 chr(0x00F3)=>'o',
				 chr(0x00DA)=>'U',
				 chr(0x00FA)=>'u',
				 chr(0x00C1)=>'A',
				 chr(0x00E1)=>'a',
				 chr(0x00AA)=>'A',
				 chr(0x0061)=>'a'
				 );
	$str=utf8_decode($str);
	$str=str_replace(
					 array_keys($codes),
					 array_values($codes),
					 $str
					 );
    return $str;
	}

/**
 * Should only be used when writing a string for use by javascript
 *
 *	@param string[$value] string to be evaluated
 *	@return string 
 */
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

/**
 * Attempts to get rid of any nasties before a mysql insert
 *
 *	@param string[$value]
 *	@return string a clean value
 */
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
	//$search=array(' * ','<','>');
	$search=array(' * ');
	$value=str_replace($search,'',$value);
	//   	$value=eregi_replace('[^-.?,!;()+:[:digit:][:space:][:alpha:]]','', $value);
	$value=addslashes($value);
	return $value;
 	}


/**
 * Does some simple data validation for input
 *
 *	@param string[$value] input value to be evaluated
 *	@param string[$format]
 *	@param string[$field_name]
 *	@return string checked value
 *
 */
function checkEntry($value, $format='', $field_name=''){
	$value=trim($value);
	$value=good_strtolower($value);
	if($field_name!='email'){$value=ucwords($value);}	
	$field_type=split('[()]', $format);

	if($field_name=='form_id'){$value=strtoupper($value);}

	if($field_type[0]=='date'){
		/*assumes date order day/month/year, php wants year-month-day*/
		$date=split('[/]',$value);
		$value=$date[2].'-'.$date[1].'-'.$date[0];
		//$value=$date[0].'-'.$date[1].'-'.$date[2];
		}
	elseif($field_type[0]=='enum'){
		$value=strtoupper($value);
		$value=checkEnum($value, $field_name);
		}
	elseif($field_type[0]=='time'){
		$time=split('[:]',$value);
		/*should be validating but no!!!*/
		//$value=$date[2].'-'.$date[1].'-'.$date[0];
		}
	return $value;
	}

/**
 * Validates a value which claims to be compatible with a enum field
 *
 *	@param string[$value]
 *	@param string[$field_name]
 *	@return string returns the same value if it is compatible with a enum field
 *
 */
function checkEnum($value, $field_name) {
	$enumarray=getEnumArray($field_name);
	if(array_key_exists($value,$enumarray)){
		}
	else{
		$value='';
		}
	return $value;
	}

/** 
 * Uses the enum $value for the enum $field_name to look up and return the $description
 * call this before displaying the lang string
 *
 *	@param string[$value] 
 *	@param string[$field_name]
 *	@return string
 *
 */
function displayEnum($value,$field_name){
	$value=strtoupper($value);
	$enumarray=getEnumArray($field_name);
	if(isset($enumarray[$value])){$description=$enumarray[$value];}
	else{
		$description='';
		//trigger_error('WRONG ENUM value: '.$value.' field: '.$field_name,E_USER_WARNING);
		}
	return $description;
	}


/**
 * Returns the array of valid enum values and their meanings for a
 * given field name (which clearly have to be named uniquely in the
 * db). It will check for the toplevel file schoolarrays.php which can
 * contain any local customisations.
 *
 *	@param string[$field_name]
 *	@return array with valid values
 */
function getEnumArray($field_name){
	global $CFG;
	/*for the student table*/
	$gender=array('M' => 'male', 'F' => 'female');

	/*for the info table*/
	$boarder=array('N' => 'notaboarder',
				   'B' => 'boarder',
				   'H' => 'hostfamily',
				   '6' => 'boardersixnightsorless', 
				   '7' => 'boardersevennights');
	$religion=array('NOT' => 'informationnotobtained', 
					'BU' => 'buddhist', 
					'CH' => 'christian', 
					'HI' => 'hindu', 
					'JE' => 'jewish', 
					'MU' => 'muslim', 
					'NO' => 'noreligion', 
					'OT' => 'otherreligion', 
					'SI' => 'sikh'
					);
	$reledu=array('A' => 'attendsreligiouseducation', 
				  'W' => 'withdrawnfromreligiouseducation');
	$relwo=array('A' => 'attendscollectivewoship', 
				 'W' => 'withdrawnfromcollectiveworthship');
	$parttime=array('N' => 'no', 'Y' => 'yes');
	$staffchild=array('N' => 'no', 'Y' => 'yes');
	$sen=array('N' => 'no', 'Y' => 'yes');
	$closed=array('N' => 'open', 'Y' => 'closed');
	$medical=array('N' => 'no', 'Y' => 'yes');
	$incare=array('N' => 'no', 'Y' => 'yes');
	$roomcategory=array('' => '', 'GL' => 'groupleader');
	$building=array('' => '');
	$bed=array('' => '');

	/*for the travelevent table*/
	$type=array('NOT' => 'informationnotobtained', 
				'A' => 'arrival', 
				'D' => 'departure'
				);

	/*for the orderbudget table*/
	$currency=array('0' => 'euros', 
					'1' => 'pounds' 
					);
	$credit=array('0' => 'debit', 
				  '1' => 'credit' 
				  );
	$budgetyearcode=array('2007' => '07', 
						  '2008' => '08', 
						  '2009' => '09', 
						  '2010' => '10', 
						  '2011' => '11', 
						  '2012' => '12' 
						  );
	$inactive=array('0' => 'no', 
					'1' => 'yes'
					);


	/*codes from CBDS 2007, including deprecated six for compatibility*/
	/*not always the same as ISO 639-2 is the alpha-3 code for language!*/
	$language=array('ENG'=>'english', 
					'ENB'=>'believedtobeenglish', 
					'OTB'=>'believedtobeotherthanenglish',
					'FRN'=>'french',
					'ITA'=>'italian',
					'NOR'=>'norwegian',
					'SPA'=>'spanish',
					//'CZE'=>'czech',
					'CHI'=>'chinese',
					//'OTH'=>'other', 
					'OTL'=>'otherlanguage',
					'NOT'=>'informationnotobtained'
/*
					'REF'=>'refused',
					'ACL'=>'Acholi',
					'ADA'=>'Adangme',
					'AFA'=>'Afar-Saho',
					'AFK'=>'Afrikaans',
					'AKA'=>'Akan/Twi-Fante',
					'AKAF'=>'Akan (Fante)',
					'AKAT'=>'Akan (Twi/Asante)',
					'ALB'=>'Albanian/Shqip',
					'ALU'=>'Alur',
					'AMR'=>'Amharic',
					'ARA'=>'Arabic',
					'ARAA'=>'Arabic (Any Other)',
					'ARAG'=>'Arabic (Algeria)',
					'ARAI'=>'Arabic (Iraq)',
					'ARAM'=>'Arabic (Morocco)',
					'ARAS'=>'Arabic (Sudan)',
					'ARAY'=>'Arabic (Yemen)',
					'ARM'=>'Armenian',
					'ASM'=>'Assamese',
					'ASR'=>'Assyrian/Aramaic',
					'AYB'=>'Anyi-Baule',
					'AYM'=>'Aymara',
					'AZE'=>'Azeri',
					'BAI'=>'Bamileke (Any)',
					'BAL'=>'Balochi',
					'BEJ'=>'Beja/Bedawi',
					'BEL'=>'Belarusian',
					'BEM'=>'Bemba',
					'BHO'=>'Bhojpuri',
					'BIK'=>'Bikol',
					'BLT'=>'Balti Tibetan',
					'BMA'=>'Burmese/Myanma',
					'BNG'=>'Bengali',
					'BNGA'=>'Bengali (Any Other)',
					'BNGC'=>'Bengali (Chittagong/Noakhali)',
					'BNGS'=>'Bengali (Sylheti)',
					'BSL'=>'British Sign Language',
					'BSQ'=>'Basque/Euskara',
					'BUL'=>'Bulgarian',
					'CAM'=>'Cambodian/Khmer',
					'CAT'=>'Catalan/Valencian',
					'CCE'=>'Caribbean Creole English',
					'CCF'=>'Caribbean Creole French',
					'CGA'=>'Chaga',
					'CGR'=>'Chattisgarhi/Khatahi',
					'CHE'=>'Chechen',
					'CHIA'=>'Chinese (Any Other)',
					'CHIC'=>'Chinese (Cantonese)',
					'CHIH'=>'Chinese (Hokkien/Fujianese)',
					'CHIK'=>'Chinese (Hakka)',
					'CHIM'=>'Chinese (Mandarin/Putonghua)',
					'CKW'=>'Chokwe',
					'CRN'=>'Cornish',
					'CTR'=>'Chitrali/Khowar',
					'CWA'=>'Chichewa/Nyanja',
					'CYM'=>'Welsh/Cymraeg',
					'DAN'=>'Danish',
					'DGA'=>'Dagaare',
					'DGB'=>'Dagbane',
					'DIN'=>'Dinka/Jieng',
					'DUT'=>'Dutch/Flemish',
					'DZO'=>'Dzongkha/Bhutanese',
					'EBI'=>'Ebira',
					'EDO'=>'Edo/Bini',
					'EFI'=>'Efik-Ibibio',
					'ESA'=>'Esan/Ishan',
					'EST'=>'Estonian',
					'EWE'=>'Ewe',
					'EWO'=>'Ewondo',
					'FAN'=>'Fang',
					'FIJ'=>'Fijian',
					'FIN'=>'Finnish',
					'FON'=>'Fon',
					'FUL'=>'Fula/Fulfulde-Pulaar',
					'GAA'=>'Ga',
					'GAE'=>'Gaelic/Irish',
					'GAL'=>'Gaelic (Scotland)',
					'GEO'=>'Georgian',
					'GER'=>'German',
					'GGO'=>'Gogo/Chigogo',
					'GKY'=>'Kikuyu/Gikuyu',
					'GLG'=>'Galician/Galego',
					'GRE'=>'Greek',
					'GREA'=>'Greek (Any Other)',
					'GREC'=>'Greek (Cyprus)',
					'GRN'=>'Guarani',
					'GUJ'=>'Gujarati',
					'GUN'=>'Gurenne/Frafra',
					'GUR'=>'Gurma',
					'HAU'=>'Hausa',
					'HDK'=>'Hindko',
					'HEB'=>'Hebrew',
					'HER'=>'Herero',
					'HGR'=>'Hungarian',
					'HIN'=>'Hindi',
					'IBA'=>'Iban',
					'IDM'=>'Idoma',
					'IGA'=>'Igala',
					'IGB'=>'Igbo',
					'IJO'=>'Ijo (Any)',
					'ILO'=>'Ilokano',
					'ISK'=>'Itsekiri',
					'ISL'=>'Icelandic',
					'ITA'=>'Italian',
					'ITAA'=>'Italian (Any Other)',
					'ITAN'=>'Italian (Napoletan)',
					'ITAS'=>'Italian (Sicilian)',
					'JAV'=>'Javanese',
					'JIN'=>'Jinghpaw/Kachin',
					'JPN'=>'Japanese',
					'KAM'=>'Kikamba',
					'KAN'=>'Kannada',
					'KAR'=>'Karen (Any)',
					'KAS'=>'Kashmiri',
					'KAU'=>'Kanuri',
					'KAZ'=>'Kazakh',
					'KCH'=>'Katchi',
					'KGZ'=>'Kirghiz/Kyrgyz',
					'KHA'=>'Khasi',
					'KHY'=>'Kihaya/Luziba',
					'KIN'=>'Kinyarwanda',
					'KIR'=>'Kirundi',
					'KIS'=>'Kisi (West Africa)',
					'KLN'=>'Kalenjin',
					'KMB'=>'Kimbundu',
					'KME'=>'Kimeru',
					'KNK'=>'Konkani',
					'KNY'=>'Kinyakyusa-Ngonde',
					'KON'=>'Kikongo',
					'KOR'=>'Korean',
					'KPE'=>'Kpelle',
					'KRI'=>'Krio',
					'KRU'=>'Kru (Any)',
					'KSI'=>'Kisii/Ekegusii (Kenya)',
					'KSU'=>'Kisukuma',
					'KUR'=>'Kurdish',
					'KURA'=>'Kurdish (Any Other)',
					'KURM'=>'Kurdish (Kurmanji)',
					'KURS'=>'Kurdish (Sorani)',
					'LAO'=>'Lao',
					'LBA'=>'Luba',
					'LBAC'=>'Luba (Chiluba/Tshiluba)',
					'LBAK'=>'Luba (Kiluba)',
					'LGA'=>'Luganda',
					'LGB'=>'Lugbara',
					'LGS'=>'Lugisu/Lumasaba',
					'LIN'=>'Lingala',
					'LIT'=>'Lithuanian',
					'LNG'=>'Lango (Uganda)',
					'LOZ'=>'Lozi/Silozi',
					'LSO'=>'Lusoga',
					'LTV'=>'Latvian',
					'LTZ'=>'Luxemburgish',
					'LUE'=>'Luvale/Luena',
					'LUN'=>'Lunda',
					'LUO'=>'Luo (Kenya/Tanzania)',
					'LUY'=>'Luhya (Any)',
					'MAG'=>'Magahi',
					'MAI'=>'Maithili',
					'MAK'=>'Makua',
					'MAN'=>'Manding/Malinke',
					'MANA'=>'Manding/Malinke (Any Other)',
					'MANB'=>'Bambara',
					'MANJ'=>'Dyula/Jula',
					'MAO'=>'Maori',
					'MAR'=>'Marathi',
					'MAS'=>'Maasai',
					'MDV'=>'Maldivian/Dhivehi',
					'MEN'=>'Mende',
					'MKD'=>'Macedonian',
					'MLG'=>'Malagasy',
					'MLM'=>'Malayalam',
					'MLT'=>'Maltese',
					'MLY'=>'Malay/Indonesian',
					'MLYA'=>'Malay (Any Other)',
					'MLYI'=>'Indonesian/Bahasa Indonesia',
					'MNA'=>'Magindanao-Maranao',
					'MNG'=>'Mongolian (Khalkha)',
					'MNX'=>'Manx Gaelic',
					'MOR'=>'Moore/Mossi',
					'MSC'=>'Mauritian/Seychelles Creoley',
					'MUN'=>'Munda (Any)',
					'MYA'=>'Maya (Any)',
					'NAH'=>'Nahuatl/Mexicano',
					'NAM'=>'Nama/Damara',
					'NBN'=>'Nubian (Any)',
					'NDB'=>'Ndebele',
					'NDBS'=>'Ndebele (South Africa)',
					'NDBZ'=>'Ndebele (Zimbabwe)',
					'NEP'=>'Nepali',
					'NUE'=>'Nuer/Naadh',
					'NUP'=>'Nupe',
					'NWA'=>'Newari',
					'NZM'=>'Nzema',
					'OAM'=>'Ambo/Oshiwambo',
					'OAMK'=>'Ambo (Kwanyama)',
					'OAMN'=>'Ambo (Ndonga)',
					'OGN'=>'Ogoni (Any)',
					'ORI'=>'Oriya',
					'ORM'=>'Oromo',
					'PAG'=>'Pangasinan',
					'PAM'=>'Pampangan',
					'PAT'=>'Pashto/Pakhto',
					'PHA'=>'Pahari/Himachali (India)',
					'PHR'=>'Pahari (Pakistan)',
					'PNJ'=>'Panjabi',
					'PNJA'=>'Panjabi (Any Other)',
					'PNJG'=>'Panjabi (Gurmukhi)',
					'PNJM'=>'Panjabi (Mirpuri)',
					'PNJP'=>'Panjabi (Pothwari)',
					'POL'=>'Polish',
					'POR'=>'Portuguese',
					'PORA'=>'Portuguese (Any Other)',
					'PORB'=>'Portuguese (Brazil)',
					'PRS'=>'Persian/Farsi',
					'PRSA'=>'Farsi/Persian (Any Other)',
					'PRSD'=>'Dari Persian',
					'PRST'=>'Tajiki Persian',
					'QUE'=>'Quechua',
					'RAJ'=>'Rajasthani/Marwari',
					'RME'=>'Romany/English Romanes',
					'RMI'=>'Romani (International)',
					'RMN'=>'Romanian',
					'RMNM'=>'Romanian (Moldova)',
					'RMNR'=>'Romanian (Romania)',
					'RMS'=>'Romansch',
					'RNY'=>'Runyakitara',
					'RNYN'=>'Runyankore-Ruchiga',
					'RNYO'=>'Runyoro-Rutooro',
					'RUS'=>'Russian',
					'SAM'=>'Samoan',
					'SCB'=>'Serbian/Croatian/Bosnian',
					'SCBB'=>'Bosnian',
					'SCBC'=>'Croatian',
					'SCBS'=>'Serbian',
					'SCO'=>'Scots',
					'SHL'=>'Shilluk/Cholo',
					'SHO'=>'Shona',
					'SID'=>'Sidamo',
					'SIO'=>'Sign Language (Other)',
					'SLO'=>'Slovak',
					'SLV'=>'Slovenian',
					'SND'=>'Sindhi',
					'SNG'=>'Sango',
					'SNH'=>'Sinhala',
					'SOM'=>'Somali',
					'SRD'=>'Sardinian',
					'SRK'=>'Siraiki',
					'SSO'=>'Sotho/Sesotho',
					'SSOO'=>'Sotho/Sesotho (Southern)',
					'SSOT'=>'Sotho/Sesotho (Northern)',
					'SSW'=>'Swazi/Siswati',
					'STS'=>'Tswana/Setswana',
					'SUN'=>'Sundanese',
					'SWA'=>'Swahili/Kiswahili',
					'SWAA'=>'Swahili (Any Other)',
					'SWAC'=>'Comorian Swahili',
					'SWAK'=>'Swahili (Kingwana)',
					'SWAM'=>'Swahili (Brava/Mwiini)',
					'SWAT'=>'Swahili (Bajuni/Tikuu)',
					'SWE'=>'Swedish',
					'TAM'=>'Tamil',
					'TEL'=>'Telugu',
					'TEM'=>'Temne',
					'TES'=>'Teso/Ateso',
					'TGE'=>'Tigre',
					'TGL'=>'Tagalog/Filipino',
					'TGLF'=>'Filipino',
					'TGLG'=>'Tagalog',
					'TGR'=>'Tigrinya',
					'THA'=>'Thai',
					'TIB'=>'Tibetan',
					'TIV'=>'Tiv',
					'TMZ'=>'Berber/Tamazight',
					'TMZA'=>'Berber/Tamazight (Any Other)',
					'TMZK'=>'Berber/Tamazight (Kabyle)',
					'TMZT'=>'Berber (Tamashek)',
					'TNG'=>'Tonga/Chitonga (Zambia)',
					'TON'=>'Tongan (Oceania)',
					'TPI'=>'Tok Pisin',
					'TRI'=>'Traveller Irish/Shelta',
					'TSO'=>'Tsonga',
					'TUK'=>'Turkmen',
					'TUL'=>'Tulu',
					'TUM'=>'Tumbuka',
					'TUR'=>'Turkish',
					'UKR'=>'Ukrainian',
					'UMB'=>'Umbundu',
					'URD'=>'Urdu',
					'URH'=>'Urhobo-Isoko',
					'UYG'=>'Uyghur',
					'UZB'=>'Uzbek',
					'VEN'=>'Venda',
					'VIE'=>'Vietnamese',
					'VSY'=>'Visayan/Bisaya',
					'VSYA'=>'Visayan/Bisaya (Any Other)',
					'VSYH'=>'Hiligaynon',
					'VSYS'=>'Cebuano/Sugbuanon',
					'VSYW'=>'Waray/Binisaya',
					'WAP'=>'Wa-Paraok (South-East Asia)',
					'WCP'=>'West-African Creole Portuguese',
					'WOL'=>'Wolof',
					'WPE'=>'West-African Pidgin English',
					'XHO'=>'Xhosa',
					'YAO'=>'Yao/Chiyao (East Africa)',
					'YDI'=>'Yiddish',
					'YOR'=>'Yoruba',
					'ZND'=>'Zande',
					'ZUL'=>'Zulu',
					'ZZZ'=>'classificationpending'
*/
					);
	$ethnicity=array(''=>''
					 );
	$languagetype=array('F' => 'firstlanguage', 
						'M' => 'multiplefirstlanguage',
						'H' => 'home',
						'T' => 'tuition',
						'S' => 'secondlanguage',
						'C' => 'correspondence'
						);
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
						 'F' => 'onfoot', 
						 'C' => 'privatecar', 
						 'T' => 'train', 
						 'B' => 'bus', 
						 'S' => 'schoolbus'
						 );

	/*NOT an enum array but defines courses who don't do homework*/
	$nohomeworkcourses=array();

	/* For the orderaction table NB. 'lodged' has no entry in the
	 * orderaction table simply exists in the orderorder table. 
	 */
	$action=array('0'=>'lodged', 
				  '1'=>'authorised', 
				  '2'=>'placed', 
				  '3'=>'delivered', 
				  '4'=>'cancelled', 
				  '5'=>'closed',
				  '6'=>'process'
				  );

	/*for the gidsid table*/
	$priority=array('0'=>'first', 
					'1'=>'second', 
					'2'=>'third', 
					'3'=>'fourth');
	$mailing=array('0'=>'nomailing', 
				   '1'=>'allmailing', 
				   '2'=>'reportsonly');
	$title=array('0'=>'', 
				 '1'=>'mr', 
				 '2'=>'mrs', 
				 '3'=>'srd', 
				 '4'=>'srada',
				 '5'=>'miss',
				 '6'=>'dr',
				 '7'=>'ms'
				 );
	$relationship=array('NOT'=>'informationnotobtained', 
						'CAR'=>'carer', 
						'DOC'=>'doctor', 
						'FAM'=>'otherfamilymember', 
						'PAM'=>'mother', 
						'PAF'=>'father', 
						'OTH'=>'othercontact', 
						'STP'=>'stepparent', 
						'GRP'=>'grandparent', 
						'REL'=>'otherrelative', 
						'SWR'=>'socialworker', 
						'RLG'=>'religiouscontact', 
						'AGN'=>'agent', 
						'HFA'=>'hostfamily'
						);
	$responsibility=array('N'=>'noparentalresponsibility', 
						  'Y'=>'parentalresponsibility'
						  );

	/*for the phone table*/
	$phonetype=array('H'=>'homephone', 'W'=>'workphone', 
					 'M'=>'mobilephone', 'F'=>'faxnumber', 'O'=>'otherphone');

	/*for the gidaid table*/
	$addresstype=array('H'=>'home', 'W'=>'work', 
					   'V'=>'holiday', 'O'=>'other');

	/*for the report table*/
	$componentstatus=array('None'=>'notapplied', 
						   'N'=>'non-validating', 
						   'V'=>'validating', 
						   'O'=>'othervalidating', 
						   'AV'=>'allvalidating', 
						   'A'=>'all');
	$strandstatus=$componentstatus;

	/*for the assessment tables*/
	$resultstatus=array(
						//'I'=>'interim', 
						'R'=>'result', 
						'T'=>'target', 
						//'P'=>'provisionalresult', 
						'E'=>'estimate', 
						'S'=>'statistics');

	$season=array('S'=>'summer', 'W'=>'winter', 'M' =>
				  'modular/continuous', '1'=>'january', '2'=>'feburary', '3' =>
				  'march', '4'=>'april', '5'=>'may', '6'=>'june', 
				  '7'=>'july', '8'=>'august', '9'=>'september', 
				  'a'=>'october', 'b'=>'november', 'c'=>'december');
	$dayofweek=array('1'=>'monday', '2'=>'tuesday', '3' =>
				  'wednesday', '4'=>'thursday', '5'=>'friday', '6'=>'saturday', 
				  '7'=>'sunday');

	/*for the sen table*/
	$senprovision=array('N'=>'notonregister', 
						'A'=> 'schoolaction',
						'P'=> 'schoolactionplus', 
						'S'=> 'statemented');
	$senranking=array('1'=>'level 1', '2'=>'level 2', '3'=>'level 3');
	$sentype=array('SPLD'=>'specificlearningdifficulty(dyslexia)', 
				   'MLD'=>'moderatelearningdifficulty', 
				   'SLD'=>'severelearningdifficulty', 
				   'PMLD'=>'profoundandmultiplelearningdifficulty', 
				   'EBD'=>'emotionalandbehaviouraldifficulty', 
				   'SCD'=>'speechorcommunicationdifficulty', 
				   'HI'=>'hearingimpairment', 
				   'VI'=>'visualimpairment', 
				   'MSI'=>'multi-sensoryimpairment', 
				   'PD'=>'physicaldisability', 
				   'AUT'=>'autism',
				   'GT'=>'giftedandtalented',
				   'STF'=>'shorttermfailing',
				   'OTH'=>'otherdifficulty/disability',
				   'ENC'=>'enrolmentconcern',
				   'GNC'=>'generalconcern'
				   );
	$sencurriculum=array('A'=>'allsubject',
						 'M'=>'modifiedcurriculum', 
						 'D'=>'curriculumdisapplied');

	/*for the exclusions table*/
	$exclusionscategory=array('F'=>'fixed-term', 'P'=>'permanent', 'L'=>'lunchtime');

	$appeal=array('R'=>'appealrejected', 'S'=>'appealsuccesful');

	$session=array('NA'=>'NA', 'AM'=>'AM', 'PM'=>'PM');

	/* For the community table, does not list special types like
	 * yeargroup, formgroup, accomodation, family etc
	 */
	$community_type=array(''=>'', 
						  'ACADEMIC'=>'academic', 
						  'HOUSE'=>'house', 
						  'TUTOR'=>'tutorgroup', 
						  'TRIP'=>'trip', 
						  'REG'=>'registrationgroup', 
						  'TRANSPORT'=>'transport', 
						  'EXTRA'=>'other'
						  );

	/* For the list_studentfield script, not an enumarray at all!
	 */
	$studentfield=array(
						''=>'',
						'Surname'=>'surname', 
						'Gender'=>'gender', 
						'YearGroup'=>'yeargroup', 
						'RegistrationGroup'=>'formgroup', 
						'House'=>'house', 
						'DOB'=>'dateofbirth',
						'Nationality'=>'nationality',
						'SecondNationality'=>'secondnationality',
						'Birthplace'=>'placeofbirth',
						'CountryOfOrigin'=>'countryoforigin',
						'Language'=>'firstlanguage',
						'EmailAddress'=>'email',
						'EnrolNumber'=>'enrolmentnumber',
						'EntryDate'=>'schoolstartdate',
						'Language'=>'language',
						'MobilePhone'=>'mobilephone',
						'PersonalNumber'=>'personalnumber',
						'Postcode'=>'postcode',
						'EPFUsername'=>'epfusername',
						'FirstContact'=>'firstcontact',
						'FirstContactPhone'=>'firstcontactphone',
						'FirstContactEmailAddress'=>'firstcontactemailaddress',
						'FirstContactPostalAddress'=>'firstcontactaddress',
						'FirstContactProfession'=>'firstcontactprofession',
                        'FirstContactEPFUsername'=>'firstcontactepfu',
						'SecondContact'=>'secondcontact',
						'SecondContactPhone'=>'secondcontactphone',
						'SecondContactEmailAddress'=>'secondcontactemailaddress',
						'SecondContactPostalAddress'=>'secondcontactaddress',
						'SecondContactProfession'=>'secondcontactprofession',
                        'SecondContactEPFUsername'=>'secondcontactepfu'
						);
	/*for the register*/
	$absencecode=array(
						'O'=>'unauthorisedabsence',
						'I'=>'illness',
						'M'=>'medicaldentalappointments',
						'P'=>'approvedsportingactivity',
						'S'=>'studyleave',
						'V'=>'educationalvisitortrip',
						'B'=>'educatedoffsite',
						'E'=>'excluded',
						'F'=>'extendedfamilyholidayagreed',
						'G'=>'familyholidaynotagreeded',
						'H'=>'familyholidayagreed',
						'J'=>'interview',
						'L'=>'lateafterregisterclosedauthorised',
						'R'=>'religiousobservance',
						'T'=>'travellerabsence',
						'W'=>'workexperience',
						'C'=>'otherauthorisedcircumstances',
						'D'=>'dualregistrationattendingother',
						'N'=>'noreasonyetprovided',
						'U'=>'lateafterregisterclosed',
						'X'=>'untimetabledsessions',
						'Y'=>'enforcedclosure',
						'Z'=>'pupilnotonrole',
						'#'=>'schoolclosedtopupils'
						);

	/**
	 * ISO 3166-1 alpha-2 codes are two-letter country codes in the ISO
	 * 3166-1 standard to represent countries and dependent areas
	 * See http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
	 */
	$nationality=array(
					   'GB'=>'unitedkingdom',
					   'ES'=>'spain',
					   'AF'=>'afghanistan',
					   'AL'=>'albania',
					   'DZ'=>'algeria',
					   'AD'=>'andorra',
					   'AE'=>'unitedarabemirates',
					   'AI'=>'anguilla',
					   'AG'=>'antiguaandbarbuda',
					   'AO'=>'angola',
					   'AQ'=>'antarctica',
					   'AR'=>'argentina',
					   'AM'=>'armenia',
					   'AW'=>'aruba',
					   'AS'=>'americansamoa',
					   'AT'=>'austria',
					   'AU'=>'australia',
					   'AZ'=>'azerbaijan',
					   'BS'=>'bahamas',
					   'BH'=>'bahrain',
					   'BB'=>'barbados',
					   'BD'=>'bangladesh',
					   'BE'=>'belgium',
					   'BY'=>'belarus',
					   'BZ'=>'belize',
					   'BJ'=>'benin',
					   'BM'=>'bermuda',
					   'BT'=>'bhutan',
					   'BO'=>'bolivia',
					   'BA'=>'bosniaandherzegovina',
					   'BV'=>'bouvetisland',
					   'BW'=>'botswana',
					   'BF'=>'burkinafaso',
					   'BG'=>'bulgaria',
					   'BI'=>'burundi',
					   'BR'=>'brazil',
					   'BN'=>'bruneidarussalam',
					   'KH'=>'cambodia',
					   'CM'=>'cameroon',
					   'CA'=>'canada',
					   'KY'=>'caymanislands',
					   'CF'=>'centralafricanrepublic',
					   'CV'=>'capeverde',
					   'TD'=>'chad',
					   'CL'=>'chile',
					   'CN'=>'china',
					   'CX'=>'christmasisland',
					   'CC'=>'cocoskeelingislands',
					   'KM'=>'comoros',
					   'CG'=>'congo',
					   'CI'=>'cotedivoire',
					   'CK'=>'cookislands',
					   'CO'=>'colombia',
					   'CR'=>'costarica',
					   'HR'=>'croatia',
					   'CU'=>'cuba',
					   'CY'=>'cyprus',
					   'CZ'=>'czechrepublic',
					   'DJ'=>'djibouti',
					   'DK'=>'denmark',
					   'DM'=>'dominica',
					   'DO'=>'dominicanrepublic',
					   'TP'=>'easttimor',
					   'EC'=>'ecuador',
					   'EG'=>'egypt',
					   'SV'=>'elsalvador',
					   'ER'=>'eritrea',
					   'EE'=>'estonia',
					   'ET'=>'ethiopia',
					   'GQ'=>'equatorialguinea',
					   'FK'=>'falklandislands',
					   'FO'=>'faroeislands',
					   'FJ'=>'fiji',
					   'FI'=>'finland',
					   'FR'=>'france',
					   'FX'=>'francemetropolitan',
					   'GF'=>'frenchguiana',
					   'PF'=>'frenchpolynesia',
					   'TF'=>'frenchsouthernterritories',
					   'GA'=>'gabon',
					   'GM'=>'gambia',
					   'DE'=>'germany',
					   'GE'=>'georgia',
					   'GH'=>'ghana',
					   'GI'=>'gibraltar',
					   'GR'=>'greece',
					   'GL'=>'greenland',
					   'GD'=>'grenada',
					   'GN'=>'guinea',
					   'GP'=>'guadeloupe',
					   'GU'=>'guam',
					   'GT'=>'guatemala',
					   'GW'=>'guineabissau',
					   'GY'=>'guyana',
					   'HT'=>'haiti',
					   'HM'=>'heardandmcdonaldislands',
					   'HN'=>'honduras',
					   'HK'=>'hongkong',
					   'HU'=>'hungary',
					   'IS'=>'iceland',
					   'IN'=>'india',
					   'IO'=>'britishindianoceanterritory',
					   'ID'=>'indonesia',
					   'IR'=>'iran',
					   'IQ'=>'iraq',
					   'IE'=>'ireland',
					   'IL'=>'israel',
					   'IT'=>'italy',
					   'JM'=>'jamaica',
					   'JO'=>'jordan',
					   'JP'=>'japan',
					   'KZ'=>'kazakhstan',
					   'KE'=>'kenya',
					   'KI'=>'kiribati',
					   'KO'=>'kosovo',
					   'KP'=>'koreademocraticpeoplesrepublic',
					   'KR'=>'korearepublic',
					   'KW'=>'kuwait',
					   'KG'=>'kyrgyzstan',
					   'LA'=>'lao',
					   'LV'=>'latvia',
					   'LB'=>'lebanon',
					   'LS'=>'lesotho',
					   'LI'=>'liechtenstein',
					   'LR'=>'liberia',
					   'LY'=>'libyanarabjamahiriya',
					   'LT'=>'lithuania',
					   'LU'=>'luxembourg',
					   'MO'=>'macau',
					   'MK'=>'macedonia',
					   'MG'=>'madagascar',
					   'MT'=>'malta',
					   'MW'=>'malawi',
					   'MV'=>'maldives',
					   'ML'=>'mali',
					   'MY'=>'malaysia',
					   'MQ'=>'martinique',
					   'MH'=>'marshallislands',
					   'MR'=>'mauritania',
					   'MU'=>'mauritius',
					   'MX'=>'mexico',
					   'FM'=>'micronesia',
					   'MD'=>'moldova',
					   'MC'=>'monaco',
					   'MN'=>'mongolia',
					   'MS'=>'montserrat',
					   'MA'=>'morocco',
					   'MZ'=>'mozambique',
					   'MM'=>'myanmar',
					   'NA'=>'namibia',
					   'NR'=>'nauru',
					   'NP'=>'nepal',
					   'NL'=>'netherlands',
					   'AN'=>'netherlandsantilles',
					   'NZ'=>'newzealand',
					   'NC'=>'newcaledonia',
					   'NI'=>'nicaragua',
					   'NE'=>'niger',
					   'NG'=>'nigeria',
					   'NU'=>'niue',
					   'NF'=>'norfolkisland',
					   'MP'=>'northernmarianaislands',
					   'NO'=>'norway',
					   'OM'=>'oman',
					   'PK'=>'pakistan',
					   'PS'=>'palestine',
					   'PA'=>'panama',
					   'PG'=>'papuanewguinea',
					   'PY'=>'paraguay',
					   'PE'=>'peru',
					   'PH'=>'philippines',
					   'PN'=>'pitcairn',
					   'PL'=>'poland',
					   'PT'=>'portugal',
					   'PW'=>'palau',
					   'PR'=>'puertorico',
					   'QA'=>'qatar',
					   'RE'=>'reunion',
					   'RO'=>'romania',
					   'RU'=>'russianfederation',
					   'RW'=>'rwanda',
					   'KN'=>'saintkittsandnevis',
					   'LC'=>'saintlucia',
					   'VC'=>'saintvincentandthegrenadines',
					   'WS'=>'samoa',
					   'SM'=>'sanmarino',
					   'ST'=>'saotomeandprincipe',
					   'SA'=>'saudiarabia',
					   'CS'=>'serbiaandmontenegro',
					   'SN'=>'senegal',
					   'SC'=>'seychelles',
					   'SL'=>'sierraleone',
					   'SG'=>'singapore',
					   'SI'=>'slovenia',
					   'SK'=>'slovakia',
					   'SB'=>'solomonislands',
					   'SO'=>'somalia',
					   'ZA'=>'southafrica',
					   'LK'=>'srilanka',
					   'SH'=>'sthelena',
					   'PM'=>'stpierreandmiquelon',
					   'SD'=>'sudan',
					   'SR'=>'suriname',
					   'SJ'=>'svalbardandjanmayenislands',
					   'SZ'=>'swaziland',
					   'SE'=>'sweden',
					   'CH'=>'switzerland',
					   'SY'=>'syrianarabrepublic',
					   'TW'=>'taiwan',
					   'TJ'=>'tajikistan',
					   'TZ'=>'tanzania',
					   'TH'=>'thailand',
					   'TG'=>'togo',
					   'TK'=>'tokelau',
					   'TO'=>'tonga',
					   'TT'=>'trinidadandtobago',
					   'TN'=>'tunisia',
					   'TM'=>'turkmenistan',
					   'TC'=>'turksandcaicos',
					   'TR'=>'turkey',
					   'TV'=>'tuvalu',
					   'UG'=>'uganda',
					   'UA'=>'ukraine',
					   'US'=>'unitedstatesofamerica',
					   'UY'=>'uruguay',
					   'UZ'=>'uzbekistan',
					   'VA'=>'vaticancitystate',
					   'VE'=>'venezuela',
					   'VG'=>'virginislandsbritish',
					   'VI'=>'virginislandsus',
					   'VN'=>'vietnam',
					   'VU'=>'vanuatu',
					   'WF'=>'wallisandfutunaislands',
					   'EH'=>'westernsahara',
					   'YE'=>'yemen',
					   'YT'=>'mayotte',
					   'ZM'=>'zambia',
					   'ZR'=>'zaire',
					   'ZW'=>'zimbabwe'
					   );

	//$profession=array(''=>'');

	$countryoforigin=$nationality;
	$secondnationality=$nationality;
	$country=$nationality;

	$fullpath=$CFG->installpath;
	if(file_exists($fullpath.'/schoolarrays.php')){include($fullpath.'/schoolarrays.php');}

	if(!isset($$field_name)){trigger_error('Not in enum: '.$field_name,E_USER_WARNING);}
	return $$field_name;
	}



/**
 * Sorts an array but is not utf8 friendly
 *  $sort_array[0]['name']='surname';
 *  $sort_array[0]['sort']='ASC';
 *  $sort_array[0]['case']=TRUE;
 *	
 *	@param array[$array]
 *	@param array[$sort]
 *	@return array sorted array
 */
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

/**
 * Lists the contents of a directory on the server, can limit by extension
 * Currently used only for the templates
 *
 *	@param string[$directory] 
 *	@param string[$extension]
 *	@return array directory content
 *
 */
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
	sort($results);
    return $results;
	}

/**
 * Reads content of csv file into array flines
 *
 *	@param $string[$file] file name
 *	@return array each line content
 *
 */
function fileRead($file){
	$flines=array();
   	while($in=fgetcsv($file,1000,',')){
		//(filename,maxrowsize,delimeter,enclosure)
		if($in[0]!=''){
			if($in[0]{0}!='#' & $in[0]{0}!='/'){$flines[]=$in;}
			}
		}
   	fclose($file);
	return $flines;
	}

/**
 * Opens a file ready for writing
 *
 *	@param string[$path] file name
 *	@return string[$file] file opened
 *
 */
function fileOpen($path){
   	$file=fopen($path, 'r');
   	if(!$file){
		$error[]='Unable to open remote file '.$path.'!'; 
		include('scripts/results.php');
		exit;
		}
	return $file;
	}

/**
 * Taken from moodlelib
 *	@param string[$element]
 *	@return string
 *
 */
function file_mimeinfo($element, $filename) {
    $mimeinfo = array (
        'xxx' =>array ('type'=>'document/unknown', 'icon'=>'unknown.gif'),
        '3gp' =>array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'ai'  =>array ('type'=>'application/postscript', 'icon'=>'image.gif'),
        'aif' =>array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
        'aiff'=>array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
        'aifc'=>array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
        'applescript' =>array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'asc' =>array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'asm' =>array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'au'  =>array ('type'=>'audio/au', 'icon'=>'audio.gif'),
        'avi' =>array ('type'=>'video/x-ms-wm', 'icon'=>'avi.gif'),
        'bmp' =>array ('type'=>'image/bmp', 'icon'=>'image.gif'),
        'c'   =>array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'cct' =>array ('type'=>'shockwave/director', 'icon'=>'flash.gif'),
        'cpp' =>array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'cs'  =>array ('type'=>'application/x-csh', 'icon'=>'text.gif'),
        'css' =>array ('type'=>'text/css', 'icon'=>'text.gif'),
        'dv'  =>array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
        'doc' =>array ('type'=>'application/msword', 'icon'=>'word.gif'),
        'dcr' =>array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'dif' =>array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
        'dir' =>array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'dxr' =>array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'eps' =>array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
        'gif' =>array ('type'=>'image/gif', 'icon'=>'image.gif'),
        'gtar'=>array ('type'=>'application/x-gtar', 'icon'=>'zip.gif'),
        'gz'  =>array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'gzip'=>array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'h'   =>array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'hpp' =>array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'hqx' =>array ('type'=>'application/mac-binhex40', 'icon'=>'zip.gif'),
        'html'=>array ('type'=>'text/html', 'icon'=>'html.gif'),
        'htm' =>array ('type'=>'text/html', 'icon'=>'html.gif'),
        'java'=>array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'jcb' =>array ('type'=>'text/xml', 'icon'=>'jcb.gif'),
        'jcl' =>array ('type'=>'text/xml', 'icon'=>'jcl.gif'),
        'jcw' =>array ('type'=>'text/xml', 'icon'=>'jcw.gif'),
        'jmt' =>array ('type'=>'text/xml', 'icon'=>'jmt.gif'),
        'jmx' =>array ('type'=>'text/xml', 'icon'=>'jmx.gif'),
        'jpe' =>array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
        'jpeg'=>array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
        'jpg' =>array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
        'jqz' =>array ('type'=>'text/xml', 'icon'=>'jqz.gif'),
        'js'  =>array ('type'=>'application/x-javascript', 'icon'=>'text.gif'),
        'latex'=> array ('type'=>'application/x-latex', 'icon'=>'text.gif'),
        'm'   =>array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'mov' =>array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'movie'=> array ('type'=>'video/x-sgi-movie', 'icon'=>'video.gif'),
        'm3u' =>array ('type'=>'audio/x-mpegurl', 'icon'=>'audio.gif'),
        'mp3' =>array ('type'=>'audio/mp3', 'icon'=>'audio.gif'),
        'mp4' =>array ('type'=>'video/mp4', 'icon'=>'video.gif'),
        'mpeg'=>array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
        'mpe' =>array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
        'mpg' =>array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
        'odt' =>array ('type'=>'application/vnd.oasis.opendocument.text', 'icon'=>'odt.gif'),
        'ott' =>array ('type'=>'application/vnd.oasis.opendocument.text-template', 'icon'=>'odt.gif'),
        'oth' =>array ('type'=>'application/vnd.oasis.opendocument.text-web', 'icon'=>'odt.gif'),
        'odm' =>array ('type'=>'application/vnd.oasis.opendocument.text-master', 'icon'=>'odt.gif'),
        'odg' =>array ('type'=>'application/vnd.oasis.opendocument.graphics', 'icon'=>'odt.gif'),
        'otg' =>array ('type'=>'application/vnd.oasis.opendocument.graphics-template', 'icon'=>'odt.gif'),
        'odp' =>array ('type'=>'application/vnd.oasis.opendocument.presentation', 'icon'=>'odt.gif'),
        'otp' =>array ('type'=>'application/vnd.oasis.opendocument.presentation-template', 'icon'=>'odt.gif'),
        'ods' =>array ('type'=>'application/vnd.oasis.opendocument.spreadsheet', 'icon'=>'odt.gif'),
        'ots' =>array ('type'=>'application/vnd.oasis.opendocument.spreadsheet-template', 'icon'=>'odt.gif'),
        'odc' =>array ('type'=>'application/vnd.oasis.opendocument.chart', 'icon'=>'odt.gif'),
        'odf' =>array ('type'=>'application/vnd.oasis.opendocument.formula', 'icon'=>'odt.gif'),
        'odb' =>array ('type'=>'application/vnd.oasis.opendocument.database', 'icon'=>'odt.gif'),
        'odi' =>array ('type'=>'application/vnd.oasis.opendocument.image', 'icon'=>'odt.gif'),
        'pct' =>array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'pdf' =>array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
        'php' =>array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'pic' =>array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'pict'=>array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'png' =>array ('type'=>'image/png', 'icon'=>'image.gif'),
        'pps' =>array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
        'ppt' =>array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
        'ps'  =>array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
        'qt'  =>array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'ra'  =>array ('type'=>'audio/x-realaudio', 'icon'=>'audio.gif'),
        'ram' =>array ('type'=>'audio/x-pn-realaudio', 'icon'=>'audio.gif'),
        'rhb' =>array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'rm'  =>array ('type'=>'audio/x-pn-realaudio', 'icon'=>'audio.gif'),
        'rtf' =>array ('type'=>'text/rtf', 'icon'=>'text.gif'),
        'rtx' =>array ('type'=>'text/richtext', 'icon'=>'text.gif'),
        'sh'  =>array ('type'=>'application/x-sh', 'icon'=>'text.gif'),
        'sit' =>array ('type'=>'application/x-stuffit', 'icon'=>'zip.gif'),
        'smi' =>array ('type'=>'application/smil', 'icon'=>'text.gif'),
        'smil'=>array ('type'=>'application/smil', 'icon'=>'text.gif'),
        'sqt' =>array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'swa' =>array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'swf' =>array ('type'=>'application/x-shockwave-flash', 'icon'=>'flash.gif'),
        'swfl'=>array ('type'=>'application/x-shockwave-flash', 'icon'=>'flash.gif'),
        'sxw' =>array ('type'=>'application/vnd.sun.xml.writer', 'icon'=>'odt.gif'),
        'stw' =>array ('type'=>'application/vnd.sun.xml.writer.template', 'icon'=>'odt.gif'),
        'sxc' =>array ('type'=>'application/vnd.sun.xml.calc', 'icon'=>'odt.gif'),
        'stc' =>array ('type'=>'application/vnd.sun.xml.calc.template', 'icon'=>'odt.gif'),
        'sxd' =>array ('type'=>'application/vnd.sun.xml.draw', 'icon'=>'odt.gif'),
        'std' =>array ('type'=>'application/vnd.sun.xml.draw.template', 'icon'=>'odt.gif'),
        'sxi' =>array ('type'=>'application/vnd.sun.xml.impress', 'icon'=>'odt.gif'),
        'sti' =>array ('type'=>'application/vnd.sun.xml.impress.template', 'icon'=>'odt.gif'),
        'sxg' =>array ('type'=>'application/vnd.sun.xml.writer.global', 'icon'=>'odt.gif'),
        'sxm' =>array ('type'=>'application/vnd.sun.xml.math', 'icon'=>'odt.gif'),
        'tar' =>array ('type'=>'application/x-tar', 'icon'=>'zip.gif'),
        'tif' =>array ('type'=>'image/tiff', 'icon'=>'image.gif'),
        'tiff'=>array ('type'=>'image/tiff', 'icon'=>'image.gif'),
        'tex' =>array ('type'=>'application/x-tex', 'icon'=>'text.gif'),
        'texi'=>array ('type'=>'application/x-texinfo', 'icon'=>'text.gif'),
        'texinfo' =>array ('type'=>'application/x-texinfo', 'icon'=>'text.gif'),
        'tsv' =>array ('type'=>'text/tab-separated-values', 'icon'=>'text.gif'),
        'txt' =>array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'wav' =>array ('type'=>'audio/wav', 'icon'=>'audio.gif'),
        'wmv' =>array ('type'=>'video/x-ms-wmv', 'icon'=>'avi.gif'),
        'asf' =>array ('type'=>'video/x-ms-asf', 'icon'=>'avi.gif'),
        'xls' =>array ('type'=>'application/vnd.ms-excel', 'icon'=>'excel.gif'),
        'xml' =>array ('type'=>'application/xml', 'icon'=>'xml.gif'),
        'xsl' =>array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'zip' =>array ('type'=>'application/zip', 'icon'=>'zip.gif')
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
 * Will reduce the $startarray to just those indexes listed in $fields
 * If fields is empty then the whole of $startarray is returned untouched
 *
 *	@param array[$startarray] initial array
 *	@param array[$fields] array with elements to select
 *	@return array resultant values
 */
function array_filter_fields($startarray,$fields){
	if(is_array($fields) and sizeof($fields)>0){
		while(list($index,$value)=each($startarray)){
			if(!in_array($index,$fields)){unset($startarray[$index]);}
			}
		}
	return $startarray;
	}


/**
 * Send an email (with attachments)
 *
 * Originally from moodlelib and altered for ClaSS. Now works only
 * with PEAR Mail of libphpmailer (the former is recommended for among
 * other things its queing of messages). Set $CFG -> emailsys to specify.
 *
 * @uses $CFG
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
function send_email_to($recipient, $from, $subject, $messagetext, $messagehtml='', $attachments='', $usetrueaddress=true, $replyto='', $replytoname='',$dbc=''){

    global $CFG;
	$success=false;
	if(empty($recipient)){
		return false;
		}
	if($CFG->emailoff=='yes'){
		return 'emailstop';
		}
	$subject=substr(stripslashes($subject), 0, 900);

	if(!isset($CFG->emailsys) or $CFG->emailsys=='phpmail'){

		include_once($CFG->phpmailerpath.'/class.phpmailer.php'); 

		$mail = new phpmailer;
		$mail->Version = $CFG->version;
		$mail->IsSMTP();
		if($CFG->debug=='on'){
			echo '<pre>' . "\n";
			$mail->SMTPDebug = true;
			}
		$mail->Host=$CFG->smtphosts;
		if($CFG->smtpuser){
			/* surely always need authentication? */
			$mail->SMTPAuth = true;
			$mail->Username = $CFG->smtpuser;
			$mail->Password = $CFG->smtppasswd;
			}

		/* for handling bounces */
		if(!empty($CFG->emailhandlebounces)){
			$mail->Sender = $CFG->emailhandlebounces;
			}
		else{
			$mail->Sender='';
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
		
		$mail->Subject = $subject;
		$mail->AddAddress($recipient,'');
		$mail->WordWrap = 79;
		/*
		  if(!empty($from->customheaders)){
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
			$mail->Encoding='quoted-printable';
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
			$success=true;
			}
		else{
			//mtrace('ERROR: '. $mail->ErrorInfo);
			tigger_error('phplib mailer send failure: '. $mail->ErrorInfo,E_USER_WARNING);
			}
		}
	elseif(isset($CFG->emailsys) and $CFG->emailsys=='pearmail'){
	
		/* PEAR MAIL: under this system, every mail is queued 
		 * for the cronjob to send it at a later stage
		 * 
		 */
		require_once "Mail/Queue.php";
		require_once 'Mail/mime.php';

		$db_options['type']='db';
		$db_options['dsn']=db_connect(false);
		$db_options['mail_table']='message_event';

		$mail_options['driver']='smtp';
		$mail_options['host']=$CFG->smtphosts;
		$mail_options['port']=25;
		$mail_options['auth']=true;
		$mail_options['username']=$CFG->smtpuser;
		$mail_options['password']=$CFG->smtppasswd;

		$mail_queue=& new Mail_Queue($db_options, $mail_options);

		if(is_string($from)){
			$from_name=$from;
			$from= $CFG->emailnoreply;
			}
		else{
			$from_name='ClaSS';
			$from=$CFG->emailnoreply;
			}

		/* message header */
		$hdrs = array( 'From'    => $from,
					   'To'      => $recipient,
					   'Subject' => $subject  );

		/* we use Mail_mime() to construct a valid mail */
		$mime =& new Mail_mime();
		if($messagehtml!=''){
			$mime->setHTMLBody($messagehtml);
			}
		else{
			$mime->setTXTBody($messagetext);
			}

		if(is_array($attachments)){
			while(list($index,$attachment)=each($attachments)){
				if(is_file($attachment['filepath'])){ 
					$mime->addAttachment($attachment['filepath']);		
					}
				}
			}

		/* next sentence has to be written after everything else */
		$body = $mime->get();
		$hdrs = $mime->headers($hdrs);

		/* Put message into queue */
		$mail_queue->put($from, $recipient, $hdrs, $body,0,false);
		if(PEAR::isError($mail_queue->container->db)){ 
			trigger_error('PEAR:'.ERROR,E_USER_WARNING);
			}
		$success=true;
		}
	else{
		tigger_error('Not configured for email: message not sent.',E_USER_WARNING);
		}
	return $success;
	}


/**
 * Send an SMS
 *
 * @param $recipient  --> The mobile phone number or an array of numbers
 * @param $message --> plain text for the message
 * @return boolean|string Returns "true" if sms was sent OK
 *         or "false" if there was any sort of error.
 */
function send_sms_to($phone,$message){

    global $CFG;

    include_once('lib/'.$CFG->smslib); 

	$result_xml = peticionCURL($phone, $message);

    if($result_xml==-1){
		return false;
		}
	else{
        return true;
		}

	}


/**
 * Takes a date string, probably from the database, and makes its user friendly.
 *
 */
function display_date($date=''){
	if($date!='' and $date!='0000-00-00'){
		list($year,$month,$day)=explode('-',$date);
		$time=mktime(0,0,0,$month,$day,$year);
		$displaydate=date('jS M Y',$time);
		}
	else{
		$displaydate='0000-00-00';
		}
	return $displaydate;
	}

/** 
 *
 * Returns two arrays containing the ratingnames and catdefs for all
 * categories of a particular type (ordered by their rating) and
 * can be optionally restricted by course and section.
 *
 *	@param strng[type]
 *	@param strig[$crid]
 *	@param strig[$secid] 
 *	@return array two arrays: rating names & category definitions
 *
 */
function fetch_categorydefs($type,$crid='%',$secid='%'){
	/*TODO: Needs to add subject specific ones IN FUTURE!*/
	$d_categorydef=mysql_query("SELECT * FROM categorydef  
				WHERE type='$type' AND (section_id LIKE '$secid' OR
				section_id='%') AND (course_id LIKE '$crid' OR
				course_id='%') ORDER BY rating;");
   	$catdefs=array();
	$ratingnames=array();
	/* Usually catdefs of the same selection use the same ratings BUT
	 * it does not have to be the case, the returned array
	 * ratingnames is indexed by the ratingname and each set of
	 * ratings is an array of descriptors indexed by ratingvalue
	 */
	while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
	   	$catdefs[$catdef['id']]=$catdef;
	   	if($catdef['rating_name']!='' 
		   and !array_key_exists($catdef['rating_name'],$ratingnames)){
				$ratingname=$catdef['rating_name'];
				$d_rating=mysql_query("SELECT * FROM rating 
						WHERE name='$ratingname' ORDER BY value;");
				$ratings=array();
				while($rating=mysql_fetch_array($d_rating,MYSQL_ASSOC)){
					$ratings[$rating['value']]=$rating['descriptor'];
					}
				$ratingnames[$ratingname]=$ratings;
				}
	   	}
	return array($ratingnames,$catdefs);
	}

/** 
 * This function calculates the time difference
 * between two moments in a sequence.
 * The function is suitable for processes that take
 * between a few seconds and a few days.
 *
 * Examples: 
 * 40s.
 * 4m-58s.
 * 2d-3h-8s.
 *
 * @param integer[$starttm] 	first moment in sequence. Format: seconds time()
 * @param integer[$endtm]		second moment. Format: seconds time()
 * @return string a string with format: 999...d-99h-99m-99s
 *
 */
function elapsedtime($starttm,$endttm) {
	$time=$endttm-$starttm;
	//$time=11425;
	$fullMinutes=floor($time/60);
	$pseg=$time-$fullMinutes*60;
	$fullHours=floor($fullMinutes/60);
	$pmin=$fullMinutes-$fullHours*60;
	$fullDays=floor($fullHours/24);
	$phours=$fullHours-$fullDays*24;
	
	$rtime='';
	if ($pseg!=0) {
		$rtime=$pseg.'s.';
		}
	if ($pmin!=0) {
		$rtime=$pmin.'m-'.$rtime;
		}
	if ($phours!=0) {
		$rtime=$phours.'h-'.$rtime;
		}
	if ($fullDays!=0) {
		$rtime=$fullDays.'d-'.$rtime;
		}
	return $rtime;
	}

?>
