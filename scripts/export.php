<?php
$ftype=$_GET['ftype'];
//trigger_error($ftype,E_USER_WARNING);
if($ftype=='fet'){$mimetype='xml';}
else{$mimetype=$ftype;}
header("Content-type: text/$mimetype");
header("Content-disposition: attachment; filename=class_export.$ftype");
readfile('/tmp/class_export.'.$ftype);
?>
