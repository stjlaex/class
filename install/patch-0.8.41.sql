ALTER TABLE groups
	ADD	type enum('a','p','b') not null default 'a' AFTER name;
UPDATE groups SET type='p' WHERE course_id='' AND subject_id='';
ALTER TABLE section 
	ADD	sequence smallint unsigned not null default '0';
ALTER TABLE section 
	CHANGE name name varchar(240) not null default '';
ALTER TABLE section 
	ADD gid int(10) NOT NULL default '0';
ALTER TABLE orderbudget 
	CHANGE section_id section_id smallint unsigned not null default '0';
