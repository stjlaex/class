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
 * For compatibility with utf8
 *
 *	@param string[$value] a string to be made lowercase
 *	@return string new alfabetic string to lowercase
 *
 */
function good_strtoupper($value){
	$value=mb_strtoupper($value, mb_detect_encoding($value));
	return $value;
	}


/**
 *
 */
function good_str_pad($value,$pad_length,$encoding){
	return str_pad($value,strlen($value)-mb_strlen($value,$encoding)+$pad_length);
	}


/**
 *
 */
function good_pad_item($item,$amount,$len){
	$encoding=mb_detect_encoding($item);

	$al=mb_strlen($amount,$encoding);
	$il=mb_strlen($item,$encoding);

	$text=mb_substr($item,0,$len-$al,$encoding);
	$text=good_str_pad($text,$len-$al,$encoding);
	$text.=$amount;
	return $text;
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
				 chr(0x00CA)=>'E',
				 chr(0x00EA)=>'e',
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
				 chr(0x00C7)=>'C',
				 chr(0x00E7)=>'c',
				 chr(0x00FC)=>'u',
				 chr(0x00DC)=>'U',
				 chr(0x00AA)=>'a',
				 chr(0x00BA)=>'o',
				 chr(0x0060)=>''
				 );
	$encoding=mb_detect_encoding($str);
	if($encoding=='UTF-8'){
        /* First change from multibyte characters to ISO-8859-1 so the next step works. */
		$str=mb_convert_encoding($str,'ISO-8859-1',$encoding);
		/* Now replace the characters with their literal ASCII equivalents defined above. */
		$str=str_replace(
						 array_keys($codes),
						 array_values($codes),
						 $str
						 );
		}

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
 *  Attempts to get rid of any nasties before a mysql insert
 *
 *	@param string[$value]
 *	@return string a clean value
 */
function clean_text_old($value,$in=true){

	//if(get_magic_quotes_gpc()){$value=stripslashes($value);}
	//else{trigger_error('NO MAGIC!!',E_USER_WARNING);}

	/*replaces all MS Word smart quotes, EM dashes and EN dashes*/
	/* TODO: had to remove because they take out chinese characters!!! */
	//$search=array(chr(145),chr(146),chr(147),chr(148),chr(150),chr(151));
	//$replace=array("'","'",'"','"','-','-');
	//$value=str_replace($search,$replace,$value);

	/*blanks possible dodgy sql injection attempt*/
	$search=array('SELECT ','INSERT ','DELETE ','DROP ');
	$replace=array(' ',' ',' ',' ',' ');
	$value=str_replace($search,$replace,$value);

	/*causes problems with xmlreader function*/
	$search=array('<p></p>','&nbsp;','<p> </p>','<p>&nbsp;</p>','&ndash;','<strong>','</strong>','&amp;','<em>','</em>','<pre>','</pre>');
	$replace=array('',' ','','','-','','',' and ','','','','');
	$value=str_replace($search,$replace,$value);

	$value=trim($value);
	//$value=eregi_replace('[^-.?,!;()+:[:digit:][:space:][:alpha:]]','', $value);

	if(!get_magic_quotes_gpc() and $in){$value=mysql_real_escape_string($value);}

	return $value;
 	}



/**
 *  Attempts to get rid of any nasties before a mysql insert
 *
 *	@param string[$value]
 *	@return string a clean value
 */
function clean_text($value,$in=true){


	/*blanks possible dodgy sql injection attempt*/
	$search=array('SELECT ','INSERT ','DELETE ','DROP ');
	$replace=array(' ',' ',' ',' ',' ');
	$value=str_replace($search,$replace,$value);

	$value=trim($value);

	if(!get_magic_quotes_gpc() and $in){$value=mysql_real_escape_string($value);}

	return $value;
 	}

/**
 *  Attempts to get rid of any nasties before a mysql insert
 *
 *	@param string[$value]
 *	@return string a clean value
 */
function clean_html($value){

	global $CFG;

	/* Needs PHP 5.4? or higher ! */
	//if(stream_resolve_include_path('HTMLPurifier.auto.php')!==false){
	if(true){

		require_once('HTMLPurifier.auto.php');

		$HTML_Allowed_Elms=array('caption','h1','h2','h3','h4','h5','h6','li','ol','p','ul','label','div','span');

		$config = HTMLPurifier_Config::createDefault();
		$config->set('Core.Encoding','UTF-8');
		$config->set('Cache.SerializerPath', $CFG->eportfolio_dataroot.'/cache/phpThumb');//set the cache path
		$config->set('HTML.TidyLevel', 'medium');
		$config->set('HTML.AllowedAttributes', array());
		$config->set('HTML.AllowedElements',$HTML_Allowed_Elms);
		//$config->set('HTML.ForbiddenElements', array('br','&amp;'));
		$config->set('CSS.AllowedProperties', array());
		$config->set('Attr.AllowedClasses', array());
		$config->set('AutoFormat.RemoveSpansWithoutAttributes',false);
		$config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
		$config->set('AutoFormat.RemoveEmpty', true);

		$config->set('HTML.DefinitionID','html-'.$CFG->clientid.'-report');
		$config->set('HTML.DefinitionRev', 1);
		//$config->set('Core.DefinitionCache', null);//disable the cache for testing only
		/* WARNING: For this to work with the cache on the Serializer
		 *  directory must exist and be writable by www-data under
		 *  cache/phpThumb
		 */
		if($def=$config->maybeGetRawHTMLDefinition()){
			$def->addElement('label', 'Block', 'Inline', 'Common', array());
			}
		$purifier= new HTMLPurifier($config);
		$newvalue=$purifier->purify($value);

		}
	else{

		$search=array('<p></p>','&nbsp;','<p> </p>','<p>&nbsp;</p>','&ndash;','<strong>','</strong>','&amp;','<em>','</em>','<pre>','</pre>');
		$replace=array('',' ','','','-','','',' and ','','','','');
		$newvalue=str_replace($search,$replace,$value);

		}

	/* TODO: Can't get purifier to remove spans and preserve content! */ 
	$newvalue = preg_replace("/<(\/)?(span)[^>]*>/i","",$newvalue);
 

	return $newvalue;
 	}



/**
 *
 * Does some simple data validation for imported data values
 *
 *	@param string[$value] input value to be evaluated
 *	@param string[$format]
 *	@param string[$field_name]
 *	@return string checked value
 *
 */
