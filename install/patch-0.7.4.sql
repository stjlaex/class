ALTER TABLE concerns RENAME comments;
ALTER TABLE subject DROP teacher_id;
ALTER TABLE yeargroup DROP teacher_id;
ALTER TABLE report
    ADD stage char(3) not null default ''  AFTER course_id;
ALTER TABLE report
    ADD component_status enum('None','N','V','A') not null default 'None' AFTER stage;
ALTER TABLE users
    ADD language varchar(10) not null default '' AFTER email;
ALTER TABLE student
    ADD middlenamelast enum('N','Y') not null default 'N' AFTER surnamefirst;
ALTER TABLE users
    ADD firstbookpref varchar(20) not null default '' AFTER language;
ALTER TABLE users
    ADD role varchar(20) not null default '' AFTER firstbookpref;
ALTER TABLE users
    ADD worklevel enum('-1','0', '1', '2') not null default '0' AFTER role;
UPDATE users SET firstbookpref='markbook';
UPDATE users SET role='teacher';
UPDATE users SET firstbookpref='infobook' WHERE username='office';
UPDATE users SET role='office' WHERE username='office';
UPDATE users SET firstbookpref='admin' WHERE username='administrator';
UPDATE users SET role='admin' WHERE username='administrator';
UPDATE comments SET subject_id='G' WHERE subject_id='General';
UPDATE comments SET subject_id='G' WHERE subject_id='%';
ALTER TABLE course
	ADD section_id smallint unsigned not null default '0' AFTER many;
