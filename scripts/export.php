<?php
header("Content-type: text/csv");
header("Content-disposition: attachment; filename=classexport.csv");
readfile('/tmp/class_export.csv');
?>
