<?php
require_once('include.php');
$CFG->books=$books;
$CFG->roles=$roles;
$CFG->version='1.0.1';
//$CFG->version='0.9.42';
$CFG->dirroot=$CFG->installpath.'/'.$CFG->applicationdirectory;
global $CFG;
$session='ClaSS'.$CFG->shortname;
if(isset($CFG->timezone)){putenv('TZ='.$CFG->timezone);}
?>