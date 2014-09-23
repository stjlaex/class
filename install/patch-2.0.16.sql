CREATE TABLE info_extra (
	user_id				int unsigned not null,
	catdef_id				int unsigned not null,
	value				varchar(150) not null default '',
	primary key (user_id,catdef_id)
) ENGINE=MYISAM;
