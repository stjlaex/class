<?php 
/**											logging.php
 * Used for debugging only.
 * Turned on by setting debug to 'On' in school.php
 * This is called after login authentication has taken place
 * and so errors before that will be logged as per php.ini settings
 *
 * Example to force a call would be:
 *    trigger_error('my message',E_USER_WARNING);
 */

if($CFG->debug=='on'){
	error_reporting(0);

	function myErrorHandler($errno, $errmsg, $filename, $linenum, $vars){
		global $CFG;
		// in reality the only entries to be
		// considered are E_WARNING, E_NOTICE, E_USER_ERROR,
		// E_USER_WARNING and E_USER_NOTICE
		$errortype=array (
                E_ERROR              => 'Error',
                E_WARNING            => 'Warning',
                E_PARSE              => 'Parsing Error',
                E_NOTICE             => 'Notice',
                E_CORE_ERROR         => 'Core Error',
                E_CORE_WARNING       => 'Core Warning',
                E_COMPILE_ERROR      => 'Compile Error',
                E_COMPILE_WARNING    => 'Compile Warning',
                E_USER_ERROR         => 'User Error',
                E_USER_WARNING       => 'User Warning',
                E_USER_NOTICE        => 'User Notice',
                E_STRICT             => 'Runtime Notice',
                E_RECOVERABLE_ERRROR => 'Catchable Fatal Error'
                );

		$err='<errorentry errortype="'. $errortype[$errno] .'">'."\n";
		$err.="\t<datetime>" . date('Y-m-d H:i:s') . "</datetime>\n";
		$err.="\t<errornum>" . $errno . "</errornum>\n";
		$err.="\t<errortype>" . $errortype[$errno] . "</errortype>\n";
		$err.="\t<errormsg>" . $errmsg . "</errormsg>\n";
		$err.="\t<scriptname>" . $filename . "</scriptname>\n";
		$err.="\t<scriptlinenum>" . $linenum . "</scriptlinenum>\n";
		$err.="\t<classbook>" . $vars['book'] . "</classbook>\n";
		$err.="\t<classcurrent>" . $vars['current'] . "</classcurrent>\n";
		// check if mysql related and if so add the mysql error
		if(eregi('(mysql)', $errmsg)){
			$err.="\t<mysqlerror>\n";
			$err.="\t\t<errno>" . mysql_errno(). "</errno>\n";
			$err.="\t\t<error>" . mysql_error(). "</error>\n";
			$err.="\t</mysqlerror>\n";
   			}
		// set of errors for which a var trace will be saved
		$user_errors=array(E_USER_ERROR, E_USER_WARNING);
		if(in_array($errno, $user_errors)){
			//$err.="\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";
			}
		$err.="</errorentry>\n\n";

		// set of errors which are not to be logged
		// $hide_errors=array(E_NOTICE);
		 $hide_errors=array();
		if(!in_array($errno, $hide_errors)){
			error_log($err,3,$CFG->classlog);
			}
		if($errno==E_USER_ERROR){
			//mail('stj@laex.org', 'Critical User Error', $err);
			}
		}
	$old_error_handler=set_error_handler('myErrorHandler');
	}
?>
