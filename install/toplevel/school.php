<?php
/**									school.php 
 *
 * The default settings in this file are intended as illustrative
 * examples and you will need to check each option and edit
 * appropriately for your local installation of ClaSS.

 * This file is not overwritten during an upgrade. If you are upgrading
 * your installation of ClaSS then do check the CHANGELOG for any
 * changes to this file, very occassionally there will be new $CFG
 * options which you will need to take from the distributed version
 * class/install/school.php and add to your local version.
 *
 * NB. The convention is to NOT add trailling slashes to any of the path variables
 *
 */
/*title visible to users along the top of the browser*/
$CFG->sitename='ClaSS Demo';
/*the web-site's real domain name*/
$CFG->siteaddress='classforschools.com';
/*the web-site's url path*/
$CFG->sitepath='/demo-site';
/*flag 'up' or 'down' to prevent any user logins*/
$CFG->sitestatus='up';
/*the path to the top-level site directory*/
$CFG->installpath='/var/www/html/demo-site';
/*almost always just class*/
$CFG->applicationdirectory='class';
/* define system wide properties for a diferent type of school */
$CFG->schooltype='';
/*full name of the school*/
$CFG->schoolname='Demo School Site';
/*filename of the school logo in top-level image directory*/
$CFG->schoollogo='schoollogo.png';
/*the welcome/warning text displayed in the sidebar at login*/
$CFG->loginaside='';
/*abbreviated name of site (single word containing only alphanumeric characters)*/
$CFG->shortname='demo';
/*magic word used to construct passwords - must be changed!*/
$CFG->shortkeyword='guest';
/*details for posting bug reports etc.*/
$CFG->support='laex.org';
$CFG->contact='stj@laex.org';
/*default site language*/
$CFG->sitelang='en';
/*default site country*/
$CFG->sitecountry='gb';
/* International dialing code for this country*/
$CFG->sitephonecode='+44';
/* The dataroot (shared with the eportfolio if one is being used)
 * which is where uploaded files will go.  This should be outside your
 * www root. Must be specified as an absolute path and it must be
 * writable by the Apache user.
 */
$CFG->eportfolio_dataroot = '/home/epfdata';
/**
 * The choice of double (AM/PM) or 'single' (AM) attendance
 * registration, assigned per section (the index is the section_id)
 * where 1 is always whole school and those differing from this need
 * to be added. Wither set to single or to the turnover time for the
 * PM session.
 */
$CFG->regtypes[1]='form';//the default community type (eg. form or reg)
$CFG->registration[1]='single';//Whole school
//$CFG->registration[2]='13:00';//where the index is the section id.
//$CFG->regperiods[1]['AM']=array('1'=>'8:45','2'=>'9:30','3'=>'10:30','4'=>'11:15','5'=>'12:00');
/* Time-zone used by the register - see http://es2.php.net/manual/en/timezones.php*/
$CFG->timezone='Europe/Madrid';
/*defualt sort order for most student lists (either surname, forename or preferred)*/
$CFG->studentlist_order='surname';
/*defualt name order for most student lists (either surname or forename)*/
$CFG->studentname_order='surname';
/* How should a teacher's name be used to sign off? Values of either
 * informal, formal or null
 */
$CFG->teachername='informal';
/**
 * An array of feeder schools whose dbs are to be checked for students
 * transfering here. This affects numbers in the enrolments table and
 * end of year exchange of student records. The feeder_code needs to
 * be defined in the Re-enrolmentStatus scheme for each school. Most
 * sites will leave these blank.
 */
$CFG->feeder_code='TDemo';
$CFG->feeders[0]='';
$CFG->feeders[1]='';
$CFG->feeders[2]='';
/**
 * As part of the enrolment process applicants maybe assessed. Set
 * this to yes for this to be availbale (course related enrolment
 * assessments are always possible this is for a general
 * assessment). Set the second to the enrolassess grade level which
 * will indicate to automaticaly flag the student as SEN; blank to be
 * ignored.
 */
$CFG->enrol_assess='no';
$CFG->enrol_assess_sen='';
/**
 * Are geocoding addresses of contacts for mapping.
 */
$CFG->enrol_geocode_off='yes';
/**
 * Is the enrolment number to be generated automatically by ClaSS
 * (yes) or is it a free value to be entered as any other field (no).
 */
