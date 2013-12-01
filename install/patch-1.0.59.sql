CREATE TABLE medical_log (
		 id				int(10) unsigned not null auto_increment,
		 student_id		int(10) not null,
		 category		varchar(10) not null default '',
		 details		text not null default '',
		 user_id		varchar(14) not null default '',
		 time			time not null,
		 date			date not null default '0000-00-00',
		 timestamp		timestamp not null default CURRENT_TIMESTAMP,
		 index			index_sid(student_id),
		 index			index_uid(user_id),
   		 primary key	(id)
) ENGINE=MYISAM;
DROP TABLE IF EXISTS student_event;
CREATE TABLE student_event (
		 id int(10) unsigned not null auto_increment,
		 student_id int(10) not null,
		 event varchar(100) default null,
		 catid varchar(10) not null default '',
		 type varchar(15) not null default '',
		 file varchar(100) not null default '',
		 status enum('0','1') not null default '0',
		 ip varchar(15) not null default '000.000.000.000',
		 user_id varchar(20) not null default '',
		 timestamp timestamp not null default CURRENT_TIMESTAMP,
		 index index_sid (student_id),
		 index index_uid (user_id),
		 primary key (id)
) ENGINE=MYISAM;
