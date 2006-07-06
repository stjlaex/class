<?php

/*include the PEAR XML stuff*/
require_once 'XML/Serializer.php';
require_once 'XML/Unserializer.php';

/*takes the root name as input*/
function xmlpreparer($rootName,$xmlentry){
	nullCorrect($xmlentry);
	$serializer_options=array(
							  'addDecl' => FALSE,
							  'encoding' => 'UTF-8',
							  'indent' => '  ',
							  'rootName' => "$rootName",
							  'defaultTagName' => 'undefined',
							  'mode'           => 'simplexml'
							  //	'scalarAsAttributes' => TRUE,
							  //   'attributesArray' => array('field_db', 'label'),
							  //	'contentNAME'        => 'value'
							  );

	$Serializer=&new XML_Serializer($serializer_options);
	$status=$Serializer->serialize($xmlentry);
	if(PEAR::isError($status)){die($status->getMessage());}
	echo $Serializer->getSerializedData();
	}

function xmlfilereader($xmlfilename){
	nullCorrect($xmlentry);
	$serializer_options=array(
							  'complexType' => 'array'
							  );

	$Unserializer=&new XML_Unserializer($serializer_options);
	$status=$Unserializer->unserialize($xmlfilename,true);
	if(PEAR::isError($status)){die($status->getMessage());}
	$Data=$Unserializer->getUnserializedData();
	return $Data;
	}
?>