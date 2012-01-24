<?php
/**
 *  xmlserializer.php
 *
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2011
 *	@version	
 *	@since				
 */


/* Still using the PHP4 xslt functions and need this for compatibility
 * with PHP5. TODO: move to PHP5 xsl functions.
 */
if((PHP_VERSION>='5') and extension_loaded('xsl')){
	require_once('xslt-php4-to-php5.php');
	}
else{
	trigger_error('XSL configuration error', E_USER_WARNING);
	}

/**
 * Aplied to ensure lowercase for all xml tagnames
 *
 * @param array $array
 * @return array
 */        
function caseCorrect($array){
	if(is_array($array)){
		$newarray=array();
		foreach($array as $key => $value){
			$key=mb_strtolower($key);
			$newarray[$key]=caseCorrect($value);
			}
		}
	else{
		$newarray=$array;
		}
	return $newarray;
	}


/**
 * Serializes an xml-compatible array into a string of xml with the
 * root name given - in preparation for using or outputing the
 * xml. All xml tagnames will be in lowercase.
 *
 * @param string $root_element_name
 * @param array $xmlarray
 * @param array $options
 * @return string
 */
function xmlpreparer($root_element_name,$xmlarray,$options=''){
	if($options==''){
		$xmlarray=caseCorrect($xmlarray);
		}
 
	$rootname=strtolower(trim($root_element_name));
	$xml=new SimpleXMLElement("<{$rootname}></{$rootname}>");

	array_to_xml($xmlarray,$xml);

	$xmlstring=$xml->asXML();
	$xmlstring=str_replace('<?xml version="1.0"?>','',$xmlstring);

	return $xmlstring;
	}




/**
 * Works recursively to convert an xml-compatible array to an xml
 * object. The array can be of any depth. If numerically indexed then
 * the passed tagname will be used.
 *
 * Only called from xmlpreparer.
 *
 * @param array $xmlarray
 * @param string $xml
 * @param string $tagname
 *
 * @return object
 *
 */
function array_to_xml($xmlarray, &$xml, $tagname=''){

    foreach($xmlarray as $key => $value){
        if(is_array($value)){
			if(!is_numeric($key)){
				$subkeys=array_keys($value);
				if(array_key_exists(0,$subkeys) and !is_numeric($subkeys[0])){
					$newnode = $xml->addChild($key);
					array_to_xml($value, $newnode, $key);
					}
				else{
					array_to_xml($value, $xml, $key);
					}
				}
            else{
				$subnode=$xml->addChild($tagname);
				array_to_xml($value, $subnode, $tagname);
				}
			}
        else{
			$value=htmlspecialchars($value);
			if(!is_numeric($key)){
				$xml->addChild($key,$value);
				}
			else{
				$xml->addChild($tagname,$value);
				}
			}
		}
	}




/**
 *
 * @param string $rootName
 * @param string $xmlentry
 */
function xmlechoer($rootName,$xmlentry){
	$xml=xmlpreparer($rootName,$xmlentry);
	echo $xml;
	}


/**
 * Combines an $xml string with an xsl file which it reads, writes the
 * html output to a file if (output_filename is set) otherwise just
 * returns the html
 *
 * @global string $CFG
 * @param string $xml
 * @param string $xsl_filename
 * @param string $output_filename
 * @return string
 */
function xmlprocessor($xml,$xsl_filename,$output_filename=NULL){
	global $CFG;

	$arguments=array(
					 '/_xml' => $xml
					 //,'/_xsl' => $xsl
					 );
	$parameters=array(
					   );
	$xh=xslt_create();
	$template_filepath='file://'.$CFG->installpath.'/templates/'.$xsl_filename;
	//xslt_set_base($xh,$filebase);
	if($output_filename!=''){$output_filepath='file://'.$CFG->eportfolio_database.'/cache/reports/'.$output_filename;}
	else{$output_filepath=NULL;}
	$html=xslt_process($xh
					   ,'arg:/_xml'
					   ,$template_filepath
					   ,$output_filepath
					   ,$arguments
					   );
	if(empty($html)){
		trigger_error('XSLT processing error: '. xslt_error($xh), E_USER_WARNING);
		}

	xslt_free($xh);

	return $html;
	}



/**
 * Uses simplexml to load xml file and converts to a standard xml
 * array. Returns empty array on failure.
 *
 *
 * @param string $xmlfilename
 * @return array
 */
