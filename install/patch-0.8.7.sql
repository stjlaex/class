ALTER TABLE info
	CHANGE email email varchar(240) not null default '';
ALTER TABLE info
	ADD phonenumber varchar(22) not null default '' AFTER email;
