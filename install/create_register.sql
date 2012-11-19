CREATE TABLE event (
		id			int unsigned not null auto_increment,
		date		date not null default '0000-00-00',
		session		enum('AM','PM') not null default 'AM',
		period		enum('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14') not null default '0',
		unique		indexeve (date,session,period),
		primary key (id)
) type=myisam;


CREATE TABLE event_notice (
		id			int unsigned not null auto_increment,
		date		date not null default '0000-00-00',
		session		enum('AM','PM') not null default 'AM',
	    comment		text,
		primary key (id)
) type=myisam;


CREATE TABLE event_notidcomid (
		notice_id		int unsigned not null default '0',
		community_id	int unsigned not null default '0',
		yeargroup_id	smallint,
		seen			tinyint(1) unsigned not null default '0',
		primary key (notice_id,community_id,yeargroup_id)
) type=myisam;


CREATE TABLE attendance (
		 event_id		int unsigned not null default '0',
		 student_id		int unsigned not null default '0',
		 status			enum('a','p') not null default 'a',
		 code			char(2) not null default '',
		 late			enum('0','1','2','3','4','5','U') not null default '0',
		 comment		text,
	  	 teacher_id		varchar(14) not null default '',	
		 int 			unsigned not null default 0,
		 logtime		timestamp(14),
		 primary key 	(event_id, student_id)
) type=myisam;


CREATE TABLE attendance_booking (
	id				int unsigned not null auto_increment,
	student_id		int unsigned not null default '0',
	community_id	int unsigned not null default '0',
	day				enum('1','2','3','4','5','6','7','%') not null default '%',
	session			enum('AM','PM') not null default 'AM',
	status			enum('a','p') not null default 'p',
	code			char(2) not null default '',
	startdate		date not null default '0000-00-00',
	enddate			date not null default '0000-00-00',
	comment			text,
	index			indexsidcomid (student_id,community_id),
	primary key		(id)
) type=myisam;
