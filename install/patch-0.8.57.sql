ALTER TABLE info
	ADD	staffchild enum('N','Y') not null AFTER transportmode;
ALTER TABLE info
	CHANGE	enrolnotes appnotes text not null default '';
ALTER TABLE info
	ADD	appcategory	varchar(240) not null default ''  AFTER appnotes;
ALTER TABLE info
	ADD	appdate	date null AFTER appcategory;
