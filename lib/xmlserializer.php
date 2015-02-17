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
	if($options==''){
		$xml=new SimpleXMLElement("<{$rootname}></{$rootname}>");
		}
	elseif($options!='' and count($options['rootAttributes'])>0){
		$attributes='';
		foreach($options['rootAttributes'] as $name=>$value){
			$attributes.=" $name='$value' ";
			}
		$xml=new SimpleXMLElement("<{$rootname} {$attributes} ></{$rootname}>");
		}

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
					if(preg_match('/x\d/',$key)){
						$key=preg_replace('/x\d*/i','',$key);
						}
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
			/* !!!!!!!! Not a good idea!?
			//$encoding=mb_detect_encoding($value);
			//$value=mb_convert_encoding($value,'UTF-8','WINDOWS-1252');
			//$value=htmlspecialchars($value,ENT_NOQUOTES,'UTF-8');
			*/
			$value=htmlspecialchars($value);
			if(!is_numeric($key)){
				if(preg_match('/x/',$key)){
					$key=preg_replace('/x\d*/i','',$key);
					}
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
 *
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
	libxml_use_internal_errors(true);
	$arguments=array(
					 '/_xml' => $xml
					 //,'/_xsl' => $xsl
					 );
	$parameters=array(
					   );
	$xh=xslt_create();
	$template_filepath='file://'.$CFG->installpath.'/templates/'.$xsl_filename;
	//xslt_set_base($xh,$filebase);This is not needed?
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
		if ($CFG->debug=='dev') {
			$html=xml_errors(libxml_get_errors(),false);
			}
		else{
			$html="<br>".get_string("anerroroccurredcreatingthisreport")."<br>";
			}
		}
	xslt_free($xh);

	return $html;
	}


/**
 *
 */
function xml_errors($errors,$print=true){
	if(!$print){$message="";}
	foreach ($errors as $error){
		if(!$print){$message.=display_xml_error($error, $xml);}
		else{echo display_xml_error($error, $xml);}
		}
	libxml_clear_errors();
	if(!$print){return $message;}
	}


/**
 *
 */
function display_xml_error($error, $xml){
	$return=$xml[$error->line-1]."<br>";
	$return.=str_repeat('-', $error->column)."^".trim($error->message)."<br>";

	switch ($error->level){
		case LIBXML_ERR_WARNING:
			$return.="Warning $error->code: ";
		break;
		case LIBXML_ERR_ERROR:
			$return.="Error $error->code: ";
		break;
		case LIBXML_ERR_FATAL:
			$return.="Fatal Error $error->code: ";
		break;
		}
	$return.=trim($error->message)
			."<br> Line: $error->line"
			."<br> Column: $error->column";
	if($error->file) {
		$return.="<br> File: $error->file";
		}
	$return.="<br>";

	return $return;
	}

/**
 *
 * Uses simplexml to load xml file and converts to a standard xml
 * array. Returns empty array on failure.
 *
 *
 * @param string $xmlfilename
 * @return array
 */
function xmlfilereader($xmlfilename){

	if(file_exists($xmlfilename)){
		//$array=simplexml_load_file($xmlfilename,null,LIBXML_NOCDATA,LIBXML_NSCLEAN);
		$array=simplexml_load_file($xmlfilename,null,LIBXML_NOCDATA);
		$xmlArray=objectToArray($array);
		}
	else{$xmlArray=array();}

	return $xmlArray;
	}


/**
 *
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
		/* Need the div tags because the first level tag is always
		 * dropped by simplexml for some reason. 
		 */
		$string=clean_html($string);
		$tags_to_replace=array("h1","h2","h3","h4","h5","p","ul");
		foreach($tags_to_replace as $tag){
			if(strpos($string,$tag.'>')){
				$$tag=0;
				while(strpos($string,$tag.'>')){
					$string=preg_replace('/'.$tag.'>/i',$tag.'x'.$$tag.'>',$string,2);
					$$tag++;
					}
				}
			}
		$xmlstring='<div>'.$string.'</div>';
		$xml=xmlstringToArray($xmlstring);
		}

	return $xml;
	}


/**
 *
 * Convert a string of xml to a php array using simplexml.
 * 
 * Optionally try and clean up the string for unwanted html elements
 * and attributes (ususally resulting from a cut'n'paste job).
 *
 * @param string $xmlstring
 *
 * @return array
 *
 */
function xmlstringToArray($xmlstring){

	$array=simplexml_load_string($xmlstring);

	if($array===false){
		/**
		 * If simplexml failed then it is probably due to unwanted attributes and tags.
		 * Attempt to tidy user inputted html and try again. 
		 */
		/* Remove unwanted tags */
		$xmlstring = preg_replace("/<(\/)?(font|span|del|a|ins|table|tbody|tr|td|colgroup|col|strong|em|br|pre|dir)[^>]*>/i","",$xmlstring);
		$xmlstring = preg_replace("/<(\/)?(script)[^>]*>*<(\/)?(script)[^>]*>/i","",$xmlstring);

		/* Remove attributes, inline style etc.
		 * Each pass takes one attribute per element, so do three times just to be sure 
		 */
		$xmlstring = preg_replace("/<([^>]*)(height|border|cellspacing|cellpadding|class|lang|style|size|face|width|id|dir|align)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>/i","<\\1>",$xmlstring);
		$xmlstring = preg_replace("/<([^>]*)(height|border|cellspacing|cellpadding|class|lang|style|size|face|width|id|dir|align)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>/i","<\\1>",$xmlstring); 
		$xmlstring = preg_replace("/<([^>]*)(height|border|cellspacing|cellpadding|class|lang|style|size|face|width|id|dir|align)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>/i","<\\1>",$xmlstring); 
		$xmlstring = preg_replace("/<([^>]*)(height|border|cellspacing|cellpadding|class|lang|style|size|face|width|id|dir|align)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>/i","<\\1>",$xmlstring); 
		
		$search=array('<p></p>','<p> </p>','<p>&nbsp;</p>','<p>:::</p>','&nbsp;');
		$replace=array('','','','',' ');
		$xmlstring=str_replace($search,$replace,$xmlstring);

		$array=simplexml_load_string($xmlstring);
		}


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
