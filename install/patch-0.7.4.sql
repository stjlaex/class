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
ALTER TABLE categorydef
	ADD section_id smallint unsigned not null default '0' AFTER course_id;
ALTER TABLE yeargroup
	CHANGE section section_id smallint unsigned not null default '0';
CREATE TABLE section (
	id		smallint unsigned not null auto_increment, 
	name 	varchar(30) not null default '', 
	primary key (id)
	);
DROP table teacher;
INSERT subject (id,name) VALUES ('G','General');
INSERT categorydef (name,type,rating,subject_id,course_id) VALUES ('Form tutor','com','0','form','%');
INSERT categorydef (name,type,rating,subject_id,course_id) VALUES ('Year coordinator','com','1','year','%');
INSERT categorydef (name,type,rating,subject_id,course_id) VALUES ('Head of secondary','com','2','section','%');
INSERT categorydef (name,type,rating,subject_id,course_id) VALUES ('Form tutor','sig','0','form','%');
INSERT categorydef (name,type,rating,subject_id,course_id) VALUES ('Year coordinator','sig','1','year','%');
INSERT categorydef (name,type,rating,subject_id,course_id) VALUES ('Head of secondary','sig','2','section','%');
INSERT categorydef (name,type,rating_name,rating,subject_id,course_id) VALUES ('Attendance percentage','att','none','-1','attendance','%');
INSERT categorydef (name,type,rating_name,rating,subject_id,course_id) VALUES ('fails','ent','none','3','fai','%');
INSERT categorydef (name,type,rating_name,rating,subject_id,course_id) VALUES ('activities','ent','none','2','act','%');
INSERT categorydef (name,type,rating_name,rating,subject_id,course_id) VALUES ('prizes','ent','none','1','pri','%');
INSERT categorydef (name,type,rating_name,rating,subject_id,course_id) VALUES ('background','ent','none','2','bac','%');
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
