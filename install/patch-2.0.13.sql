CREATE TABLE user_attendance (
	id				smallint unsigned auto_increment,
	username			varchar(30) not null default '',
	status			enum('a','p') not null default 'a',
	comment			varchar(100) not null default '',
	date				date not null default '0000-00-00',
	session			enum('','AM','PM') not null default '',
	logtime			timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	primary key		(id)
) ENGINE=MYISAM;