$CFG->enrol_number_generate='no';
/**
 * Custom formula to generate unique enrolment number.
function enrolno_formula($sid){
	$Enrolment=(array)fetchEnrolment($sid);
	$year=$Enrolment['Year']['value'] -1;
	$year=substr($year,2,2);
	$d_i=mysql_query("SELECT MAX(CAST(formerupn AS UNSIGNED)) FROM info WHERE formerupn LIKE '$year%';");
	$maxno=mysql_result($d_i,0);
	if($maxno!=NULL){
		$idno=substr($maxno,2) + 1;
		$enrolno=$year. sprintf("%03s",$idno);// number format 001-999
		}
	else{$enrolno=0;}
	trigger_error('NEW enrolno : '.$enrolno,E_USER_WARNING);
	return $enrolno;
	}
*/
/**
 * Custom formula to generate unique enrolment number.
function parse_enquiry_form($html_form_text){

	$form_fields=array('guardian'=>array(),'student'=>array());


	return $form_fields;	
	}
*/
/**
 * The start of the month (integer 1 to 12) beyond which the current enrolment year ends.
 * Probably just the end of term if you don't care.
 */
$CFG->enrol_cutoffmonth='08';
/**
 * The end of the month (integer 1 to 12) after which the budget year for orders
 * rolls forward.
 */
$CFG->budget_endmonth='05';
/**
 * The minimum balance of a budget below which it will be locked from
 * further orders. Set to 0 to disable budget locking.
 */
$CFG->budget_lock='0';
/**
 * Does the school have boarders.
 */
$CFG->enrol_boarders='no';
/**
 *
 * Optional settings for receiving emails through an imap account
 */
$CFG->email_imap_off='yes';
$CFG->email_imap_host='';
$CFG->email_imap_user='';
$CFG->email_imap_passwd='';
/**
 *
 * Optional settings to tune the use of emails for notifying staff
 * values set to either 'yes' or 'no'
 */
/* Setting to yes will mean emailing is disabled completely. */
$CFG->emailoff='no';
$CFG->email_pastoral_send='no';
$CFG->emailnoreply='';
/* The index for $CFG->emailnoreplyname has to be the email for the name */
$CFG->emailnoreplyname[]='';
$CFG->emailhandlebounces='';
/* Only needed if using an external mail server, something other than local sendmail. */
$CFG->smtphosts='';
$CFG->smtpuser='';
$CFG->smtppasswd='';
/* Choose to send email notifications to the responsible staff for a student. */
$CFG->emailincidents='yes';
$CFG->emailguardianincidents='no';
$CFG->emailcomments='no';
$CFG->emailguardiancomments='no';
$CFG->emailmerits='no';
$CFG->emailguardianmerits='no';
/* Bcc these email addresses with all group messages to guardians. */
$CFG->emailguardianbccs[]='';
/* Will exclude guardians of boarders from all emails unless set to 'yes'. */
$CFG->emailboarders='no';
/* Options for the Register: send out reminders to relevant staff of incomplete registers. */
$CFG->emailregisterreminders='no';
/* The dedicated noreply address for parents replying to absence messages. */
$CFG->emailregisternoreply='';
/**/
$CFG->default_merit_points='';
/**
 */
$CFG->smsoff='yes';
$CFG->smslib='sms_lib.php';
/**
 */
$CFG->feeslib='fees_lib.php';
$CFG->feesdetails['nif']='NIFno';
$CFG->feesdetails['bic']='DEFBICg';
/* student_id or enrolno */
$CFG->fees_mandate_type='student_id';
/**
 *
 * Optional LDAP connection details.
 */
$CFG->ldapdc1='example';
$CFG->ldapdc2='com';
$CFG->ldapserver='';
$CFG->ldapuser='';
$CFG->ldappasswd='';
$CFG->clientid='';
/*optional details of the school's lms site*/
$CFG->lmssite='';
$CFG->lmstabname='Moodle';
$CFG->lmsshare='secret';
$CFG->lms_db='';
/* Optional details of the school's calendar site - currently only
 * work with a public google calendar. 
 */
$CFG->calendarsite='https://www.google.com/calendar';
$CFG->calendartabname='Calendar';
$CFG->calendarsrc='';
/*optional details of the statement bank for writing report comments*/
$CFG->statementbank_db='';
/*optional details for publishing reports to pdf using html2ps*/
$CFG->html2psscript='';
/**
 *
 * API Key for web services
 *
 */
$CFG->api_key='';
$CFG->ppod_api='';
$CFG->schoolbag_api_url='';
$CFG->schoolbag_api_key='';
/**
 *
 * These are for development sites only - they will dramatically
 * slow performance - should always be set to off.
 *
 */
$CFG->debug='off';
$CFG->classlog='/var/www/classerrors.xml';
$CFG->serverlog='/var/www/myerrors.html';

?>
