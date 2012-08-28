
CREATE TABLE info (
	student_id		int unsigned not null default 0, 
	upn				char(20) not null default '',
	formerupn		char(20) not null default '',
	otherpn1		varchar(40) not null default '',
	otherpn2		varchar(40) not null default '',
	enrolstatus		enum('EN','AP','AT','ATD','ACP','AC','RE','CA','WL','C','P', 'G','S','M') not null default 'C',
	entrydate		date null,
	leavingdate 	date null,
	email			varchar(240) not null default '',
	epfusername		varchar(128) not null default '',
	phonenumber		varchar(22) not null default '',
	parttime		enum('N','Y') not null,
	boarder			char(2) not null default 'N',
	nationality		char(2) not null default '',
	secondnationality		char(2) not null default '',
	birthplace		varchar(240) not null default '',
	countryoforigin	char(2) not null default '',
	ethnicity		char(4) not null default '',
	language		char(4) not null default '',
	languagetype	enum('NOT','F','M','H','T','S','C') not null,
	religion		enum('NOT', 'NO','BU','CH','HI','JE','MU','SI','OT') not null,
	reledu			enum('A','W') not null,
	relwor			enum('A','W') not null,
	sen				enum('N','Y') not null,
	medical			enum('N','Y') not null,
	incare			enum('N','Y') not null,
	transportmode	enum('NOT','F', 'C', 'T', 'B', 'S') not null,
	staffchild 		enum('N','Y') not null, 
	appnotes		text not null default '',
	appcategory		varchar(240) not null default '',
	appdate			date null,
	siblings		enum('N','Y') not null,
   	primary key		(student_id)
) type=myisam;


CREATE TABLE accomodation (
	id					int unsigned not null auto_increment,
	student_id			int unsigned not null, 
	community_id		int unsigned not null, 
	roomcategory		char(2) not null default '',
	building			char(2) not null default '',
	room				varchar(4) not null default '',
	bed					varchar(4) not null default '',
	invoice				varchar(80),
	bookingdate			date null,
	arrivaldate			date,
	arrivaltime			time,
	arrivalairport		varchar(240) not null default '',
	arrivalflight		varchar(240) not null default '',
	departuredate		date,
	departuretime		time,
	departureairport	varchar(240) not null default '',
	departureflight		varchar(240) not null default '',
	index				index_student (student_id),
   	primary key			(id)
) type=myisam;


CREATE TABLE senhistory (
	id				int unsigned not null auto_increment,
	student_id		int unsigned not null, 
	senprovision	enum('N','A','P','Q','S') not null, 
	startdate		date,
	reviewdate		date,
	assessmentdate	date,
	index			index_student (student_id),
   	primary key		(id)
) type=myisam;


CREATE TABLE sentype (
	student_id		int unsigned not null, 
	entryn			smallint unsigned not null auto_increment,
	senranking		enum('1', '2', '3') not null,
	sentype			char(4) not null default '',
	senassessment	enum('I','E') not null,
   	primary key		(student_id, entryn, senassessment)
) type=myisam;


CREATE TABLE sencurriculum (
	senhistory_id	int unsigned not null,
	subject_id		varchar(10) not null default '', 
	curriculum		enum('A','M','D') not null,
	categorydef_id	int unsigned not null default '0',
	comments		text not null default '',
	targets			text not null default '',
	outcome			text not null default '',
	extra			text not null default '',
   	primary key		(senhistory_id, subject_id)
) type=myisam;


CREATE TABLE incidents (
	id				int unsigned not null auto_increment,
	student_id		int unsigned not null, 
	entrydate		date,
	yeargroup_id	smallint not null default '0',
	category		varchar(30) not null default '',
	detail			text not null default '',
	closed			enum('N','Y') not null,
	subject_id		varchar(10) not null default '',
	teacher_id		varchar(14) not null default '',	
	index			index_student (student_id),
   	primary key		(id)
) type=myisam;


CREATE TABLE incidenthistory (
	incident_id		int unsigned not null, 
	entryn			smallint unsigned not null auto_increment,
	comment			text not null default '',
	category		varchar(30) not null default '',
	teacher_id		varchar(14) not null default '',
	entrydate		date not null,
   	primary key		(incident_id,entryn)
) type=myisam;


CREATE TABLE comments (
	id				int unsigned not null auto_increment,
	student_id		int unsigned not null, 
	entrydate		date,
	yeargroup_id	smallint not null default '0',
	category		varchar(100) not null default '',
	detail			text not null default '',
	subject_id		varchar(10) not null default '',
	teacher_id		varchar(14) not null default '',
	guardians		enum('0','1') not null default'0',
	incident_id		int unsigned not null, 
	merit_id		int unsigned not null, 
	index			index_student (student_id),
   	primary key		(id)
) type=myisam;


CREATE TABLE background (
	id				int unsigned not null auto_increment,
	student_id		int unsigned not null,
	type			char(3) not null default '',
	entrydate		date,
	yeargroup_id	smallint not null default '0',
	detail			text not null default '',
	category		varchar(100) not null default '',
	subject_id		varchar(10) not null default '',
	teacher_id		varchar(14) not null default '',	
	index			index_student (student_id),
   	primary key		(id)
) type=myisam;


