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
/*
UK ethnicity codes:
$ethnicity=array('WBRI' => 'whitebritish',
				 'WIRI' => 'whiteirish',
				 'WOTO' => 'whiteother',
				 'MWBC' => 'mixedwhiteandblackcaribbean',
				 'MWBA' => 'mixedwhiteandblackafrican',
				 'MWA' => 'mixedwhiteandasian',
				 'MOTH' => 'anyothermixedbackground',
				 'REFU' => 'preferrednottosay'
				);

US ethnicity codes:
$ethnicity=array('W' => 'whitenonhispanic',
				 'WH' => 'whitehispanic',
				 'AFA' => 'africanamerican',
				 'ASA' => 'asianamerican',
				 'NAA' => 'nativeamerican',
				 'N' => 'pacificislanderalaskannative',
				 'OTH' => 'other',
				 'REFU' => 'preferrednottosay'
				);
*/

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
$Student['Ethnicity']=array();

/*Eg. This would make Gender a non-required form field.*/
//$Student['Gender']['inputtype']='';
?>