<?php
$asswd=md5($password);
mysql_query("INSERT INTO users (username, passwd, forename,role,firstbookpref) VALUES ('administrator','$asswd', 'administrator','admin','admin')");
mysql_query("INSERT INTO users (username, passwd, forename, role,firstbookpref) VALUES ('office',
'$asswd', 'office','office','infobook')");	
$office_uid=mysql_insert_id();
?>
