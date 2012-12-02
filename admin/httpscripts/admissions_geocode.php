#! /usr/bin/php -q
<?php
/** 
 *                                                       admissions_enquiries.php
 * 
 */
$book='admin';
$current='admissions_geocode.php';

/* The path is passed as a command line argument. */
function arguments($argv){
    $ARGS=array();
    foreach($argv as $arg){
		if(ereg('--([^=]+)=(.*)',$arg,$reg)){
			$ARGS[$reg[1]]=$reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)){
            $ARGS[$reg[1]]='true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);
require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/lib/curl_calls.php');

if($CFG->enrol_geocode_off=='no'){

	$d_a=mysql_query("SELECT id, street, neighbourhood, region, postcode, country
   								FROM address WHERE lat='0' AND lng='0' AND privateaddress='N';");
	$ano=0;
	while($a=mysql_fetch_array($d_a) and $ano<40){

		$addid=$a['id'];

		if($addid>0 and $a['street']!=''){

			$coords=map_geocode::getLatLng($a);
			if($coords===false){
				$lat='999999';
				$lng='999999';
				}
			else{
				$lat=$coords['lat'];
				$lng=$coords['lng'];
				}

			mysql_query("UPDATE address SET lat='$lat', lng='$lng' WHERE id='$addid';");
			//trigger_error($ano.': '.$lat. ' '.$lng,E_USER_WARNING);

			unset($coords);
			$ano++;
			}
		}

	}


/*
 * end options: 
 */ 

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');

exit;

/*
 * end options: 
 */ 



/**
 * Google geocode API call using curl
 * Result returned as a json object with lots of info - only return lat+lng coordiantes though
 * 
 *
 */
class map_geocode{


	static private $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address=";
	
	static public function getLatLng($add){

		/* Try to strip off flat numbers etc. as they seem to confuse google */
		$address=explode(',',$add['street']);
		$address=explode('-',$address[0]);

		/* Less information the better! */
		//if($add['neighbourhood']!=''){$address.=', '.$add['street'];}


		$address=urlencode($address[0]);


		$comp='';
		if($add['country']==''){
			$comp.='country:'. strtoupper($CFG->sitecountry);
			}
		else{
			$comp.='country:'. $add['country'];
			}
		if($add['postcode']!=''){
			$comp.='|postal_code:'.$add['postcode'];
			}
		/* Less information the better!
		if($add['region']!=''){
			$comp.='|administrative_area:'.$region;
			}
		*/

		$url = self::$url. $address.'&'.'components='.$comp;
           
		$resp_json = self::curl_file_get_contents($url);
		$resp = json_decode($resp_json, true);

		if($resp['status']=='OK'){
			return $resp['results'][0]['geometry']['location'];
            }
		else{
			return false;
            }
        }


	static private function curl_file_get_contents($URL){
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $URL);
		$contents = curl_exec($c);
		curl_close($c);
			
		if($contents){
			return $contents;
			}
		else{
			return FALSE;
			}
        }
    }
?>