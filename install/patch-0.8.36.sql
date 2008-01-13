ALTER TABLE users 
	ADD title varchar(20) not null default '' AFTER surname;
ALTER TABLE users 
	ADD epfusername varchar(128) not null default '' AFTER emailpasswd;
ALTER TABLE info 
	ADD epfusername varchar(128) not null default '' AFTER email;
ALTER TABLE assessment 
	ADD profile_name varchar(60) not null default '' AFTER deadline;
INSERT INTO categorydef (name,type,subject_id,course_id) VALUES ('FS Steps','pro','EY','FS');
UPDATE assessment SET profile_name='FS Steps' WHERE course_id='FS' AND grading_name='FS steps';