function checkEntry($value,$format='',$field_name='',$date_format='european'){

	$value=trim($value);
	$field_type=explode('(', $format);

	if(strpos($field_name,'email')!==false){$value=good_strtolower($value);}

	if($field_name=='form_id' or $field_name=='candidaten1' or $field_name=='candidaten2'){
		$value=strtoupper($value);
		}

	if($field_type[0]=='date'){
		/* Need to convert to YYYY-MM-DD */
		$date_separator='';
		if(strpos($value,'-')!==false){
			$date_separator='-';
			}
		elseif(strpos($value,'/')!==false){
			$date_separator='/';
			}
		elseif(strpos($value,'.')!==false){
			$date_separator='.';
			}

		if($date_separator!=''){
			$date=explode($date_separator,$value);
			if($date_format=='european'){
				/*date order 0day 1month 2year*/
				$value=$date[2].'-'.$date[1].'-'.$date[0];
				}
			elseif($date_format=='us'){
				/*date order 0month 1day 2year*/
				$value=$date[2].'-'.$date[0].'-'.$date[1];
				}
			else{
				/*date order 0year 1month 2day*/
				$value=$date[0].'-'.$date[1].'-'.$date[2];
				}
			}
		else{
			$value='';
			}
		
		}
	elseif($field_type[0]=='enum'){
		$value=strtoupper($value);
		if(checkEnum($value,$field_name)==''){
			$value=getEnumValue($value,$field_name);
			}
		}
	elseif($field_type[0]=='time'){
		if(preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/",$value)){
				}
			else{
				$value='';
				}
		}

	return $value;
	}


/**
 *
 * Validates a value which claims to be compatible with a enum field.
 * Returns the same value if it is compatible with a enum field or
 * blank if it is not.
 *
 *	@param string[$value]
 *	@param string[$field_name]
 *	@return string 
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
 *
 * The enum value being returned is actaully key of the enum array
 * pointing to the string being passed. All enum strings are
 * themselves key to the lang array so are lowercase and no spaces and
 * in English, which may not be much like the user input!
 *
 * Returns an empty string on failure.
 *
 * TODO: look the user string in the lang array to get the enum string
 * first
 *
 *	@param string[$string]
 *	@param string[$field_name]
 *
 *	@return string 
 *
 */
