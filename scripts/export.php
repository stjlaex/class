<?php
header("Content-type: text/csv");
header("Content-disposition: attachment; filename=class_export.csv");
readfile('/tmp/class_export.csv');
?>
