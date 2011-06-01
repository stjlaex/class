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
);
ALTER TABLE users ADD homephone varchar(22) not null default '' AFTER nologin;
ALTER TABLE users ADD mobilephone	varchar(22) not null default '' AFTER homephone;
ALTER TABLE users ADD personalcode	varchar(120) not null default '' AFTER mobilephone;
ALTER TABLE users ADD address_id	int unsigned not null default '0' AFTER personalcode;
ALTER TABLE users ADD dob			date not null default '0000-00-00' AFTER address_id;
ALTER TABLE users ADD contractdate  date not null default '0000-00-00' AFTER dob;