function getEnumValue($string,$field_name){


	$string=good_strtolower($string);
	$string=str_replace(' ','',$string);

	$enumarray=getEnumArray($field_name);
	$enumvalue='';

	while($enumstring=current($enumarray)){
		if($string==$enumstring){
			$enumvalue=key($enumarray);
			//trigger_error($field_name.':  '.$string.' = '.$enumvalue,E_USER_WARNING);
			}
		next($enumarray);
		}

	return $enumvalue;
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
				   '7' => 'boardersevennights'
				   );
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
	$paperstyle=array('portrait' => 'portrait', 
				  'landscape' => 'landscape');
	$reledu=array('A' => 'attendsreligiouseducation', 
				  'W' => 'withdrawnfromreligiouseducation');
	$relwo=array('A' => 'attendscollectivewoship', 
				 'W' => 'withdrawnfromcollectiveworthship');
	$parttime=array('N' => 'no', 'Y' => 'yes');
	$staffchild=array('N' => 'no', 'Y' => 'yes');
	$siblings=array('N' => 'no', 'Y' => 'yes');
	$sen=array('N' => 'no', 'Y' => 'yes');
	$closed=array('N' => 'open', 'Y' => 'closed');
	$medical=array('N' => 'no', 'Y' => 'yes');
	$incare=array('N' => 'no', 'Y' => 'yes');
	$private=array('N' => 'no', 'Y' => 'yes');
	$privateaddress=array('N' => 'no', 'Y' => 'yes');
	$privatephone=array('N' => 'no', 'Y' => 'yes');
	$roomcategory=array('' => '', 'GL' => 'groupleader');
	$building=array('' => '');
	$bed=array('' => '');

	/*for the travelevent table*/
	$type=array('NOT' => 'informationnotobtained', 
				'A' => 'arrival', 
				'D' => 'departure'
				);
	/*for the orderbudget table*/
	$currency=array('0' => 'EUR', 
					'1' => 'GBP'
					);
	$credit=array('0' => 'debit', 
				  '1' => 'credit' 
				  );
	$success=array('0' => '', 
				   '1' => 'achieved' ,
				   '2' => 'notachieved' 
				   );
	$budgetyearcode=array('2007' => '07', 
						  '2008' => '08', 
						  '2009' => '09', 
						  '2010' => '10', 
						  '2011' => '11', 
						  '2012' => '12', 
						  '2013' => '13', 
						  '2014' => '14', 
						  '2015' => '15',
						  '2016' => '16' 
						  );
	$inactive=array('0' => 'no', 
					'1' => 'yes'
					);

	/* For the fees tables. */
	$paymenttype=array('1' => 'bank', 
					   '2' => 'cash',
					   '3' => 'other',
					   '4' => 'specialpayment1',
					   '5' => 'specialpayment2',
					   '6' => 'specialpayment3'
					   );
	$payment=array('0' => 'due', 
				   '1' => 'paid',
				   '2' => 'notpaid'
				   );

	/*codes from CBDS 2007, including deprecated six for compatibility*/
	/*not always the same as ISO 639-2 is the alpha-3 code for language!*/
	$language=array('ENG'=>'english', 
					'ENB'=>'believedtobeenglish', 
					'OTB'=>'believedtobeotherthanenglish',
					'BUL'=>'bulgarian',
					'CZE'=>'czech',
					'DAN'=>'danish',
					'DUT'=>'dutch',
					'EST'=>'estonian',
					'FIN'=>'finnish',
					'FRN'=>'french',
					'GER'=>'german',
					'GRE'=>'greek',
					'ITA'=>'italian',
					'LIT'=>'lithuanian',
					'NOR'=>'norwegian',
					'POL'=>'polish',
					'POR'=>'portuguese',
					'RMNR'=>'romanian',
					'RUS'=>'russian',
					'SLO'=>'slovak',
					'SLV'=>'slovenian',
					'SPA'=>'spanish',
					'SWE'=>'swedish',
					'TUR'=>'turkish',
					'CHI'=>'chinese',
					'OTL'=>'otherlanguage',
					'NOT'=>'informationnotobtained',
					'ACL'=>'acholi',
					'ADA'=>'adangme',
					'AFA'=>'afar-Saho',
					'AFK'=>'afrikaans',
					'AKA'=>'akantwifante',
					'AKAF'=>'akanfante',
					'AKAT'=>'akantwiasante',
					'ALB'=>'albanianshqip',
					'ALU'=>'alur',
					'AMR'=>'amharic',
					'ARA'=>'arabic',
					'ARAA'=>'arabicother',
					'ARAG'=>'arabicalgeria',
					'ARAI'=>'arabiciraq',
					'ARAM'=>'arabicmorocco',
					'ARAS'=>'arabicsudan',
					'ARAY'=>'arabicyemen',
					'ARM'=>'armenian',
					'ASM'=>'assamese',
					'ASR'=>'assyrianaramaic',
					'AYB'=>'anyibaule',
					'AYM'=>'aymara',
					'AZE'=>'azeri',
					'BAI'=>'bamileke',
					'BAL'=>'balochi',
					'BEJ'=>'bejabedawi',
					'BEL'=>'belarusian',
					'BEM'=>'bemba',
					'BHO'=>'bhojpuri',
					'BIK'=>'bikol',
					'BLT'=>'baltitibetan',
					'BMA'=>'burmesemyanma',
					'BNG'=>'bengali',
					'BNGA'=>'bengali (Any Other)',
					'BNGC'=>'bengali (Chittagong/Noakhali)',
					'BNGS'=>'bengali (Sylheti)',
					'BSL'=>'british Sign Language',
					'BSQ'=>'basque',
					'CAM'=>'cambodiankhmer',
					'CAT'=>'catalan',
					'CCE'=>'caribbean Creole English',
					'CCF'=>'caribbean Creole French',
					'CGA'=>'chaga',
					'CGR'=>'chattisgarhikhatahi',
					'CHE'=>'chechen',
					'CHIA'=>'chineseother',
					'CHIC'=>'chinesecantonese',
					'CHIH'=>'chinesehokkienfujianese',
					'CHIK'=>'chinesehakka',
					'CHIM'=>'chinesemandarinputonghua',
					'CKW'=>'chokwe',
					'CRN'=>'cornish',
					'CTR'=>'chitralikhowar',
					'CWA'=>'chichewanyanja',
					'CYM'=>'welsh',
					'DGA'=>'dagaare',
					'DGB'=>'dagbane',
					'DIN'=>'dinkajieng',
					'DZO'=>'dzongkhabhutanese',
					'EBI'=>'ebira',
					'EDO'=>'edobini',
					'EFI'=>'efik-Ibibio',
					'ESA'=>'esanishan',
					'EWE'=>'ewe',
					'EWO'=>'ewondo',
					'FAN'=>'fang',
					'FIJ'=>'fijian',
					'FON'=>'fon',
					'FUL'=>'fulafulfuldepulaar',
					'GAA'=>'ga',
					'GAE'=>'gaelicirish',
					'GAL'=>'gaelicscotland',
					'GEO'=>'georgian',
					'GGO'=>'gogochigogo',
					'GKY'=>'kikuyu/Gikuyu',
					'GLG'=>'galician/Galego',
					'GREA'=>'greek (Any Other)',
					'GREC'=>'greek (Cyprus)',
					'GRN'=>'guarani',
					'GUJ'=>'gujarati',
					'GUN'=>'gurennefrafra',
					'GUR'=>'gurma',
					'HAU'=>'hausa',
					'HDK'=>'hindko',
					'HEB'=>'hebrew',
					'HER'=>'herero',
					'HGR'=>'hungarian',
					'HIN'=>'hindi',
					'IBA'=>'iban',
					'IDM'=>'idoma',
					'IGA'=>'igala',
					'IGB'=>'igbo',
					'IJO'=>'ijo',
					'ILO'=>'ilokano',
					'ISK'=>'itsekiri',
					'ISL'=>'icelandic',
					'ITAA'=>'italian (Any Other)',
					'ITAN'=>'italian (Napoletan)',
					'ITAS'=>'italian (Sicilian)',
					'JAV'=>'javanese',
					'JIN'=>'jinghpawkachin',
					'JPN'=>'japanese',
					'KAM'=>'kikamba',
					'KAN'=>'kannada',
					'KAR'=>'karen',
					'KAS'=>'kashmiri',
					'KAU'=>'kanuri',
					'KAZ'=>'kazakh',
					'KCH'=>'katchi',
					'KGZ'=>'kirghiz',
					'KHA'=>'khasi',
					'KHY'=>'Kihayaluziba',
					'KIN'=>'kinyarwanda',
					'KIR'=>'kirundi',
					'KIS'=>'kisi',
					'KLN'=>'kalenjin',
					'KMB'=>'kimbundu',
					'KME'=>'kimeru',
					'KNK'=>'konkani',
					'KNY'=>'kinyakyusa',
					'KON'=>'kikongo',
					'KOR'=>'korean',
					'KPE'=>'kpelle',
					'KRI'=>'krio',
					'KRU'=>'kru',
					'KSI'=>'kisii',
					'KSU'=>'kisukuma',
					'KUR'=>'kurdish',
					'KURA'=>'kurdishother',
					'KURM'=>'kurdish (Kurmanji)',
					'KURS'=>'kurdish (Sorani)',
					'LAO'=>'lao',
					'LBA'=>'luba',
					'LBAC'=>'luba (Chiluba/Tshiluba)',
					'LBAK'=>'luba (Kiluba)',
					'LGA'=>'luganda',
					'LGB'=>'lugbara',
					'LGS'=>'lugisulumasaba',
					'LIN'=>'lingala',
					'LNG'=>'lango (Uganda)',
					'LOZ'=>'lozisilozi',
					'LSO'=>'lusoga',
					'LTV'=>'latvian',
					'LTZ'=>'luxemburgish',
					'LUE'=>'luvale/Luena',
					'LUN'=>'lunda',
					'LUO'=>'luo (Kenya/Tanzania)',
					'LUY'=>'luhya',
					'MAG'=>'magahi',
					'MAI'=>'maithili',
					'MAK'=>'makua',
					'MAN'=>'manding/Malinke',
					'MANA'=>'manding/Malinke (Any Other)',
					'MANB'=>'bambara',
					'MANJ'=>'dyulajula',
					'MAO'=>'maori',
					'MAR'=>'marathi',
					'MAS'=>'maasai',
					'MDV'=>'maldiviandhivehi',
					'MEN'=>'mende',
					'MKD'=>'macedonian',
					'MLG'=>'malagasy',
					'MLM'=>'malayalam',
					'MLT'=>'maltese',
					'MLY'=>'malayindonesian',
					'MLYA'=>'malay',
					'MLYI'=>'indonesian/Bahasa Indonesia',
					'MNA'=>'magindanao-Maranao',
					'MNG'=>'mongolian',
					'MNX'=>'manxgaelic',
					'MOR'=>'mooremossi',
					'MSC'=>'mauritian/Seychelles Creoley',
					'MUN'=>'munda (Any)',
					'MYA'=>'maya (Any)',
					'NAH'=>'nahuatl/Mexicano',
					'NAM'=>'nama/Damara',
					'NBN'=>'nubian (Any)',
					'NDB'=>'ndebele',
					'NDBS'=>'ndebele (South Africa)',
					'NDBZ'=>'ndebele (Zimbabwe)',
					'NEP'=>'nepali',
					'NUE'=>'nuernaadh',
					'NUP'=>'nupe',
					'NWA'=>'newari',
					'NZM'=>'nzema',
					'OAM'=>'ambo/Oshiwambo',
					'OAMK'=>'ambo (Kwanyama)',
					'OAMN'=>'ambo (Ndonga)',
					'OGN'=>'ogoni',
					'ORI'=>'oriya',
					'ORM'=>'oromo',
					'PAG'=>'pangasinan',
					'PAM'=>'pampangan',
					'PAT'=>'pashto/Pakhto',
					'PHA'=>'pahari/Himachali (India)',
					'PHR'=>'pahari (Pakistan)',
					'PNJ'=>'panjabi',
					'PNJA'=>'panjabi (Any Other)',
					'PNJG'=>'panjabi (Gurmukhi)',
					'PNJM'=>'panjabi (Mirpuri)',
					'PNJP'=>'panjabi (Pothwari)',
					'PORA'=>'portugueseother',
					'PORB'=>'portuguesebrazil',
					'PRS'=>'persianfarsi',
					'PRSA'=>'farsipersianother',
					'PRSD'=>'daripersian',
					'PRST'=>'tajikipersian',
					'QUE'=>'quechua',
					'RAJ'=>'rajasthanimarwari',
					'RME'=>'romanyenglish',
					'RMI'=>'romaniinternational',
					'RMN'=>'romanian',
					'RMNM'=>'romanianmoldova',
					'RMS'=>'romansch',
					'RNY'=>'runyakitara',
					'RNYN'=>'runyankoreruchiga',
					'RNYO'=>'runyororutooro',
					'SAM'=>'samoan',
					'SCB'=>'serbiancroatianbosnian',
					'SCBB'=>'bosnian',
					'SCBC'=>'croatian',
					'SCBS'=>'serbian',
					'SCO'=>'scots',
					'SHL'=>'shillukcholo',
					'SHO'=>'shona',
					'SID'=>'sidamo',
					'SIO'=>'signlanguageother',
					'SND'=>'sindhi',
					'SNG'=>'sango',
					'SNH'=>'sinhala',
					'SOM'=>'somali',
					'SRD'=>'sardinian',
					'SRK'=>'siraiki',
					'SSO'=>'sothosesotho',
					'SSOO'=>'sothosesothosouthern',
					'SSOT'=>'sothosesothonorthern',
					'SSW'=>'swazisiswati',
					'STS'=>'tswanasetswana',
					'SUN'=>'sundanese',
					'SWA'=>'swahilikiswahili',
					'SWAA'=>'swahiliother',
					'SWAC'=>'comorianswahili',
					'SWAK'=>'swahilikingwana',
					'SWAM'=>'swahilibravamwiini',
					'SWAT'=>'swahilibajunitikuu',
					'TAM'=>'tamil',
					'TEL'=>'telugu',
					'TEM'=>'temne',
					'TES'=>'tesoateso',
					'TGE'=>'tigre',
					'TGL'=>'tagalogfilipino',
					'TGLF'=>'filipino',
					'TGLG'=>'tagalog',
					'TGR'=>'tigrinya',
					'THA'=>'thai',
					'TIB'=>'tibetan',
					'TIV'=>'tiv',
					'TMZ'=>'berbertamazight',
					'TMZA'=>'berbertamazightother',
					'TMZK'=>'berbertamazightkabyle',
					'TMZT'=>'berbertamashek',
					'TNG'=>'tongachitonga',
					'TON'=>'tonganoceania',
					'TPI'=>'tokpisin',
					'TRI'=>'travellerirish',
					'TSO'=>'tsonga',
					'TUK'=>'turkmen',
					'TUL'=>'tulu',
					'TUM'=>'tumbuka',
					'UKR'=>'ukrainian',
					'UMB'=>'umbundu',
					'URD'=>'urdu',
					'URH'=>'urhoboisoko',
					'UYG'=>'uyghur',
					'UZB'=>'uzbek',
					'VEN'=>'venda',
					'VIE'=>'vietnamese',
					'VSY'=>'visayanbisaya',
					'VSYA'=>'visayanbisayaother',
					'VSYH'=>'hiligaynon',
					'VSYS'=>'cebuanosugbuanon',
					'VSYW'=>'waraybinisaya',
					'WAP'=>'waparaok',
					'WCP'=>'westafricancreoleportuguese',
					'WOL'=>'wolof',
					'WPE'=>'westafricanpidginenglish',
					'XHO'=>'xhosa',
					'YAO'=>'yao',
					'YDI'=>'yiddish',
					'YOR'=>'yoruba',
					'ZND'=>'zande',
					'ZUL'=>'zulu',
					'ZZZ'=>'classificationpending'
					);

	$language2=$language;
	$language3=$language;
	$languagetype=array('F' => 'firstlanguage', 
						'M' => 'multiplefirstlanguage',
						'H' => 'home',
						'T' => 'tuition',
						'S' => 'secondlanguage',
						'C' => 'correspondence'
						);
	$languagetype2=$languagetype;
	$languagetype3=$languagetype;
	$ethnicity=array(''=>''
					 );
	$enrolstatus=array('EN' => 'enquired', 
					   'AP' => 'applied', 
					   'AT' => 'awaitingtesting', 
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
	$appmethod=array('W' => 'website',
					'E' => 'email',
					'IP' => 'inperson',
					'T' => 'transfer',
					'P' => 'phone'
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
				 '7'=>'ms',
				 '8'=>'major'
				 );
	$relationship=array('NOT'=>'informationnotobtained', 
						'CAR'=>'carer', 
						'DOC'=>'doctor', 
						'FAM'=>'otherfamilymember', 
						'PAM'=>'mother', 
						'PAF'=>'father', 
						'OTH'=>'othercontact', 
						'STP'=>'stepparent', 
						'GRM'=>'grandmother', 
						'GRF'=>'grandfather', 
						'REL'=>'otherrelative', 
						'SWR'=>'socialworker', 
						'RLG'=>'religiouscontact', 
						'FAF'=>'familyfriend', 
						'AGN'=>'agent', 
						'HFA'=>'hostfamily',
						'TUT'=>'personaltutor'
						);
	$responsibility=array('N'=>'noparentalresponsibility', 
						  'Y'=>'parentalresponsibility'
						  );

	/*for the phone table*/
	$phonetype=array('H'=>'homephone', 'W'=>'workphone', 
					 'M'=>'mobilephone', 'F'=>'faxnumber', 'O'=>'otherphone', 'N'=>'carer');

	/*for the gidaid table*/
	$addresstype=array('H'=>'home', 'W'=>'work', 
					   'V'=>'holiday', 'O'=>'other');

	/*for the report and assessment tables*/
	$subjectstatus=array('N'=>'non-validating', 
						 'V'=>'validating', 
						 'O'=>'othervalidating', 
						 'AV'=>'allvalidating', 
						 'A'=>'all');
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
	$dayofweek=array('1'=>'monday' 
					 ,'2'=>'tuesday'
					 ,'3'=>'wednesday' 
					 ,'4'=>'thursday'
					 ,'5'=>'friday' 
					 //,'6'=>'saturday'
					 //,'7'=>'sunday'
					 );

	/*for the sen table*/
	$senprovision=array('N'=>'notonregister', 
						'A'=> 'schoolaction',
						'P'=> 'schoolactionplus', 
						'S'=> 'statemented');
	$senranking=array('1'=>'level 1', '2'=>'level 2', '3'=>'level 3');
	$sentypeinternal=array('NO'=>'nosenissue', 
						   'LD'=>'literacydifficulty', 
						   'ND'=>'numeracydifficulty', 
						   'MD'=>'memory', 
						   'AD'=>'attention', 
						   'EBD'=>'emotionalandbehaviouraldifficulty', 
						   'SCD'=>'speechorcommunicationdifficulty', 
						   'VI'=>'visualimpairment', 
						   'HI'=>'hearingimpairment', 
						   'PD'=>'physicaldisability', 
						   'GT'=>'giftedandtalented',
						   'GNC'=>'generalconcern',
						   'ENC'=>'enrolmentconcern',
						   'EAR'=>'externalassessmentrecommended',
						   'EAL'=>'eal'
						   );
	$sentype=array('EAN'=>'noexternalassessement', 
				   'DYL'=>'dyslexia', 
				   'DYG'=>'dysgraphia', 
				   'DYC'=>'dyscalculia', 
				   'DYP'=>'dyspraxia',
				   'ADHD'=>'adhd', 
				   'AUT'=>'autism', 
				   'WM'=>'workingmemory', 
				   'EBD'=>'emotionalandbehaviouraldifficulty', 
				   'SCD'=>'speechorcommunicationdifficulty', 
				   'VI'=>'visualimpairment', 
				   'HI'=>'hearingimpairment', 
				   'PD'=>'physicaldisability', 
				   'GT'=>'giftedandtalented',
				   'GNC'=>'generalconcern',
				   'EAL'=>'eal',
				   'OTH'=>'otherdifficulty/disability'
				   );
	$senassessment=array('I'=>'internal',
						 'E'=>'external');
	/*	$sentype=array('SPLD'=>'specificlearningdifficulty(dyslexia)', 
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
				   'ATD'=>'attentiondifficulties',
				   'GNC'=>'generalconcern',
				   'EAL'=>'eal'
				   );
	*/
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
						  //'form'=>'form', 
						  'ACADEMIC'=>'academic', 
						  'HOUSE'=>'house', 
						  'TUTOR'=>'club', 
						  'TRIP'=>'trip', 
						  'REG'=>'registrationgroup', 
						  'TRANSPORT'=>'transport', 
						  'EXTRA'=>'other'
						  );

	/*
	 * For the list_studentfield script, not an enumarray at all! Uses fetchStudent_singlefield function.
	 */
	$studentfield=array(
						''=>'',
						'Surname'=>'surname', 
						'Forename'=>'forename', 
						'Gender'=>'gender', 
						'YearGroup'=>'yeargroup', 
						'TutorGroup'=>'tutorgroup', 
						'RegistrationGroup'=>'formgroup', 
						'RegistrationTutor'=>'formtutor',
						'Course'=>'course', 
						'House'=>'house', 
						'DOB'=>'dateofbirth',
						'Age'=>'age',
						'Nationality'=>'nationality',
						'SecondNationality'=>'secondnationality',
						'Birthplace'=>'placeofbirth',
						'CountryOfOrigin'=>'countryoforigin',
						'Language'=>'firstlanguage',
						'SecondLanguage'=>'secondlanguage',
						'ThirdLanguage'=>'thirdlanguage',
						'EmailAddress'=>'email',
						'EnrolNumber'=>'enrolmentnumber',
						'EnrolmentNotes'=>'enrolmentnotes',
						'EnrolmentStatus'=>'enrolstatus',
						'EnrolmentYearGroup'=>'enrolyeargroup',
						'EnrolmentApplicationDate'=>'applicationdate',
						'EnrolmentApplicationMethod'=>'applicationmethod',
						'EnrolmentPreviousSchool'=>'previousschool',
						'EnrolmentLeavingReason'=>'leavingreason',
						'Siblings'=>'siblings',
						'StaffChild'=>'staffchild',
						'EntryDate'=>'schoolstartdate',
						'LeavingDate'=>'schoolleavingdate',
						'Language'=>'language',
						'MobilePhone'=>'mobilephone',
						'TransportMode'=>'transportmode',
						'PersonalNumber'=>'personalnumber',
						'OtherNumber'=>'othernumber',
						'IdExpiryDate'=>'expirydate',
						'AnotherNumber'=>'anothernumber',
						'CandidateID'=>'candidateid',
						'CandidateNumber'=>'candidatenumber',
						'Postcode'=>'postcode',
						'Transport'=>'transport',
						'Club'=>'club',
						'EPFUsername'=>'epfusername',
						'FirstContact'=>'firstcontact',
						'FirstContactPhone'=>'firstcontactphone',
						'FirstContactMobilePhone'=>'firstcontactmobilephone',
						'FirstContactEmailAddress'=>'firstcontactemailaddress',
						'FirstContactPostalAddress'=>'firstcontactaddress',
						'FirstContactRelationship'=>'firstcontactrelationship',
						'FirstContactProfession'=>'firstcontactprofession',
						'FirstContactCompany'=>'firstcontactcompany',
                        'FirstContactEPFUsername'=>'firstcontactepfu',
                        'FirstContactNote'=>'firstcontactnote',
                        'FirstContactCode'=>'firstcontactcode',
                        'FirstContactPrivate'=>'firstcontactprivate',
                        'FirstContactTitle'=>'firstcontacttitle',
                        'FirstContactAddressTitle'=>'firstcontactaddresstitle',
                        'FirstContactSurname'=>'firstcontactsurname',
                        'FirstContactForename'=>'firstcontactforename',
						'SecondContact'=>'secondcontact',
						'SecondContactPhone'=>'secondcontactphone',
						'SecondContactEmailAddress'=>'secondcontactemailaddress',
						'SecondContactPostalAddress'=>'secondcontactaddress',
						'SecondContactRelationship'=>'secondcontactrelationship',
						'SecondContactProfession'=>'secondcontactprofession',
						'SecondContactCompany'=>'secondcontactcompany',
                        'SecondContactEPFUsername'=>'secondcontactepfu',
                        'SecondContactNote'=>'secondcontactnote',
                        'SecondContactCode'=>'secondcontactcode',
                        'SecondContactTitle'=>'secondcontacttitle',
                        'SecondContactPrivate'=>'secondcontactprivate',
						'SecondContactSurname'=>'secondcontactsurname',
						'SecondContactForename'=>'secondcontactforename',
						'ThirdContact'=>'thirdcontact',
						'ThirdContactPhone'=>'thirdcontactphone',
						'ThirdContactEmailAddress'=>'thirdcontactemailaddress',
						'ThirdContactPostalAddress'=>'thirdcontactaddress',
						'ThirdContactRelationship'=>'thirdcontactrelationship',
						'ThirdContactProfession'=>'thirdcontactprofession',
                        'ThirdContactEPFUsername'=>'thirdcontactepfu',
                        'ThirdContactTitle'=>'thirdcontacttitle',
                        'ThirdContactNote'=>'thirdcontactnote',
                        'ThirdContactCode'=>'thirdcontactcode',
                        'ThirdContactPrivate'=>'thirdcontactprivate'
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
						'US'=>'signedoutafterregisterclosed',
						'X'=>'untimetabledsessions',
						'Y'=>'enforcedclosure',
						'Z'=>'pupilnotonrole',
						'#'=>'schoolclosedtopupils'
						);
	/*for the register*/
	$latecode=array(
					'0' => '',
					'1' => 'latebeforeregisterclosed'
					);
	/* TODO: for the register - lesson attendance
	$contributioncode=array(
							'0' => '',
							'1' => 'excellent',
							'2' => 'good',
							'3' => 'poor',
							'4' => 'disruptive'
							);
	*/

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
					   'ME'=>'montenegro',
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
					   'RS'=>'serbia',
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
					   'ZW'=>'zimbabwe',
					   'XX' => 'informationnotobtained',
					   'ZZ' => 'classificationpending'
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
 * Lists the contents of a directory on the server, can limit by extension.
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
 * with PEAR Mail.
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
function send_email_to($recipient, $from, $subject, $messagetext, $messagehtml='', $attachments='',
					   $reply='', $dbc='', $mailtable=''){

    global $CFG;
	$success=false;
	$subject=substr(stripslashes($subject), 0, 900);

	if(is_array($CFG->emailnoreply)){
		$default_emailnoreply=$CFG->emailnoreply[0];
		}
	else{
		$default_emailnoreply=$CFG->emailnoreply;
		}

	if(empty($recipient)){
		return false;
		}
	if($CFG->emailoff=='yes'){
		tigger_error('Not configured for email: message not sent.',E_USER_WARNING);
		return 'emailstop';
		}
	
	/* PEAR MAIL: under this system, every mail is queued 
	 * for the cronjob to send it at a later stage
	 * 
	 */
	require_once "Mail/Queue.php";
	require_once 'Mail/mime.php';

	$db_options['type']='db';
	if($dbc==''){$db_options['dsn']=db_connect(false);}
	else{$db_options['dsn']=$dbc;}
	
	if($mailtable==''){$db_options['mail_table']='message_event';}
	else{$db_options['mail_table']=$mailtable;}
	
	$mail_options['driver']='smtp';
	$mail_options['host']=$CFG->smtphosts;
	$mail_options['port']=25;
	$mail_options['auth']=true;
	$mail_options['username']=$CFG->smtpuser;
	$mail_options['password']=$CFG->smtppasswd;
	
	$mail_queue=& new Mail_Queue($db_options, $mail_options);

	if(is_string($from) and !empty($from)){
		$from_name=$from.' <'.$default_emailnoreply.'>';
		$replyto=$default_emailnoreply;
		}
	elseif(is_array($from) and !empty($from['email'])){
		$from_name=$from['name'].' <'.$from['email'].'>';
		$replyto=$from['email'];
		}
	else{
		$from_name=$CFG->schoolname.' <'.$default_emailnoreply.'>';
		$replyto=$default_emailnoreply;
		}

	/* Option to overide the default reply-to address*/
	if(is_string($reply) and !empty($reply)){
		$replyto=$reply;
		}
	elseif(is_array($reply) and !empty($reply['email'])){
		$replyto=$reply['email'];
		}


	/* Are we accepting bounces and failure notices? */
	if(!empty($CFG->emailhandlebounces)){
		$return= $CFG->emailhandlebounces;
		}
	else{
		$return='';
		}

	/* The account doing the sending. */
	if(!empty($CFG->smtpsender)){
		$sender=$CFG->smtpsender;
		}
	else{
		$sender=$default_emailnoreply;
		}
	
	/* TODO: make use of the $from['email] and $from_name values 
	 * message header 
	 */
	/* 'Date'    => date("r") must be RFC 2822 foramtted date for email headers */
	$hdrs = array( 'From'    => $from_name,
				   'To'      => $recipient,
				   'Subject' => $subject,
				   'Date'    => date("r"),
				   'Message-Id' => '<'.  microtime(true).$CFG->clientid.'@classforschools.com>',
				   'Sender' => $sender,
				   'Organization' => $CFG->schoolname,
				   'Reply-To' => $replyto,
				   'Return-Path' => $return
				   );
	$mimeparams=array(
					  'text_encoding'=>'7bit',
					  'text_charset'=>'UTF-8',
					  'html_charset'=>'UTF-8',
					  'head_charset'=>'UTF-8'
					  );

	/* we use Mail_mime() to construct a valid mail */
	$mime =& new Mail_mime();
	if($messagehtml!=''){
		$mime->setHTMLBody($messagehtml);
		}
	else{
		$mime->setHTMLBody('<p>'.$messagetext.'</p>');
		}
	$mime->setTXTBody($messagetext);
	
	/* Only PDF allowed as attachments. */
	if(is_array($attachments)){
		foreach($attachments as $attachment){
			if(is_file($attachment['filepath'])){ 
				$mimetype='application/pdf';
				$mime->addAttachment($attachment['filepath'],$mimetype,$attachment['filename']);
				}
			}
		}
	
	/* next sentence has to be written after everything else */
	//$body = $mime->get();
	$body = $mime->get($mimeparams);  
	$hdrs = $mime->headers($hdrs);
	
	/* Put message into queue */ 
	$delete_after_send = true;//keep the size of the queue to a minimum
	$seconds_to_send = 600;//small delay
	$mail_queue->put($replyto, $recipient, $hdrs, $body,$seconds_to_send,$delete_after_send);
	if(PEAR::isError($mail_queue->container->db)){ 
		trigger_error('PEAR: '.ERROR,E_USER_WARNING);
		}
	else{
		$success=true;
		}

	return $success;
	}


