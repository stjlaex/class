ALTER TABLE guardian 
	ADD note text not null default '' AFTER companyname;
ALTER TABLE guardian 
	ADD code varchar(120) not null default '' AFTER note;
