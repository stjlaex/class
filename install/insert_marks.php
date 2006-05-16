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

/*
	Predefines the ratings for comments/concerns 'con' category
*/
mysql_query("INSERT INTO rating VALUES ('simple three','Poor','','-1')");
mysql_query("INSERT INTO rating VALUES ('simple three','Satisfactory','','0')");
mysql_query("INSERT INTO rating VALUES ('simple three','Good','','1')");
mysql_query("INSERT INTO rating VALUES ('con','negative','a cause for concern','-1')");
mysql_query("INSERT INTO rating VALUES ('con','positive','an improvement','1')");
mysql_query("INSERT INTO rating VALUES ('private','confidential','restricted access','-1')");
mysql_query("INSERT INTO rating VALUES ('private','not confidential','shared with staff','1')");

mysql_query("INSERT INTO categorydef VALUES (1,'Academic Performance','con','-1','con','%','%')");
mysql_query("INSERT INTO categorydef VALUES (2,'Completion of Class / Homework','con','-1','con','%','%')");
mysql_query("INSERT INTO categorydef VALUES (3,'Attitude','con','-1','con','%','%')");
mysql_query("INSERT INTO categorydef VALUES (4,'Behaviour','con','-1','con','%','%')");
mysql_query("INSERT INTO categorydef VALUES (5,'Social','con','-1','con','%','%')");
mysql_query("INSERT INTO categorydef VALUES (6,'Organisation','rep','-1','simple three','%','%')");
mysql_query("INSERT INTO categorydef VALUES (7,'Punctuality','rep','-1','simple three','%','%')");
mysql_query("INSERT INTO categorydef VALUES (8,'Quality of Homework','rep','-1','simple three','%','%')");
mysql_query("INSERT INTO categorydef VALUES (9,'Punctuality of Homework','rep','-1','simple three','%','%')");
mysql_query("INSERT INTO categorydef VALUES (10,'Contributing to Class','rep','-1','simple three','%','%')");
mysql_query("INSERT INTO categorydef VALUES (11,'Working to Potential','rep','-1','simple three','%','%')");
mysql_query("INSERT INTO categorydef VALUES ('','Telephone call','bac','-1','private','%','%')");
mysql_query("INSERT INTO categorydef VALUES ('','Email','bac','-1','private','%','%')");
mysql_query("INSERT INTO categorydef VALUES ('','Letter','bac','-1','private','%','%')");
mysql_query("INSERT INTO categorydef VALUES ('','In person','bac','-1','private','%','%')");
mysql_query("INSERT INTO categorydef VALUES ('','Unknown','bac','-1','private','%','%')");
?>
