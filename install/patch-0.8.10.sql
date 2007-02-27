ALTER TABLE senhistory DROP sencurriculum;
ALTER TABLE sencurriculum 
	ADD curriculum enum('A','M','D') not null AFTER subject_id;
ALTER TABLE sentypes
	CHANGE sentype sentype CHAR(3) not null default '';
ALTER TABLE sencurriculum 
	ADD categorydef_id	int unsigned not null default '0' AFTER curriculum;
UPDATE sencurriculum SET subject_id='G' WHERE subject_id='General';
UPDATE sencurriculum SET subject_id='G' WHERE subject_id='%';
INSERT categorydef (name,type,rating,subtype,course_id) 
	VALUES ('Literacy support','sen','0','','%');
INSERT categorydef (name,type,rating,subtype,course_id) 
	VALUES ('Maths support','sen','1','','%');
INSERT categorydef (name,type,rating,subtype,course_id) 
	VALUES ('Litracy challenge','sen','2','','%');
INSERT categorydef (name,type,rating,subtype,course_id) 
	VALUES ('Maths challenge','sen','3','','%');
INSERT categorydef (name,type,rating,subtype,course_id) 
	VALUES ('Gifted and Talented','sen','4','','%');
