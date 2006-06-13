<?php
/*	Tables for adminbase
*/

mysql_query("
CREATE TABLE student (
	id				int unsigned not null auto_increment, 
	surname 		varchar(30) not null default '', 
	forename		varchar(30) not null default '',
	middlenames		varchar(30) not null default '',
	surnamefirst	enum('Y','N') not null default 'N',
	middlenamelast	enum('Y','N') not null default 'N',
	preferredforename varchar(30) not null default '',
	formersurname	varchar(30) not null default '',
	gender 			enum('','M','F') not null default '', 
	dob 			date not null default '0000-00-00', 
	form_id 		varchar(20) not null default '',
	yeargroup_id 	smallint not null default '0', 
	index 			index_name (surname(5),forename(5)),
	index 			index_forename (forename(5)),
	primary key (id)
);");

mysql_query("
CREATE TABLE form (
		id					varchar(20) not null default '',
		yeargroup_id		smallint not null default '0',
		teacher_id			varchar(10) not null default '',
		primary key (id)
);");

mysql_query("
CREATE TABLE yeargroup (
		id				smallint not null default '0',
		name			varchar(20) not null default '',
		ncyear			enum('P','N', 'R', '1', '2', '3', '4', '5', '6',
							'7', '8', '9', '10', '11', '12', '13',
							'14') not null,
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
CREATE TABLE  course (
	id				varchar(10) not null default '', 
	name 			varchar(40) not null default '',
	stage	   		smallint not null default '0',
    generate		enum('', 'forms','sets','none') not null default '',
	naming			varchar(40) not null default '',
	many			smallint unsigned not null default '4',
   	section_id		smallint unsigned not null default '0',
	primary key (id)
);");

mysql_query("
CREATE TABLE cohort (
   	id				int not null default '0',
	course_id	   	varchar(10) not null default '',
	stage			char(3) not null default '',
	year			year not null default '0000',
	season			enum('','S','W','M','1','2','3','4','5','6','7','8','9','a','b','c') NOT NULL DEFAULT 'S',
	status			enum('','C') not null default ''
);");

mysql_query("
CREATE TABLE cridbid (
		 course_id		varchar(10) not null default '',
		 subject_id		varchar(10) not null default '',
		 primary key 	(course_id, subject_id)
);");


mysql_query("
CREATE TABLE classes (
		yeargroup_id	smallint not null default 0,
		course_id		varchar(10) not null default '',
		subject_id		varchar(10) not null default '',
        generate		enum('', 'forms','sets','none') not null default '',
		naming			varchar(40) not null default '',
		many			smallint unsigned default 4,
		index			index_crid (course_id),
		primary 		key (yeargroup_id, course_id, subject_id)
);");
mysql_query("
CREATE TABLE class (
       	id	    		varchar(10) not null default '',
       	details	    	varchar(100) not null default '',
		subject_id		varchar(10) not null default '',
		course_id		varchar(10) not null default '',
		yeargroup_id	smallint not null default 0,
		stage			char(3) not null default '',
		index			index_bid (subject_id),
		index			index_crid (course_id),
		index			index_yid  (yeargroup_id),
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
		 teacher_id		varchar(10) not null default '',
		 class_id		varchar(10) not null default '',
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
  username		varchar(14) NOT NULL default '', 
  passwd		char(32) binary NOT NULL default '',
  cookie		char(32) binary NOT NULL default '',
  session		char(32) binary NOT NULL default '',
  ip			varchar(15) binary NOT NULL default '', 
  forename		varchar(50) NOT NULL DEFAULT '',
  surname		varchar(50) NOT NULL DEFAULT '',
  email			varchar(200) NOT NULL DEFAULT '',
  language		varchar(10) NOT NULL DEFAULT '',
  firstbookpref varchar(20),
  role			varchar(20),
  nologin		tinyint(1) NOT NULL default '0',
  logcount		int(10) unsigned NOT NULL default '0',
  logtime		timestamp(14),
  INDEX			index_name (username),
  PRIMARY KEY  (uid)
)");
mysql_query("
CREATE TABLE  history (
  uid			int(10) unsigned,
  page			varchar(60) NOT NULL default '', 
  time			timestamp(14)
)");
mysql_query("
CREATE TABLE groups (
	gid 			int(10) unsigned auto_increment,
	subject_id		varchar(10) not null default '',
	course_id		varchar(10) not null default '',
	yeargroup_id	smallint not null default '',
	name 			varchar(50) not null default '',
	INDEX			index_crid (course_id),
	INDEX			index_bid (subject_id),
	INDEX			index_yid (yeargroup_id),
  	PRIMARY KEY		(gid)
)");
mysql_query("
CREATE TABLE perms (
  uid 			int(10) NOT NULL default '0',
  gid 			int(10) NOT NULL default '0',
  r				set('0','1') NOT NULL default '0',
  w				set('0','1') NOT NULL default '0',
  x				set('0','1') NOT NULL default '0',
  e				set('0','1') NOT NULL default '0',
  PRIMARY KEY  	(uid, gid)
)");
mysql_query("
CREATE TABLE section (
	id		smallint unsigned auto_increment, 
	name 	varchar(30) not null default '', 
	primary key (id)
)");
?>