CREATE TABLE exclusions (
	student_id		int unsigned not null,
	category		enum('F', 'P', 'L') not null default 'F',
	reason			text not null default '',
	startdate		date not null default '0000-00-00',
	session			enum('NA', 'AM', 'PM') not null,
	enddate			date not null default '0000-00-00',
	appeal			enum('N','Y') not null,
	appealdate		date not null default '0000-00-00',
	appealresult	enum('', 'R', 'S') not null default '',
   	primary key		(student_id, startdate)
) type=myisam;


CREATE TABLE guardian (
	id			int unsigned not null auto_increment, 
	surname		varchar(120) not null default '', 
	forename	varchar(120) not null default '', 
	middlenames	varchar(30) not null default '', 
	title		varchar(20) not null default '', 
	addresstitle varchar(120) not null default '',
	gender		enum('F','M') not null, 
	dob			date not null default '0000-00-00',
	translator	enum('N','Y') not null,
	language	char(3) not null default '',
	nationality	char(2) not null default '',
	email		varchar(240) not null default '',
	epfusername	varchar(128) not null default '',
	profession	varchar(120) not null default '',
	companyname	varchar(240) not null default '',
	note		text not null default '',
	code		varchar(120) not null default '',
	private		enum('N','Y') not null,
	index index_name (surname(5),forename(5)),
	index index_forename (forename(5)),
	primary key (id)
) type=myisam;


CREATE TABLE gidsid (
		 guardian_id		int unsigned not null,
		 student_id			int unsigned not null,
		 priority			enum('0','1','2','3','4') not null,
		 mailing			enum('0','1','2','3','4') not null,
		 relationship		enum('NOT','CAR','DOC','FAM','OTH', 
								'PAM','PAF','STP','REL','SWR','HFA','AGN','GRM','GRF','TUT') not null,
		 responsibility		enum('N','Y') not null,
		 paymenttype		enum('0','1','2','3','4','5','6','7','8') not null default '0',
		 primary key 	(guardian_id, student_id)
) type=myisam;


CREATE TABLE gidaid (
		 guardian_id	int unsigned not null,
		 address_id		int unsigned not null,
		 priority		smallint unsigned not null,
		 addresstype	enum('H', 'W', 'V', 'O') not null,
		 primary key 	(guardian_id, address_id)
) type=myisam;



CREATE TABLE address (
   	id				int unsigned not null auto_increment, 	
	street			varchar(160) not null default '',
	neighbourhood	varchar(160) not null default '',
	region			varchar(160) not null default '',
	postcode		varchar(8) not null default '',
	country			varchar(40) not null default '',
	lat				decimal(10,6) not null,
	lng				decimal(10,6) not null,
	index			index_address (region(5)),
	primary key (id)
) type=myisam;



CREATE TABLE phone (
	id				int unsigned not null auto_increment,
	some_id			int unsigned not null default '0',
	number			varchar(22) not null default '',
	phonetype		enum('H', 'M', 'W', 'F', 'O') not null,
	index			index_id (some_id),
	primary key 	(id)	
) type=myisam;


CREATE TABLE message_event (
  id bigint(20) NOT NULL default '0',
  create_time datetime NOT NULL default '0000-00-00 00:00:00',
  time_to_send datetime NOT NULL default '0000-00-00 00:00:00',
  sent_time datetime default NULL,
  id_user bigint(20) NOT NULL default '0',
  ip varchar(20) NOT NULL default 'unknown',
  sender varchar(50) NOT NULL default '',
  recipient text NOT NULL,
  headers text NOT NULL,
  body longtext NOT NULL,
  try_sent tinyint(4) NOT NULL default '0',
  delete_after_send tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (id),
  KEY time_to_send (time_to_send),
  KEY id_user (id_user)
) type=myisam;


CREATE TABLE merits (
	id				int unsigned not null auto_increment, 
	student_id		int unsigned not null,
	date			date not null default '0000-00-00',
	year			year not null default '0000',
	activity		varchar(100) not null default '',
	detail			text not null default '',
	result			varchar(30) not null default '',
    value		 	smallint not null default '0',
	teacher_id		varchar(14) not null default '',
	subject_id		varchar(10) not null default '',
	component_id	varchar(10) not null default '',
	index			index_result(student_id),
   	primary key		(id)
) type=myisam;

CREATE TABLE update_event (
	id				int unsigned not null auto_increment, 
	student_id		int unsigned not null default '0',
	updatedate		date not null default '0000-00-00',
	export			enum('0', '1') not null,
	exportdate		date not null default '0000-00-00',
	primary key 	(id)
) type=myisam;

CREATE TABLE message_text_event (
	id		 		int unsigned not null auto_increment,
	some_id			int unsigned not null default '0',
	texttype		enum('s', 'g', 'u') not null,
	phonenumber		varchar(22) not null default '',
	textbody		text not null default '',
	date			date not null default '0000-00-00',
	success			enum('0', '1') not null,
	time			timestamp(14),
	try				tinyint(4) not null default '0'
	primary key 	(id)
) type=myisam;
