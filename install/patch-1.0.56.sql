DROP TABLE IF EXISTS medical_log;
CREATE TABLE medical_log (
		 id				int(10) unsigned not null auto_increment,
		 student_id		int(10) not null,
		 category			varchar(10) not null default '',
		 details			text not null default '',
		 user_id			varchar(14) not null default '',
		 time			time not null,
		 date			date not null default '0000-00-00',
		 timestamp		timestamp not null default CURRENT_TIMESTAMP,
		 index			index_sid(student_id),
		 index			index_uid(user_id),
   		 primary key	(id)
) ENGINE=MYISAM;
