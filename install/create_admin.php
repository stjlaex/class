<?php
/**	Tables the core admin tables
 */

mysql_query("
CREATE TABLE student (
	id				int unsigned not null auto_increment, 
	surname 		varchar(120) not null default '', 
	forename		varchar(120) not null default '',
	middlenames		varchar(120) not null default '',
	surnamefirst	enum('Y','N') not null default 'N',
	middlenamelast	enum('Y','N') not null default 'N',
	preferredforename varchar(30) not null default '',
	formersurname	varchar(120) not null default '',
	gender 			enum('','M','F') not null default '', 
	dob 			date not null default '0000-00-00', 
	form_id 		varchar(10) not null default '',
	yeargroup_id 	smallint, 
	index 			index_name (surname(5),forename(5)),
	index 			index_forename (forename(5)),
	primary key (id)
);");
mysql_query("
CREATE TABLE form (
		id					varchar(10) not null default '',
		name				varchar(20) not null default '',
		yeargroup_id		smallint not null default '0',
		teacher_id			varchar(14) not null default '',
		primary key (id)
);");
mysql_query("
CREATE TABLE yeargroup (
		id				smallint not null default '0',
		name			varchar(20) not null default '',
		sequence	   	smallint unsigned not null default '0',
		section_id		smallint unsigned not null default '0',
		primary key (id)
);");
mysql_query("
CREATE TABLE subject (
		id				varchar(10) not null default '',
		name			varchar(40) not null default '',
		primary key (id)

);");
mysql_query("
CREATE TABLE course (
	id				varchar(10) not null default '', 
	name 			varchar(40) not null default '',
	sequence	   	smallint unsigned not null default '0',
    generate		enum('', 'forms','sets','none') not null default '',
	naming			varchar(40) not null default '',
	many			smallint unsigned not null default '4',
	section_id		smallint not null default 0,
	endmonth		enum('','1','2','3','4','5','6','7','8','9','10','11','12') not null default '',
	primary key (id)
);");
mysql_query("
CREATE TABLE cridbid (
		course_id		varchar(10) not null default '',
	    subject_id		varchar(10) not null default '',
		primary 		key (course_id, subject_id)
);");
mysql_query("
CREATE TABLE classes (
		course_id		varchar(10) not null default '',
	    subject_id		varchar(10) not null default '',
		stage			char(3) not null default '',
        generate		enum('', 'forms','sets','none') not null default '',
		naming			varchar(40) not null default '',
		many			smallint unsigned default 4,
		primary 		key (course_id, subject_id, stage)
);");
mysql_query("
CREATE TABLE class (
       	id	    		varchar(10) not null default '',
       	detail	    	varchar(240) not null default '',
		subject_id		varchar(10) not null default '',
		course_id		varchar(10) not null default '',
		stage			char(3) not null default '',
		index			index_bid (subject_id),
		index			index_crid (course_id),
		primary key  	(id)
);");
mysql_query("
CREATE TABLE cidsid (
		 class_id		varchar(10) not null default '',
		 student_id		int unsigned not null default 0,
		 primary key 	(class_id, student_id)
);");
mysql_query("
CREATE TABLE tidcid (
		 teacher_id		varchar(14) not null default '',
		 class_id		varchar(10) not null default '',
		 component_id  	varchar(10) not null default '',
		 primary key 	(teacher_id, class_id)
);");
mysql_query("
CREATE TABLE component (
		 id				varchar(10) not null default '',
		 course_id		varchar(10) not null default '',
		 subject_id		varchar(10) not null default '',
		 status			enum('N','V') not null default 'N',
		 primary key 	(id, course_id, subject_id)
);");
mysql_query("
CREATE TABLE  users (
  uid			int(10) unsigned auto_increment,
  username		varchar(14) not null default '', 
  passwd		char(32) binary not null default '',
  cookie		char(32) binary not null default '',
  session		char(32) binary not null default '',
  ip			varchar(15) binary not null default '', 
  forename		varchar(50) not null default '',
  surname		varchar(50) not null default '',
  title			varchar(20) not null default '',
  email			varchar(200) not null default '',
  emailuser		varchar(60) not null default '',
  emailpasswd	char(32) binary not null default '',
  epfusername	varchar(128) not null default '',
  language		varchar(10) not null default '',
  firstbookpref varchar(20),
  role			varchar(20),
  senrole		enum('0','1') not null,
  worklevel	   	enum('-1','0', '1', '2') not null default '0',
  nologin		tinyint(1) not null default '0',
  logcount		int(10) unsigned not null default '0',
  logtime		timestamp(14),
  index			index_name (username),
  primary key	(uid)
)");
mysql_query("
CREATE TABLE  history (
  uid			int(10) unsigned,
  page			varchar(60) not null default '', 
  time			timestamp(14)
)");
mysql_query("
CREATE TABLE groups (
	gid 			int(10) unsigned auto_increment,
	subject_id		varchar(10) not null default '',
	course_id		varchar(10) not null default '',
	yeargroup_id	smallint,
	name 			varchar(50) not null default '',
    type			enum('a','p','b') not null default 'a',
	index			index_crid (course_id),
	index			index_bid (subject_id),
	index			index_yid (yeargroup_id),
  	primary key		(gid)
)");
mysql_query("
CREATE TABLE perms (
  uid 			int(10) not null default '0',
  gid 			int(10) not null default '0',
  r				set('0','1') not null default '0',
  w				set('0','1') not null default '0',
  x				set('0','1') not null default '0',
  e				set('0','1') not null default '0',
  primary key  	(uid, gid)
)");
mysql_query("
CREATE TABLE section (
	id				smallint unsigned not null auto_increment, 
	name			varchar(240) not null default '', 
	sequence	   	smallint unsigned not null default '0',
	address_id		int unsigned not null default '0',
	gid 			int(10) NOT NULL default '0',
	primary key		(id)
)");
mysql_query("
CREATE TABLE community (
	id			int unsigned not null auto_increment, 
	name		varchar(30) not null default '', 
    type		enum('','academic','family','form','year','tutor','alumni','enquired','applied','accepted','trip','reg','stop','extra','','accomodation') not null default '',
	year		year not null default '0000',
	season		enum('','S','W','M','1','2','3','4','5','6','7','8','9','a','b','c') not null default '',
	capacity	smallint unsigned not null default 0,
    detail		varchar(240) not null default '',
	index		indexcom (type,name),
	primary		key (id)
);");

mysql_query("
CREATE TABLE comidsid (
	community_id	int unsigned not null default '0',
	student_id		int unsigned not null default '0',
	joiningdate		date null,
	leavingdate 	date null,
	primary key 	(community_id, student_id)
);");

mysql_query("
CREATE TABLE cohort (
	id				int unsigned not null auto_increment, 
	course_id	   	varchar(10) not null default '',
	stage			char(3) not null default '',
	year			year not null default '0000',
	season			enum('','S','W','M','1','2','3','4','5','6','7','8','9','a','b','c') not null default 'S',
	unique			indexcohort (course_id,stage,year,season),
	primary key (id)
);");

mysql_query("
CREATE TABLE cohidcomid (
	cohort_id		int unsigned not null default '0',
	community_id	int unsigned not null default '0',
	primary key 	(cohort_id, community_id)
);");

mysql_query("
CREATE TABLE categorydef (
	id				int unsigned not null auto_increment, 
	name			varchar(60) not null default '',
	type			char(3) not null default '',
	subtype			varchar(20) not null default '',
	rating			enum('-12', '-11', '-10', '-9', '-8', '-7',
							'-6', '-5', '-4', '-3', '-2', '-1', 
							'0', '1', '2', '3', '4', '5', '6',
							'7', '8', '9', '10', '11', '12') not null default 0,
	rating_name		varchar(30) not null default ''
	comment			text not null default '',
	subject_id		varchar(10) not null default '',
	course_id		varchar(10) not null default '',
	section_id		smallint not null default 0,
   	primary key		(id)
);");

mysql_query("
CREATE TABLE rating (
	name			varchar(30) not null default '',
	descriptor		varchar(30) not null default '',
	longdescriptor	varchar(250) not null default '',
	value			enum('-12', '-11', '-10', '-9', '-8', '-7',
							'-6', '-5', '-4', '-3', '-2', '-1', 
							'0', '1', '2', '3', '4', '5', '6',
							'7', '8', '9', '10', '11', '12') not null default 0,
   	primary key		(name, value)
);");

?>
