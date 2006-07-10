ALTER TABLE assessment 
	ADD year year NOT NULL DEFAULT '0000' AFTER course_id;
ALTER TABLE assessment 
	ADD season enum('','S','W','M','1','2','3','4','5','6','7','8','9','a','b','c') NOT NULL AFTER year;
ALTER TABLE assessment 
	ADD component_id varchar(10) NOT NULL DEFAULT '' AFTER subject_id;
ALTER TABLE assessment 
	ADD resultstatus enum('R', 'T', 'E') NOT NULL AFTER resultqualifier;
ALTER TABLE assessment 
	CHANGE component element char(3) NOT NULL DEFAULT '';
ALTER TABLE assessment 
	DROP ncyear;
ALTER TABLE assessment 
	ADD label varchar(12) NOT NULL DEFAULT '' AFTER description;
ALTER TABLE assessment  
	ADD component_status enum('None','N','V','A') NOT NULL DEFAULT 'None' AFTER course_id;

ALTER TABLE eidsid 
	CHANGE resultstatus resultstatus enum('', 'I', 'P') NOT NULL;
ALTER TABLE eidsid
    ADD value float NOT NULL DEFAULT '0.0' AFTER result;
ALTER TABLE eidmid 
	CHANGE resultstatus resultstatus enum('', 'I', 'P') NOT NULL;

ALTER TABLE yeargroup 
	CHANGE ncyear ncyear enum('P','N', 'R', '1', '2', '3', '4', '5', '6',
							'7', '8', '9', '10', '11', '12', '13',
							'14') not null,

ALTER TABLE method 
	CHANGE method method char(3) NOT NULL DEFAULT '';
ALTER TABLE method 
	ADD levelling_name varchar(20) NOT NULL DEFAULT '' AFTER markdef_name;
ALTER TABLE method 
	ADD assessment_year year NOT NULL DEFAULT '0000' AFTER levelling_name;
ALTER TABLE method 
	DROP primary key, 
	ADD primary key	(method, resultqualifier, course_id, assessment_year);

ALTER TABLE reportentry 
	CHANGE teacher_id teacher_id varchar(14) NOT NULL DEFAULT '';
ALTER TABLE reportentry 
	CHANGE entryn entryn smallint unsigned not null auto_increment;

CREATE TABLE statvalues (
	stats_id		int unsigned not null auto_increment,
	subject_id		varchar(10) not null default '%',
	component_id	varchar(10) not null default '',
	m				float not null default '0',
	c				float not null default '0',
	error			float not null default '0',
	sd				float not null default '0',
	weight			float not null default '0',
	primary key 	(stats_id, subject_id, component_id)
);

CREATE TABLE stats (
	id				int unsigned not null auto_increment,
	description		varchar(60) not null default '',
	course_id		varchar(10) not null default '%',
	assone_id		int unsigned not null default '0',
	asstwo_id		int unsigned not null default '0',
	year			year not null default '0000',
	primary key 	(id)
);

INSERT INTO categorydef (id , name , type , rating , rating_name , subject_id , course_id) VALUES ('', 'Unknown', 'bac', '-1', 'private', '%', '%');
ALTER TABLE background CHANGE detail detail BLOB NULL DEFAULT NULL;
ALTER TABLE component 
	ADD status enum('N','V') NOT NULL DEFAULT 'N' AFTER subject_id;
ALTER TABLE class
    ADD stage char(3) not null default '' AFTER yeargroup_id;
ALTER TABLE mark DROP course_id;
ALTER TABLE mark DROP subject_id;
CREATE TABLE cohort (
   	id				int not null default '0',
	course_id	   	varchar(10) not null default '',
	stage			char(3) not null default '',
	year			year not null default '0000',
	season			enum('','S','W','M','1','2','3','4','5','6','7','8','9','a','b','c') NOT NULL DEFAULT 'S',
	status			enum('','C') not null default '',
	primary key 	(id)
);

ALTER TABLE users
    ADD language varchar(10) not null default '' AFTER email;
