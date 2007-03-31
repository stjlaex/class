ALTER TABLE users 
	ADD emailpasswd	char(32) binary NOT NULL default '' AFTER email;
ALTER TABLE info
	CHANGE boarder boarder enum('N','B','H','6','7') not null;
ALTER TABLE community
	CHANGE details detail varchar(240) not null;
ALTER TABLE info
	CHANGE enrolstatus enrolstatus enum('EN','AP','AT','ATD','ACP','AC','RE','CA','WL','C', 'P', 'G','S','M') not null default 'C';
ALTER TABLE tidcid
	CHANGE teacher_id teacher_id varchar(14) not null default '';
ALTER TABLE form
	CHANGE teacher_id teacher_id varchar(14) not null default '';
UPDATE community SET name='AP' WHERE name='applied';
UPDATE community SET name='AC' WHERE name='accepted';
UPDATE community SET name='EN' WHERE name='enquired';
