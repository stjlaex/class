<?php
/**						schoolarrays.php
 *
 * Used to over-ride defintions in the class libs and allows for
 * customisation to match specific schools' needs which can be easily
 * preserved between upgrades.
 */

/* First, this is for localisation of any of the enum arrays which are */
/* part of lib/functions.php */


$building=array('' => ''
				);


/* Second, this is for localised changes to the xml-arrays, perhaps to remove */
/* unwanted fields or to make them required etc. */

/* Eg. This would remove the Boarder field from use completely.*/
//$Student['Boarder']=array();
$Student['PartTime']=array();
//$Student['TransportMode']=array();
//$Student['MiddleNames']=array();
//$Student['PreferredForename']=array();
$Student['Birthplace']=array();
$Student['CountryofOrigin']=array();
//$Student['Religion']=array();

/*Eg. This would make Gender a non-required form field.*/
//$Student['Gender']['inputtype']='';
?>