function xmlfilereader($xmlfilename){

	if(file_exists($xmlfilename)){
		$array=simplexml_load_file($xmlfilename);
		$xmlArray=objectToArray($array);
		}
	else{$xmlArray=array();}

	return $xmlArray;
	}


/**
 * Unserialize some $xml to a php array.
 *
 * @param string $xml
 * @return string
 */
function xmlreader($string){

	$check=strpos($string,'<');
	if($check===false){
		/* At least make sure this could be xml and if not just return the plain text */
		$xml=$string;
		}
	else{
		/* Need the div tags because the first level tag is always dropped by simplexml for some reason. */
		$string=clean_text($string,false);
		$xmlstring='<div>'.html_entity_decode($string,ENT_QUOTES,"UTF-8").'</div>';
		$xml=xmlstringToArray($xmlstring);
		}

	return $xml;
	}


/**
 * Cean up a string for unwanted html elements and attributes (ususally resulting from a cut'n'paste job). to a php array.
 *
 * @param string $xml
 * @return string
 */
function xmlstringToArray($xmlstring){

	/* Remove unwanted tags */
	$xmlstring = preg_replace("/<(\/)?(font|span|del|ins|table|tbody|tr|td|colgroup|col|strong|em|br|pre)[^>]*>/i","",$xmlstring);

	/* Remove attributes, inline style etc.
	 * Each pass takes one attribute per element, so do three times just to be sure 
	 */
	$xmlstring = preg_replace("/<([^>]*)(class|lang|style|size|face|width|id)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>/i","<\\1>",$xmlstring);
	$xmlstring = preg_replace("/<([^>]*)(class|lang|style|size|face|width|id)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>/i","<\\1>",$xmlstring); 
	$xmlstring = preg_replace("/<([^>]*)(class|lang|style|size|face|width|id)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>/i","<\\1>",$xmlstring); 

	$search=array('<p></p>','<p> </p>','<p>&nbsp;</p>','<p>:::</p>','&nbsp;');
	$replace=array('','','','',' ');
	$xmlstring=str_replace($search,$replace,$xmlstring);


	/*
	 * TODO: is php5-tidy a useful tool? need to test for install before calling as its not standard
	 */
	if($tiny){
		$config=array(
					  'indent' => false,
					  'drop-proprietary-attributes' => true,
					  'drop-empty-paras' => true,
					  'drop-font-tags' => true,
					  'hide-comments' => true,
					  'output-xml' => true,
					  'show-body-only' => true,
					  //'merge-spans' => true,
					  //'enclose-block-text' => true,
					  'word-2000' => true,
					  'bare' => true,
					  'wrap' => 0);	
		$xml=tidy_parse_string($xmlstring, $config, 'utf8');
		tidy_clean_repair($xml);
		}
	else{
		$xml=$xmlstring;
		}

	$array=simplexml_load_string($xml);
	$newArray=objectToArray($array);
	//$newArray=object2array($array);

	return $newArray;
	}


/***** TODO: do we care which method is used? **********/
function object2array($object){ 
	return @json_decode(@json_encode($object),1); 
	}

function objectToArray($object){
	if(!is_object( $object ) && !is_array( $object )){
		return $object;
		}

	if(is_object($object) ){
		$object=get_object_vars($object);
		}
	return array_map('objectToArray', $object );
	}
/**** The abve remain experimental!!!  ******/



/**
 * This overcomes a discrepancy in the way XML_Unserializer chooses
 * to generate the array for fields with no value, one value and many
 * values. ClaSS requires they should all be treated as for many
 * values and a numerically indexed array results. Maybe there is an
 * XML_Unserializer option I'm missing that can solve this?
 *
 * @param array $inarray
 * @param string $indexname
 * @return array
 */
function xmlarray_indexed_check($inarray,$indexname){
	$inarray=(array)$inarray;
	if(is_array($inarray[$indexname])){
		$keys=array_keys($inarray[$indexname]);
		//if(!array_key_exists(0,$inarray[$indexname])){
		if(!is_numeric($keys[0])){
			$inarray[$indexname]=array($inarray[$indexname]);
			//$inarray[$indexname][0]='';
			}
		}
	elseif($inarray[$indexname]!=''){
		$inarray[$indexname]=(array)$inarray[$indexname];
		}
	else{
		$inarray[$indexname]=array();
		$inarray[$indexname][0]='';
		}

	return $inarray;
	}
?>