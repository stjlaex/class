ALTER TABLE guardian
	DROP prefix;
ALTER TABLE guardian
	  CHANGE title title enum('0','1','2','3','4','5','6','7','8') not null;
ALTER TABLE guardian
	  CHANGE surname surname varchar(120) not null default '';
ALTER TABLE guardian
	  CHANGE forename forename varchar(120) not null default '';
ALTER TABLE student
	  CHANGE surname surname varchar(120) not null default '';
ALTER TABLE student
	  CHANGE forename forename varchar(120) not null default '';
ALTER TABLE users
	ADD	senrole	enum('0','1') not null AFTER role;
ALTER TABLE info
	ADD enrolnotes text not null default '' AFTER transportmode;
