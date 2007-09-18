<?php
/**									school.php 
 *
 * The default settings in this file are intended as illustrative
 *examples and you will need to check each option and edit
 *appropriately for your local installation of ClaSS.

 * This file is not overwritten during an upgrade. If you are upgrading
 *your installation of ClaSS then do check the CHANGELOG for any
 *changes to this file, very occassionally there will be new $CFG
 *options which you will need to take from the distributed version
 *class/install/school.php and add to your local version.
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
/*school-specific abbreviated name of site*/
$CFG->shortname='demo';
/*school-specific magic word - must be changed!*/
$CFG->shortkeyword='guest';
/*details for posting bug reports etc.*/
$CFG->support='laex.org';
$CFG->contact='stj@laex.org';
/*default site language*/
$CFG->sitelang='en';
/*the choice of 'double' (AM/PM) or 'single' (AM) attendance registration*/
$CFG->registration='double';
/*used by the register - see http://es2.php.net/manual/en/timezones.php*/
$CFG->timezone='Europe/Madrid';
/*****
 *Optional settings to tune the use of emails for notifying staff
 *values set to either 'yes' or 'no'
 */
/*setting to yes will mean all emailing to staff is prevented*/
$CFG->emailoff='no';
$CFG->emailnoreply='';
$CFG->emailhandlebounces='';
/*path to where you've installed the phpmailer library*/
$CFG->phpmailerpath='/usr/share/php/libphp-phpmailer';//this works for Debian
/*only needed if using an external mail server, something other than local sendmail*/
$CFG->smtphosts='';
$CFG->smtpuser='';
$CFG->smtppasswd='';
/*choose to send email notifications to the responsible staff for a student*/
$CFG->emailincidents='yes';
$CFG->emailguardianincidents='no';
$CFG->emailcomments='no';
/*send out reminders to relevant staff of approaching deadlines*/
$CFG->emailreminders='no';
/*****
 *All of the following are connection details
 *for optional services residing outside of ClaSS
 *Once configured they need to be made accessible to users
 *by uncommenting the relevant entry in include.php
 *A seperate book tab is then added to frame each.
 */
/*optional details of the school's eportfolio site*/
$CFG->eportfoliosite='';
$CFG->eportfoliotabname='Portfolio';
$CFG->eportfolioshare='secret';
$CFG->eportfolio_db='classelgg';
$CFG->eportfolio_db_prefix='elgg';
/*optional details of the school's lms site*/
$CFG->lmssite='';
$CFG->lmstabname='Moodle';
$CFG->lmsshare='secret';
$CFG->lms_db='classmoodle';
/*optional details of the school's webmail*/
$CFG->webmailsite='http://webmail.demo.org';
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