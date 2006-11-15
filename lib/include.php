<?php 
/**			   								include.php   
 *
 * Includes all the lilbrary functions common to all books. 
 * Any new files should added here.
 */

$languages=array(
				 'de' => 'Deutsch',
				 'en' => 'English',
				 'es' => 'Espanol',
				 'fr' => 'Francais'
				 );
require_once($CFG->dirroot.'/lib/functions.php');
if($CFG->debug=='on'){require_once($CFG->dirroot.'/lib/logging.php');}
require_once($CFG->dirroot.'/lib/community_functions.php');
require_once($CFG->dirroot.'/lib/fetch_student.php');
require_once($CFG->dirroot.'/lib/fetch_assessment.php');
require_once($CFG->dirroot.'/lib/fetch_report.php');
require_once($CFG->dirroot.'/lib/fetch_attendance.php');
require_once($CFG->dirroot.'/lib/language.php');
require_once($CFG->dirroot.'/lib/html_functions.php');
require_once($CFG->dirroot.'/lib/xmlserializer.php');
require_once($CFG->dirroot.'/lib/statementbank.php');
?>
