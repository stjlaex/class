CREATE TABLE report (
		id				int  unsigned not null auto_increment,
		title			varchar(60) not null default '',
		date			date not null default '0000-00-00',
		deadline		date not null default '0000-00-00',
		comment			text,
	 	course_id		varchar(10) not null default '',
		stage			char(3) not null default '',
		subject_status	enum('None','N','V','O','AV','A') not null default 'None',
		component_status enum('None','N','V','O','AV','A') not null default 'None',
		addcomment		enum('no','yes') not null default 'no', 
		commentlength	smallint(6) unsigned not null default '0',
		commentcomp		enum('no','yes') not null default 'no', 
		addcategory		enum('no','yes') not null default 'no', 
		style			varchar(60) not null default '',
		transform		varchar(60) not null default '',
		rating_name		text not null default '',
		year			year not null default '0000',
		subject_id		varchar(10) not null default '',
		primary key		(id)
) type=myisam;

CREATE TABLE reportentry (
		 report_id		int  unsigned not null default '0',
		 student_id		int  unsigned not null default '0',
		 subject_id		varchar(10) not null default '',
		 entryn			smallint unsigned not null auto_increment,
		 component_id	varchar(10) not null default '',
		 description	varchar(60) not null default '',
		 comment		text not null default '',
		 category		text not null default '',
	  	 teacher_id		varchar(14) not null default '',	
		 primary key 	(report_id, student_id, subject_id,
							component_id, entryn)
) type=myisam;

CREATE TABLE assessment (
	id				int unsigned not null auto_increment, 
	subject_id		varchar(10) not null default '%',
	component_id   	varchar(10) not null default '',
	stage			char(3) not null default '',
	method			char(3) not null default '',
	element			char(3) not null default '',
	description		varchar(60) not null default '',
	label			varchar(12) not null default '',
	resultqualifier	char(2) not null default '',
	resultstatus    enum('R', 'T', 'E', 'S') default 'R' not null,
	outoftotal		smallint(5) unsigned not null default '0',
	derivation		varchar(60) not null default '',
	statistics		enum('N','Y') not null,
    grading_name	varchar(20) not null default '',
	course_id		varchar(10) not null default '%',
	component_status enum('None','N','V','O','AV','A') not null default 'None',
	strand_status	enum('None','N','V','O','AV','A') not null default 'None',
	year			year not null default '0000',
	season          enum('','S','W','M','1','2','3','4','5','6','7','8','9','a','b','c') not null,
	creation		date not null default '0000-00-00',
	deadline		date not null default '0000-00-00',
	profile_name	varchar(60) not null default '',
	index			index_subject(subject_id),
   	primary key		(id)
) type=myisam;

CREATE TABLE eidsid (
	id				int unsigned not null auto_increment,
	assessment_id	int unsigned not null default '0',
	student_id		int unsigned not null default '0',
	subject_id		varchar(10) not null default '',
	component_id	varchar(10) not null default '',
	date			date not null default '0000-00-00',
	resultstatus	enum('', 'I', 'P') not null,
	result			varchar(30) not null default '',
    value		 	float not null default '0.0',
	weight			smallint unsigned not null default '1',
	examboard		char(3) not null default '',
	examsyllabus	char(6) not null default '',
	index			index_result(student_id),
	primary key 	(id)	
) type=myisam;

CREATE TABLE eidmid (
	assessment_id	int unsigned not null default '0',
	mark_id			int unsigned not null default '0',
	date			date not null default '0000-00-00',
	resultstatus	enum('I', 'R', 'T', 'P', 'E') not null,
	result			char(3) not null default '',
	examboard		char(3) not null default '',
	examsyllabus	char(6) not null default '',
	primary key 	(assessment_id, mark_id)
) type=myisam;

CREATE TABLE rideid (
	report_id		int unsigned not null default '0',
	assessment_id	int unsigned not null default '0',
	priority		smallint unsigned not null default 0,
	primary key 	(report_id, assessment_id)
) type=myisam;

CREATE TABLE ridcatid (
	report_id		int unsigned not null default '0',
	categorydef_id	int unsigned not null default '0',
	subject_id		varchar(10) not null default '%',
	primary key 	(report_id, categorydef_id, subject_id)
) type=myisam;

CREATE TABLE statvalues (
	stats_id		int unsigned not null auto_increment,
    stage		 	char(3) not null default '',
	subject_id		varchar(10) not null default '%',
	component_id	varchar(10) not null default '',
	m				float not null default '0',
	c				float not null default '0',
	error			float not null default '0',
	sd				float not null default '0',
	weight			float not null default '0',
    value1		 	float not null default '0.0',
    value2		 	float not null default '0.0',
    value3		 	float not null default '0.0',
    value4		 	float not null default '0.0',
	date			date not null default '0000-00-00',
	primary key 	(stats_id, stage, subject_id, component_id)
) type=myisam;

CREATE TABLE stats (
	id				int unsigned not null auto_increment,
	description		varchar(60) not null default '',
	course_id		varchar(10) not null default '%',
	assone_id		int unsigned not null default '0',
	asstwo_id		int unsigned not null default '0',
	profile_name	varchar(60) not null default '',
	primary key 	(id)
) type=myisam;

CREATE TABLE derivation (
	resultid		int unsigned not null default '0',
	operandid		int unsigned not null default '0',
	type		    enum('A', 'M', 'R', 'S') default 'A' not null,
	element			char(3) not null default '',
	primary key 	(resultid, operandid, type)
) type=myisam;

CREATE TABLE report_event (
	report_id		int unsigned not null default '0',
	student_id		int unsigned not null default '0',
	date			date not null default '0000-00-00',
	success			enum('0', '1') not null,
	time			timestamp(14),
	try				tinyint(4) not null default '0'
	primary key 	(report_id,student_id)
) type=myisam;
