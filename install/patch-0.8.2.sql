DROP TABLE attendance;
DROP TABLE event;
CREATE TABLE event (
		id				int unsigned not null auto_increment,
		date			date not null default '0000-00-00',
		period			enum('AM','PM') not null default 'AM',
		subject_id		varchar(10) not null default '',
		course_id		varchar(10) not null default '',
		primary key (id)
) type=myisam;
CREATE TABLE attendance (
		 event_id		int unsigned not null default '0',
		 student_id		int unsigned not null default '0',
		 entryn			smallint unsigned not null auto_increment,
		 comment		text,
		 code			char(1) not null default '',
	  	 teacher_id		varchar(14) not null default '',	
		 primary key 	(event_id, student_id, entryn)
) type=myisam;
