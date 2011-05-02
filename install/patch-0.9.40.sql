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
