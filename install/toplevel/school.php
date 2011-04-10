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
/**
 * The choice of double (AM/PM) or 'single' (AM) attendance
 * registration, assigned per section (the index is the section_id)
 * where 1 is always whole school and those differing from this need
 * to be added. Wither set to single or to the turnover time for the
 * PM session.
 */
$CFG->registrationtype='form';//the default community type (eg. form or reg)
$CFG->registration[1]='single';//Whole school
//$CFG->registration[2]='13:00';//where the index is the section id.
//$CFG->regperiods[1]['AM']=array('1'=>'8:45','2'=>'9:30','3'=>'10:30','4'=>'11:15','5'=>'12:00');
/* Time-zone used by the register - see http://es2.php.net/manual/en/timezones.php*/
$CFG->timezone='Europe/Madrid';
/*defualt sort order sor most student lists*/
$CFG->studentlist_order='surname';
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
 * The start of the month (integer 1 to 12) beyond which students
 * count as new mid-year enrolments as distinct from enrolments who joined in
 * time for the start of the academic year. Probably just the start of
 * term if you don't care.
 */
$CFG->enrol_cutoffmonth='09';
/**
 * Does the school have boarders.
 */
$CFG->enrol_boarders='no';
/*****
 *
 * Optional settings to tune the use of emails for notifying staff
 * values set to either 'yes' or 'no'
 */
/*setting to yes will mean all emailing is prevented*/
$CFG->emailoff='no';
$CFG->emailnoreply='';
$CFG->emailhandlebounces='';
/* Choose either pearmail of phpmail (and for the latter specify path
 * to where you've installed the phpmailer library).
 */
$CFG->emailsys='phpmail';
$CFG->phpmailerpath='/usr/share/php/libphp-phpmailer';//this works for Debian
/*only needed if using an external mail server, something other than local sendmail*/
$CFG->smtphosts='';
$CFG->smtpuser='';
$CFG->smtppasswd='';
/*choose to send email notifications to the responsible staff for a student*/
$CFG->emailincidents='yes';
$CFG->emailguardianincidents='no';
$CFG->emailcomments='no';
$CFG->emailguardiancomments='no';
$CFG->emailmerits='no';
$CFG->emailguardianmerits='no';
/*will exclude guardians of boarders from all emails unless set to 'yes'*/
$CFG->emailboarders='no';
/*send out reminders to relevant staff of approaching deadlines*/
$CFG->emailreminders='no';
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
/*****
 *
 * All of the following are connection details
 * for optional services residing outside of ClaSS
 * Once configured they need to be made accessible to users
 * by uncommenting the relevant entry in include.php
 * A seperate book tab is then added to frame each.
 */
/*optional details of the school's eportfolio site*/
$CFG->eportfoliosite='';
$CFG->eportfoliotabname='Portfolio';
$CFG->eportfolioshare='secret';
$CFG->eportfolio_db='';
$CFG->eportfolio_db_prefix='';
$CFG->eportfolio_access='enrol';
/* The dataroot shared with the eportfolio which is where uploaded files will go.
 * This should be outside your wwwroot. Specified relative to your installpath.
 */
$CFG->eportfolio_dataroot = $CFG->installpath . '../theboxdata';
/*optional details of the school's lms site*/
$CFG->lmssite='';
$CFG->lmstabname='Moodle';
$CFG->lmsshare='secret';
$CFG->lms_db='';
/*optional details of the school's webmail*/
$CFG->webmailsite='';
$CFG->webmailtabname='WebMail';
$CFG->webmailshare='secret';
$CFG->webmail_db='';/*probably not needed!*/
/*optional details of the statement bank for writing report comments*/
$CFG->statementbank_db='';
/*optional details for publishing reports to pdf using html2ps*/
$CFG->html2psscript='';
/*****
 * These are for development sites only - they will dramatically
 * slow performance - should always be set to off.
 */
$CFG->debug='off';
$CFG->classlog='/var/www/classerrors.xml';
$CFG->serverlog='/var/www/myerrors.html';
?>