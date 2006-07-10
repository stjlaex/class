<?php
/*
	Predefines some assessment methods for compatibility with the CBDS
*/
 
mysql_query("INSERT INTO method VALUES ('GF','EG','%','GCSE','','')");
mysql_query("INSERT INTO method VALUES ('GS','EG','%','GCSE','','')");
mysql_query("INSERT INTO method VALUES ('AS','EG','%','A Level Grade','','')");
mysql_query("INSERT INTO method VALUES ('AL','EG','%','A Level Grade','','')");
mysql_query("INSERT INTO method VALUES ('TA','EG','GCSE','GCSE','','')");
mysql_query("INSERT INTO method VALUES ('TA','EG','AS','A Level Grade','','')");
mysql_query("INSERT INTO method VALUES ('TA','EG','A2','A Level Grade','','')");
mysql_query("INSERT INTO method VALUES ('%','NL','%','nat cur level','','')");
mysql_query("INSERT INTO method VALUES ('CE','CL','','1st certificate','','')");
mysql_query("INSERT INTO method VALUES ('%','NV','%','raw score','','')");
mysql_query("INSERT INTO method VALUES ('%','PC','%','test percent','','')");

?>
