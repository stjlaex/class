<?php
/**
 *  xmlserializer.php
 *
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2010
 *	@version	
 *	@since				
 */


/*include the PEAR XML stuff*/
require_once 'XML/Serializer.php';
require_once 'XML/Unserializer.php';
if((PHP_VERSION>='5')&&extension_loaded('xsl')){
	require_once('xslt-php4-to-php5.php');
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
		//$array=array_change_key_case($array,CASE_LOWER);
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
 * Takes the root name as input
 *
 * @param string $rootName
 * @param string $xmlentry
 * @param array $options
 * @return string
 */
function xmlpreparer($rootName,$xmlentry,$options=''){
	if($options==''){
		$xmlentry=nullCorrect($xmlentry);
		$xmlentry=caseCorrect($xmlentry);
		$options=array(
					   'addDecl' => FALSE,
					   'encoding' => 'UTF-8',
					   'indent' => '  ',
					   'rootName' => "$rootName",
					   'defaultTagName' => 'undefined',
					   'mode'           => 'simplexml',
					   //	'scalarAsAttributes' => TRUE,
					   //   'attributesArray' => array('field_db', 'label'),
					   //	'contentNAME' => 'value'
					   //	'addDoctype' => true
					   //	'doctype' => array(
					   //	'uri' => 'http://pear.php.net/dtd/package-1.0',
					   //	'id'  => '-//PHP//PEAR/DTD PACKAGE 0.1')
					   );
		}
	$Serializer=new XML_Serializer($options);
	$status=$Serializer->serialize($xmlentry);
	if(PEAR::isError($status)){die($status->getMessage());}
	return $Serializer->getSerializedData();
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
 * Any ouput currently goes to the toplevel directory reports.
 * Still under development!!!!
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
	$filebase='file://'.$CFG->installpath;
	xslt_set_base($xh,$filebase);
	if($output_filename!=''){$output_filename=$filebase.'/reports/'.$output_filename;}
	$html=xslt_process($xh
					   ,'arg:/_xml'
					   ,$filebase.'/templates/'.$xsl_filename 
					   ,$output_filename
					   ,$arguments
					   );
	if(empty($html)){
		trigger_error('XSLT processing error: '. xslt_error($xh), E_USER_WARNING);
		}

	xslt_free($xh);

	return $html;
	}

/**
 * Reads an xml file and xsl file, writes output to a third file
 *
 * @global string $CFG
 * @param string $xml_filename
 * @param string $xsl_filename
 * @return null
 */
function xmlfileprocessor($xml_filename,$xsl_filename){
	global $CFG;

	$xh=xslt_create();
	$filebase='file://'.$CFG->installpath . '/templates/';
	xslt_set_base($xh,$filebase);
	xslt_process($xh
				 ,$xml_filename 
				 ,$xsl_filename 
				 ,'output2.html'
				 ,$arguments
				 );
	trigger_error('XSLT processing error: '. xslt_error($xh), E_USER_WARNING);
	xslt_free($xh);
	return;
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
		//$string=clean_text($string);
		$xmlstring='<div>'.html_entity_decode($string,ENT_QUOTES,"UTF-8").'</div>';
		$xml=xmlstringToArray($xmlstring);
		}
	/* This was the old PEAR library method...
	else{
		$nicexml=clean_text($xmlstring);
		$Unserializer=new XML_Unserializer();
		$status=$Unserializer->unserialize($nicexml);
		if(PEAR::isError($status)){
			//die($status->getMessage());
			//$data=array();
			$data='';
			}
		else{
			$data=$Unserializer->getUnserializedData();
			}
		}
	*/

	return $xml;
	}


function xmlstringToArray($xml){
    $array=simplexml_load_string($xml);
    $newArray=objectToArray($array);
	return $newArray;
	}

function objectToArray($object){
	if(!is_object( $object ) && !is_array( $object )){
		return $object;
		}

	if(is_object($object) ){
		$object = get_object_vars($object);
		}
	return array_map('objectToArray', $object );
	}



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