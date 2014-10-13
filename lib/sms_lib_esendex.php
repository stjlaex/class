<?php
/**
 *
 * Send SMS texts via the Esendex API
 * Requires the path to the Esendex SDK to be set in school.php as 
 *					$CFG->smslib_lib_path='/home/stj/public_html/esendex-php-sdk'
 */
require_once $CFG->smslib_lib_path.'/src/autoload.php';



/**
 * Wrapper function for calling the Esendex service
 * The name is dumb (even though it does use curl) but a carry-over the the Lleida lib.
 */
function peticionCURL($telnumber, $textbody){
	global $CFG;

	$message = new \Esendex\Model\DispatchMessage(
												  $CFG->schoolname, // Send from display name
												  $telnumber, // Send to any valid phone number
												  $textbody,
												  \Esendex\Model\Message::SmsType
												  );

	$authentication = new \Esendex\Authentication\LoginAuthentication(
																	  $CFG->smslib_account,  // Your Esendex Account Reference
																	  $CFG->smslib_username, // Your login email address
																	  $CFG->smslib_passwd  // Your password
																	  );
	$service = new \Esendex\DispatchService($authentication);
	$result = $service->send($message);

	//trigger_error($result->id(),E_USER_WARNING);
	//trigger_error($result->uri(),E_USER_WARNING);
	}
?>