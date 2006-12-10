<?php
mysql_query("
CREATE TABLE event (
		id				int unsigned not null auto_increment,
		date			date not null default '0000-00-00',
		period			enum('AM','PM') not null default 'AM',
		unique			indexcom (date,period),
		primary key (id)
) type=myisam;
");

mysql_query("
CREATE TABLE attendance (
		 event_id		int unsigned not null default '0',
		 student_id		int unsigned not null default '0',
		 status			enum('a','p') not null default 'a',
		 code			char(1) not null default '',
		 late			enum('0','1','2','3','4','5','U') not null default '0',
		 comment		text,
	  	 teacher_id		varchar(14) not null default '',	
		 primary key 	(event_id, student_id)
) type=myisam;
");

/**
mysql_query("
CREATE TABLE eventcatid (
	event_id		int unsigned not null default '0',
	categorydef_id	int unsigned not null default '0',
	primary key 	(event_id, categorydef_id)
) type=myisam;");

mysql_query("
CREATE TABLE attendance_history (
	student_id			int unsigned not null default 0, 
	yeargroup_id	smallint not null default '0',
	lea					varchar(3) not null default '',
	estab				varchar(4) not null default 'XXXX',
	possible			smallint unsigned,
	attended			smallint unsigned,
	unauthorised		smallint unsigned,
	late				smallint unsigned,
   	primary key         (student_id, yeargroup_id)
) type=myisam;");
*/
?>
