ALTER TABLE users 
	ADD emailpasswd	char(32) binary NOT NULL default '' AFTER email;
ALTER TABLE info
	CHANGE boarder boarder enum('N','B','H','6','7') not null;
ALTER TABLE info
	CHANGE enrolstatus enrolstatus enum('EN','AP','AT','ATD','ACP','AC','RE','CA','WL','C', 'P', 'G','S','M') not null default 'C';
