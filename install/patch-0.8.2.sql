DROP TABLE attendance;
DROP TABLE event;
CREATE TABLE event (
		id				int unsigned not null auto_increment,
		date			date not null default '0000-00-00',
		period			enum('AM','PM') not null default 'AM',
		unique			indexcom (date,period),
		primary key (id)
) type=myisam;
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
ALTER TABLE guardian
	CHANGE email email varchar(240) not null default '';
