<?php
require_once('include.php');
$CFG->books=$books;
$CFG->roles=$roles;
$CFG->version='3.0.0';
$CFG->dirroot=$CFG->installpath.'/'.$CFG->applicationdirectory;
global $CFG;
$session='Classis'.$CFG->shortname;
if(isset($CFG->timezone)){putenv('TZ='.$CFG->timezone);}
?>
