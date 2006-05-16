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
require_once($CFG->dirroot.'/lib/fetch_student.php');
require_once($CFG->dirroot.'/lib/fetch_assessment.php');
require_once($CFG->dirroot.'/lib/fetch_report.php');
require_once($CFG->dirroot.'/lib/language.php');
require_once($CFG->dirroot.'/lib/html_functions.php');
require_once($CFG->dirroot.'/lib/xmlserializer.php');
?>
