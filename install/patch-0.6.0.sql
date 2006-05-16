ALTER TABLE perms ADD e SET('0','1') NOT NULL DEFAULT '0';
ALTER TABLE report ADD addcomment ENUM('no','yes') NOT NULL DEFAULT 'yes'; 
ALTER TABLE report ADD addcategory ENUM('no','yes') NOT NULL DEFAULT 'yes';
ALTER TABLE report ADD style varchar(60) NOT NULL DEFAULT '';
ALTER TABLE report ADD transform varchar(60) NOT NULL DEFAULT '';
ALTER TABLE users ADD logtime timestamp(14) ON UPDATE CURRENT_TIMESTAMP NOT NULL default CURRENT_TIMESTAMP;
ALTER TABLE student CHANGE gender gender ENUM( '', 'M', 'F' ) NOT NULL; 
ALTER TABLE eidmid DROP yearofexam;
ALTER TABLE eidmid DROP season; 
ALTER TABLE subject DROP teacher_id; 
ALTER TABLE course DROP teacher_id; 
ALTER TABLE yeargroup DROP teacher_id; 
DROP TABLE subbid;
DROP TABLE eidsid;
CREATE TABLE eidsid (
	id				int unsigned NOT NULL auto_increment,
	assessment_id	int unsigned NOT NULL,
	student_id		int unsigned NOT NULL,
	subject_id		varchar(10) NOT NULL,
	component_id	varchar(10) NOT NULL,
	date			date NOT NULL DEFAULT '0000-00-00',
	resultstatus	enum('I', 'R', 'T', 'P', 'E') NOT NULL,
	result			char(3) NOT NULL DEFAULT '',
	examboard		char(3) NOT NULL DEFAULT '',
	examsyllabus	char(6) NOT NULL DEFAULT '',
	index			index_result(student_id),
	primary key 	(id)
);
CREATE TABLE  history (
  uid			int(10) unsigned,
  page			varchar(40) NOT NULL default '', 
  time			timestamp NULL
);

ALTER TABLE mark ADD component_id varchar(10) NOT NULL DEFAULT '';
ALTER TABLE background ADD category varchar(100) NOT NULL DEFAULT '';
ALTER TABLE background ADD teacher_id varchar(14) NOT NULL DEFAULT '';
INSERT INTO categorydef VALUES ('','Telephone call','bac','-1','private','%','%');
INSERT INTO categorydef VALUES ('','Email','bac','-1','private','%','%');
INSERT INTO categorydef VALUES ('','Letter','bac','-1','private','%','%');
INSERT INTO categorydef VALUES ('','In person','bac','-1','private','%','%');
INSERT INTO rating VALUES ('private','confidential','restricted access','-1');
INSERT INTO rating VALUES ('private','not confidential','shared with staff','1');
