<?php

// Include XML_Serializer
require_once 'XML/Serializer.php';

//takes the root name as input
function xmlpreparer($rootName,$xmlentry){
	nullCorrect($xmlentry);
	$serializer_options=array(
							  'addDecl' => FALSE,
							  'encoding' => 'ISO-8859-1',
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

?>