ALTER TABLE course
	ADD endmonth enum('','1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL DEFAULT '' AFTER section_id;
ALTER TABLE course
	CHANGE stage sequence smallint unsigned not null default '0';
ALTER TABLE categorydef
	ADD section_id smallint unsigned not null default '0' AFTER course_id;
ALTER TABLE categorydef
	ADD subtype	varchar(20) not null default '' AFTER type;
ALTER TABLE categorydef
	CHANGE name name varchar(60) not null default '';
ALTER TABLE yeargroup
	CHANGE section section_id smallint unsigned not null default '0';
CREATE TABLE section (
	id		smallint unsigned not null auto_increment, 
	name 	varchar(30) not null default '', 
	primary key (id)
	);
DROP table teacher;
DROP table method;
INSERT subject (id,name) VALUES ('G','General');
INSERT categorydef (name,type,rating,subtype,course_id) VALUES ('Form tutor','com','0','form','%');
INSERT categorydef (name,type,rating,subtype,course_id) VALUES ('Year coordinator','com','1','year','%');
INSERT categorydef (name,type,rating,subtype,course_id) VALUES ('Head of secondary','com','2','section','%');
INSERT categorydef (name,type,rating,subtype,course_id) VALUES ('Form tutor','sig','0','form','%');
INSERT categorydef (name,type,rating,subject_id,course_id) VALUES ('Year coordinator','sig','1','year','%');
INSERT categorydef (name,type,rating,subtype,course_id) VALUES ('Head of secondary','sig','2','section','%');
INSERT categorydef (name,type,rating_name,rating,subtype,course_id) VALUES ('Attendance percentage','att','none','-1','attendance','%');
INSERT categorydef (name,type,rating_name,rating,subtype,course_id) VALUES ('fails','ent','none','3','fai','%');
INSERT categorydef (name,type,rating_name,rating,subtype,course_id) VALUES ('activities','ent','none','2','act','%');
INSERT categorydef (name,type,rating_name,rating,subtype,course_id) VALUES ('prizes','ent','none','1','pri','%');
INSERT categorydef (name,type,rating_name,rating,subtype,course_id) VALUES ('background','ent','none','2','bac','%');
INSERT INTO rating VALUES ('fivegrade','Poor','','1');
INSERT INTO rating VALUES ('fivegrade','Satisfactory','','2');
INSERT INTO rating VALUES ('fivegrade','Good','','3');
INSERT INTO rating VALUES ('fivegrade','Very good','','4');
INSERT INTO rating VALUES ('fivegrade','Excellent','','5');
ALTER TABLE tidcid
	ADD component_id varchar(10) not null default '';
ALTER TABLE background
	ADD subject_id varchar(10) not null default '' AFTER category;
ALTER TABLE background
	ADD type varchar(3) not null default 'bac' AFTER student_id;
INSERT INTO background (type,student_id,entrydate,ncyear,category,detail,subject_id,teacher_id) SELECT 'act',student_id,entrydate,ncyear,category,detail,subject_id,teacher_id FROM activities;
INSERT INTO background (type,student_id,entrydate,ncyear,detail,subject_id,teacher_id) SELECT 'fai',student_id,entrydate,ncyear,detail,subject_id,teacher_id FROM fails;
INSERT INTO background (type,student_id,entrydate,ncyear,category,detail,subject_id,teacher_id) SELECT 'pri',student_id,entrydate,ncyear,category,detail,subject_id,teacher_id FROM prizes;
DROP table activities;
DROP table fails;
DROP table prizes;
ALTER TABLE gidsid
	CHANGE relationship relationship enum('NOT','CAR','DOC','FAM','OTH','PAM','PAF','STP','REL','SWR','HFA','AGN') not null;
ALTER TABLE gidsid
	CHANGE priority priority enum('0','1','2','3','4') not null;
ALTER TABLE gidsid
	ADD mailing enum('0','1','2','3','4') not null AFTER priority;
ALTER TABLE class DROP yeargroup_id;
DROP TABLE classes;
CREATE TABLE classes (
		course_id		varchar(10) not null default '',
	    subject_id		varchar(10) not null default '',
		stage			char(3) not null default '',
        generate		enum('', 'forms','sets','none') not null default '',
		naming			varchar(40) not null default '',
		many			smallint unsigned default 4,
		index			index_crid (course_id),
		primary key (course_id, subject_id, stage)
);
DROP TABLE cohort;
CREATE TABLE cohort (
	id				int unsigned not null auto_increment, 
	course_id	   	varchar(10) not null default '',
	stage			char(3) not null default '',
	year			year not null default '0000',
	season			enum('','S','W','M','1','2','3','4','5','6','7','8','9','a','b','c') NOT NULL DEFAULT 'S',
	unique 			indexcohort (course_id,stage,year,season),
	primary key (id)
);
CREATE TABLE community (
	id			smallint unsigned not null auto_increment, 
	name		varchar(30) not null default '', 
    type		enum('','family','form','year','tutor','alumni','enquired','applied','accepted','trip','reg','stop','extra') not null default '',
    details		varchar(240) not null default '',
	unique 		indexcom (type,name),
	primary key  	(id)
);
CREATE TABLE comidsid (
	community_id	int unsigned not null default '0',
	student_id		int unsigned not null default '0',
	joiningdate		date null,
	leavingdate 	date null,
	primary key 	(community_id, student_id)
);
CREATE TABLE cohidcomid (
	cohort_id		int unsigned not null default '0',
	community_id	int unsigned not null default '0',
	primary key 	(cohort_id, community_id)
);
CREATE TABLE transport (
	id				smallint unsigned auto_increment, 
	name			varchar(30) not null default '', 
    details			varchar(240) not null default '',
	capacity		smallint unsigned not null default 0,
	teacher_id		varchar(14) NOT NULL default '',
	primary key  	(id)
);
CREATE TABLE transportstop (
	id				smallint unsigned auto_increment, 
	transport_id	smallint not null default 0, 
	name			varchar(30) not null default '', 
    details			varchar(240) not null default '',
	primary key  	(id)
);
ALTER TABLE assessment 
	ADD grading_name varchar(20) not null default '' AFTER outoftotal;
ALTER TABLE markdef DROP tier;
ALTER TABLE score DROP tier;
ALTER TABLE markdef
	CHANGE scoretype scoretype enum('value','grade','percentage','comment') not null default 'value';
ALTER TABLE form
	CHANGE id id varchar(10) not null default '';
ALTER TABLE form
	ADD name varchar(20) not null default '' AFTER id;
ALTER TABLE yeargroup
	CHANGE ncyear sequence smallint unsigned not null default '0';
ALTER TABLE attendance
	CHANGE ncyear yeargroup_id smallint unsigned not null default '0';
ALTER TABLE background
	CHANGE ncyear yeargroup_id smallint unsigned not null default '0';
ALTER TABLE comments
	CHANGE ncyear yeargroup_id smallint unsigned not null default '0';
ALTER TABLE incidents
	CHANGE ncyear yeargroup_id smallint unsigned not null default '0';
ALTER TABLE info
	CHANGE enrolstatus 	enrolstatus	enum('EN','AP','AC','C', 'P', 'G','S','M') not null default 'C';
