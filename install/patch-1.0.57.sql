ALTER TABLE merits  ADD core_value VARCHAR(10) NOT NULL;
CREATE TABLE report_skill_log (
		 id			int unsigned not null auto_increment,
		 student_id	int unsigned not null default '0',
		 skill_id		int unsigned not null default '0',
		 report_id	int unsigned not null default '0',
		 rating		smallint not null default '0',
		 comment		text not null default '',
		 teacher_id	varchar(14) not null default '',
		 timestamp	timestamp,
		 index		index_skill(skill_id),
		 index		index_student(student_id),
		 primary key	(id)
) ENGINE=MYISAM;
