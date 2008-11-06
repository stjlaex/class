<?php
/*
	Requires PHP5, uses included XSL extension (to be enabled).
	To be used in PHP4 scripts using XSLT extension.
	Allows PHP4/XSLT scripts to run on PHP5/XSL

	Typical use:
	{
		if ((PHP_VERSION>='5')&&extension_loaded('xsl'))
			require_once('xslt-php4-to-php5.php');
	}

	Version 0.6, 2008-11-01, http://alexandre.alapetite.net/doc-alex/xslt-php4-php5/

	------------------------------------------------------------------
	Written by Alexandre Alapetite, http://alexandre.alapetite.net/cv/

	Copyright 2004-2008, Licence: Creative Commons "Attribution-ShareAlike 2.0 France" BY-SA (FR),
	http://creativecommons.org/licenses/by-sa/2.0/fr/
	http://alexandre.alapetite.net/divers/apropos/#by-sa
	- Attribution. You must give the original author credit
	- Share Alike. If you alter, transform, or build upon this work,
	  you may distribute the resulting work only under a license identical to this one
	  (Can be included in GPL/LGPL projects)
	- The French law is authoritative
	- Any of these conditions can be waived if you get permission from Alexandre Alapetite
	- Please send to Alexandre Alapetite the modifications you make,
	  in order to improve this file for the benefit of everybody

	If you want to distribute this code, please do it as a link to:
	http://alexandre.alapetite.net/doc-alex/xslt-php4-php5/
*/

$xslArgs=null;	//Warning: only one XSLT object at a time
function xslt_create() {return new xsltprocessor();}
function xslt_errno($xh) {return 7;}
function xslt_error($xh) {return '?';}
function xslt_fetch_result($xslt_handle,$buffer_name=null)
{
	global $xslArgs;
	if (empty($buffer_name)) return $xslArgs['/_result'];
	elseif (substr($buffer_name,0,5)==='arg:/') $buffer_name=substr($buffer_name,5);
	if (isset($xslArgs['/'.$buffer_name])) return $xslArgs['/'.$buffer_name];
	elseif (isset($xslArgs[$buffer_name])) return $xslArgs[$buffer_name];
	else return '';
}
function xslt_free($xh) {unset($xh);}
function xslt_process($xh,$xmlcontainer,$xslcontainer,$resultcontainer=null,$arguments=array(),$parameters=array())
{	//See also: http://alexandre.alapetite.net/doc-alex/domxml-php4-php5/
	//Based on: http://www.php.net/manual/ref.xsl.php#45415
	$xml=new DOMDocument();
	$basedir=$xh->getParameter('sablotron','xslt_base_dir');
	if ($basedir && ($workdir=getcwd())) chdir($basedir);
	if (substr($xmlcontainer,0,5)==='arg:/')
	{
		$arg=substr($xmlcontainer,5);
		if (isset($arguments['/'.$arg])) $xml->loadXML($arguments['/'.$arg]);
		elseif (isset($arguments[$arg])) $xml->loadXML($arguments[$arg]);
	}
	else $xml->load($xmlcontainer);
	$xsl=new DOMDocument();
	if (substr($xslcontainer,0,5)==='arg:/')
	{
		$arg=substr($xslcontainer,5);
		if (isset($arguments['/'.$arg])) $xsl_=&$arguments['/'.$arg];
		elseif (isset($arguments[$arg])) $xsl_=&$arguments[$arg];
		else $xsl_='';
	}
	else $xsl_=file_get_contents($xslcontainer);
	$xsl->loadXML(str_replace('arg:/','arg://',$xsl_));
	$xh->importStyleSheet($xsl);
	global $xslArgs;
	$xslArgs=$arguments;
	if ($parameters!=null)
		foreach ($parameters as $param=>$value)
			$xh->setParameter('',$param,$value);
	$result=$xh->transformToXML($xml);
	if (isset($resultcontainer))
	{
		if (substr($resultcontainer,0,4)==='arg:') $xslArgs[substr($resultcontainer,4)]=$result;
		else file_put_contents($resultcontainer,$result);
	}
	if ($basedir && $workdir) chdir($workdir);
	if (isset($resultcontainer)) return true;
	else return $result;
}
function xslt_run($xh,$xslt_file,$xml_data_file,$result=null,$xslt_params=array(),$xslt_args=array()) {return xslt_process($xh,$xml_data_file,$xslt_file,$result==null?'arg:/_result':$result,$xslt_args,$xslt_params);}
function xslt_set_base($xh,$base) {$xh->setParameter('sablotron','xslt_base_dir',str_replace('file://','',$base));}
function xslt_set_encoding($xh,$encoding) {$xh->setParameter('sablotron','xslt_encoding',$encoding);}	//Does nothing? So manual encoding, or use xsl:output @encoding in XSL document
function xslt_set_error_handler($xh,$handler) {}

class xslt_arg_stream
{
	public $position;
	private $xslArg;
	function stream_eof() {return $this->position>=strlen($this->xslArg);}
	function stream_open($path,$mode,$options,&$opened_path)
	{
		$this->position=0;
		$url=parse_url($path);
		$varname=$url['host'];
		global $xslArgs;
		if (isset($xslArgs['/'.$varname])) $this->xslArg=&$xslArgs['/'.$varname];
		elseif (isset($xslArgs[$varname])) $this->xslArg=&$xslArgs[$varname];
		else return false;
		return true;
	}
	function stream_read($count)
	{
		$ret=substr($this->xslArg,$this->position,$count);
		$this->position+=strlen($ret);
		return $ret;
	}
	function stream_tell() {return $this->position;}
	function url_stat() {return array();}
}

stream_wrapper_register('arg','xslt_arg_stream');
?>
