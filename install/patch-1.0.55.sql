DROP TABLE IF EXISTS report_skill;
CREATE TABLE report_skill (
		 id				int(10) unsigned not null auto_increment,
		 name			text not null default '',
		 subtype			varchar(20) not null default '',
		 profile_id		smallint(6)  unsigned not null default '0',
		 subject_id		varchar(10) not null default '',
		 component_id		varchar(10) not null default '',
		 stage			char(3) not null,
		 rating			smallint(6) not null,
		 rating_name		varchar(20) not null,
		 index			index_profile(profile_id),
   		 primary key	(id)
) ENGINE=MYISAM;
DROP TABLE IF EXISTS report_skill_log;
CREATE TABLE report_skill_log (
	     	 id			int unsigned not null auto_increment,
		 skill_id		int  unsigned not null default '0',
		 student_id		int  unsigned not null default '0',
		 date			date not null default '0000-00-00',
		 value			smallint not null default 0,
		 comment		text not null default '',
	  	 teacher_id		varchar(14) not null default '',
		 index			index_skill(skill_id),
		 index			index_student(student_id),
   		 primary key	(id)
) ENGINE=MYISAM;