/**
 * Send an SMS
 *
 * @param $phone  --> The mobile phone number or an array of numbers
 * @param $message --> plain text for the message
 * @return boolean|string Returns "true" if sms was sent OK
 *         or "false" if there was any sort of error.
 */
function send_sms_to($phone,$message,$recipientid=0){

	global $CFG;

	$todate=date('Y-m-d');
	$type='g';//g=guardian,s=student,u=user

	/*TODO: validate the phone number before sending. */
	if(strpos($phone,'+')!==false){}
	elseif(!empty($CFG->sitephonecode)){
		if(strpos($phone,'0')===0){$phone=substr($phone,1);}
		$phone=$CFG->sitephonecode.$phone;
		}


	mysql_query("INSERT INTO message_text_event SET phonenumber='$phone',
   					textbody='$message', texttype='$type', some_id='$recipientid', date='$todate', success='0';");
	
	return true;
	}


/**
 * Takes a date string, probably from the database, and makes its user friendly.
 *
 */
function display_date($date='',$format='human'){
	if($date!='' and $date!='0000-00-00'){
		list($year,$month,$day)=explode('-',$date);
		$time=mktime(0,0,0,$month,$day,$year);
		if($format=='human'){
			$displaydate=date('jS M Y',$time);
			}
		else{
			$displaydate=date('d/m/Y',$time);
			}
		}
	else{
		//$displaydate='0000-00-00';
		$displaydate='';
		}
	return $displaydate;
	}

/**
 * Takes a number and format it for display to the desired currency.
 * This defaults to currency '0' is 'euros' and '1' is 'pounds'.
 *
 * TODO: format to match the $CFG->sitecountry
 */
function display_money($amount,$currency='0'){
	/*
	$test=setlocale(LC_MONETARY, 'es_ES.utf8');
	$money=money_format('%.2n', $amount);
	trigger_error($test,E_USER_WARNING);
	*/

	$money=number_format($amount,2,',','.');
	//$money.=' '.displayEnum($currency,'currency');

	return $money;
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
				$d_rating=mysql_query("SELECT * FROM rating WHERE name='$ratingname' ORDER BY value;");
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

/**
 *
 */
function check_email_valid($email){
	if(preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$/i', $email)){ 
		$valid=true;
		}
	else{
		$valid=false;
		}
	return $valid;
	}

/** 
 * Checks the CFG setting for studentlist_order and returns matching
 * mysql fields for ordering result set. Allowed CFG options:
 *    surname
 *    forename
 *    preferred
 *
 * @return string a string suitable for mysql ORDER BY clause
 *
 */
function get_studentlist_order(){

	global $CFG;

	$orderby='surname, forename';
	if(isset($CFG->studentlist_order)){
		if($CFG->studentlist_order=='forename'){$orderby='forename, surname';}
		elseif($CFG->studentlist_order=='preferred'){$orderby='preferredforename, surname';}
		}

	return $orderby;
	}

/**
 *
 *
 *	@param
 *	@return date
 */
function check_class_release(){
	global $CFG;
	$upgrade=false;

	$d_c=mysql_query("SELECT comment FROM categorydef WHERE
						name='current installed version' AND type='rel';");
	if(mysql_num_rows($d_c)>0){
		$version=mysql_result($d_c,0);
		}
	else{
		$version=$CFG->version;
		mysql_query("INSERT INTO categorydef SET comment='$version',
						name='current installed version', type='rel';");
		mysql_query("INSERT INTO categorydef SET comment='',
						name='previous installed version', type='rel';");
		}

	if($version!=$CFG->version){
		$dbversion=explode('.',$version);
		$db_major=$dbversion[0];
		$db_minor=$dbversion[1];
		$db_revision=$dbversion[2];

		$relversion=explode('.',$CFG->version);
		$rel_major=$relversion[0];
		$rel_minor=$relversion[1];
		$rel_revision=$relversion[2];

		mysql_query("UPDATE categorydef SET comment='$CFG->version' WHERE
						name='current installed version' AND type='rel';");
		mysql_query("UPDATE categorydef SET comment='$version' WHERE
						name='previous installed version' AND type='rel';");
		$upgrade=true;
		}

	if($upgrade and $db_major==$rel_major){
		$mdiff=$db_minor-$rel_minor;
		if($mdiff==0){$rdiff=$db_revision-$rel_revision+1;$final_revision=$rel_revision;}
		else{$rdiff=$db_revision-98;$final_revision=99;}
		while($mdiff<1){
			$minor=$rel_minor+$mdiff;
			while($rdiff<1){
				$upgrade_message='';
				$revision=$final_revision+$rdiff;
				$fname='patch-'.$rel_major.'.'.$minor.'.'.$revision.'.sql';
				$errorno=execute_sql_file('install/'.$fname);
				if($errorno==0){
					$upgrade_message='UPGRADE SUCCESS: changes applied to db '.$fname;
					}
				elseif($errorno>0){
					$upgrade_message='UPGRADE FAILED: the db could not be upgraded with '.$fname;
					}
				if($upgrade_message!=''){
					$upgrade_message=$CFG->clientid.' '.$upgrade_message;
					trigger_error($upgrade_message,E_USER_WARNING);
					if($CFG->emailoff!='yes'){
						send_email_to('stj@'.$CFG->support,'',$upgrade_message,$upgrade_message,$upgrade_message);
						}
					}
				$rdiff++;
				}
			$mdiff++;
			if($mdiff==0){$rdiff=0-$rel_revision;$final_revision=$rel_revision;}
			else{$rdiff=-100;$final_revision=99;}
			}
		}

	/*$class_update_path=$CFG->installpath."/".$CFG->applicationdirectory."/scripts/school_conf_update.php";
	$argvs="--path=".$CFG->installpath;
	$update_school_php="php $class_update_path $argvs 2>&1";
	$result=exec(escapeshellcmd($update_school_php));*/

	return $upgrade;
	}


/**
 * Loads an sql file from the install directory and executes using mysql_query.
 * Recognises comments indicated on multiple and single lines.
 *
 * @param string[$fname]
 * @return boolean[success]
 */

function execute_sql_file($fname){

	$errorno=-1;

	if(file_exists($fname)){

		$errorno=0;
		$fcontent=file_get_contents($fname);
		$lines=explode("\n",$fcontent);
		$query='';
		foreach($lines as $sql_line){
			if(trim($sql_line)!='' and strpos($sql_line,'--')===false){
				$query.=$sql_line;
				if(preg_match("/(.*);/", $sql_line)){
					$query = substr($query, 0, strlen($query)-1);
					//Executing the parsed string, returns the error code in failure
					mysql_query($query);
					if(mysql_errno()){
						$errorno++;
						trigger_error('UPGRADE ERROR! '.mysql_error(),E_USER_WARNING);
						}
					$query='';
					}
				}
			}

		}

	return $errorno;
	}

/**
 *
 */
function getBrowser(){
    $u_agent=$_SERVER['HTTP_USER_AGENT'];
    $bname='Unknown';
    $platform='Unknown';
    $version= "";

    /* Get the platform */
    if(preg_match('/linux/i', $u_agent)){
        $platform='linux';
		}
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform='mac';
		}
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform='windows';
		}
   
    /* Get the name of the useragent */
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
        $bname='Internet Explorer';
        $ub="MSIE";
		}
    elseif(preg_match('/Firefox/i',$u_agent)){
        $bname='Mozilla Firefox';
        $ub="Firefox";
		}
    elseif(preg_match('/Chrome/i',$u_agent)){
        $bname='Google Chrome';
        $ub="Chrome";
		}
    elseif(preg_match('/Safari/i',$u_agent)){
        $bname='Apple Safari';
        $ub="Safari";
		}
    elseif(preg_match('/Opera/i',$u_agent)){
        $bname='Opera';
        $ub="Opera";
		}
    elseif(preg_match('/Netscape/i',$u_agent)){
        $bname='Netscape';
        $ub="Netscape";
		}
   
    /* Get the correct version number */
    $known=array('Version', $ub, 'other');
    $pattern='#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if(!preg_match_all($pattern, $u_agent, $matches)){
		/* No matching number just continue */
		}
   
    $i=count($matches['browser']);
    if($i!=1){
        /* Have two since we are not using 'other' argument yet
		 * see if version is before or after the name
		 */
        if(strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
			}
        else{
            $version= $matches['version'][1];
			}
		}
    else{
        $version= $matches['version'][0];
		}
   
    /* Check if we have a number */
    if($version==null || $version==""){$version="?";}
	
    return array('userAgent' => $u_agent,
				 'name'      => $bname,
				 'version'   => $version,
				 'platform'  => $platform,
				 'pattern'   => $pattern
				 );
	}

/**
 * php.ini contains settings in shorthand notation - use this to convert to bytes
 */
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

/**
 * Validate an IBAN
 * 
 * @param $iban	IBAN Account
 * 
 * return boolean
 */
function checkIBAN($iban){
	$chars=array(  "A"=>"10","B"=>"11","C"=>"12","D"=>"13","E"=>"14","F"=>"15",
				"G"=>"16","H"=>"17","I"=>"18","J"=>"19","K"=>"20","L"=>"21",
				"M"=>"22","N"=>"23","O"=>"24","P"=>"25","Q"=>"26","R"=>"27",
				"S"=>"28","T"=>"29","U"=>"30","V"=>"31","W"=>"32","X"=>"33",
				"Y"=>"34","Z"=>"35"
			  );
	if(!preg_match("/\A[A-Z]{2}\d{2} ?[A-Z\d]{4}( ?\d{4}){1,} ?\d{1,4}\z/", $iban)) {
		return false;
		}
	$country=substr($iban,0,2);
	$check=substr($iban,2,2);
	$account=substr($iban,4);

	$code=$account.$country."00";
	$ncode=strtr($code,$chars);
	$rest=bcmod($ncode,97);
	$digits=98-$rest;
	if($digits<10){$digits=str_pad($digits,2,"0",STR_PAD_LEFT);}
	$compare=$country.$digits.$account;

	if($compare==$iban){return true;}
	else{return false;}
	}


/**
 * Resize an image bigger than given size or crops it given crop details
 * 
 * @param $image		image file path
 * @param $max_width	maximum width for image size
 * @param $max_height	maximum height for image size
 * @param array($crop)	crop details: x1,x2,y1,y2,w,h
 * 
 * return boolean
 */
function resize_image($image,$max_width=600,$max_height=600,$crop=array()){
	$info=getimagesize($image);
	if(($info[0]!=$max_width and $info[1]!=$max_height and ($info[0]>$max_width or $info['1']>$max_height)) or count($crop)>0){
		switch($info[2]){
			case IMAGETYPE_JPEG:
				$type='image';
				$img=imagecreatefromjpeg($image);
			break;
			case IMAGETYPE_PNG:
				$type='image';
				$img=imagecreatefrompng($image);
			break;
			case IMAGETYPE_GIF:
				$type='image';
				$img=imagecreatefromgif($image);
			break;
			default:
				//unlink($image);
			return;
			}

		if($type=='image'){
			$width=imagesx($img);
			$height=imagesy($img);

			if($height>$width){
				$ratio=$max_height/$height;
				$new_height=$max_height;
				$new_width=$width*$ratio;
				}
			else{
				$ratio=$max_width/$width; 
				$new_width=$max_width;
				$new_height=$height*$ratio; 
				}

			if(count($crop)>0){
				$x1=$crop['x1'];$x2=$crop['x2'];$y1=$crop['y1'];$y2=$crop['y2'];
				$w=$crop['w'];$h=$crop['h'];
				$image_true_color=imagecreatetruecolor($max_width, $max_height);
				imagecopyresampled($image_true_color, $img, 0, 0, $x1, $y1, $max_width, $max_height, $w, $h);
				}
			else{
				$image_true_color=imagecreatetruecolor($new_width, $new_height);
				imagecopyresampled($image_true_color, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				}
			imagejpeg($image_true_color, $image, 100);
			/*TODO: save transparent background for png files (needs filename extension png, not jpeg)
				if($ext=='png'){
					$img=imagecreatetruecolor(100,100);
					imagesavealpha($image_true_color, true);
					$color=imagecolorallocatealpha($image_true_color,0x00,0x00,0x00,127);
					imagefill($image_true_color, 0, 0, $color);
					imagepng($image_true_color, $image);
					}
			*/
			imagedestroy($image_true_color);
			return true;
			}
		}
		else{return false;}
	}

/** Taken from PHP manual and > PHP5? */
function dateDiff($startdate,$enddate){
	$startArry = date_parse($startdate);
	$endArry = date_parse($enddate);
	$start_date = gregoriantojd($startArry["month"], $startArry["day"], $startArry["year"]);
	$end_date = gregoriantojd($endArry["month"], $endArry["day"], $endArry["year"]);
	return round(($end_date - $start_date), 0);
	}
/***/

/**
 * Custom trigger_error
 */
function eror($string){
	if(is_array($string)){$string=implode(',',$string);}
	trigger_error($string,E_USER_WARNING);
	}

function getHTTPType(){
	if(isset($_SERVER['HTTPS'])){
		$http='https';
		}
	else{
		$http='http';
		}
	return $http;
	}

?>
