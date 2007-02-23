ALTER TABLE senhistory DROP sencurriculum;
ALTER TABLE sencurriculum 
	ADD curriculum enum('A','M','D') not null AFTER subject_id;
ALTER TABLE sencurriculum 
	ADD categorydef_id	int unsigned not null default '0' AFTER curriculum;
