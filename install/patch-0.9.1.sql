ALTER TABLE component 
	CHANGE status status enum('N','V','O','U') not null default 'N';
ALTER TABLE assessment  
	CHANGE component_status component_status enum('None','N','V','O','AV','A') NOT NULL DEFAULT 'None';
ALTER TABLE assessment  
	CHANGE strand_status strand_status enum('None','N','V','O','AV','A') NOT NULL DEFAULT 'None';
ALTER TABLE report
	  CHANGE rating_name rating_name text not null default '';
CREATE TABLE report_event (
	report_id		int unsigned not null default '0',
	student_id		int unsigned not null default '0',
	date			date not null default '0000-00-00',
	success			enum('0', '1') not null default '0',
	time			timestamp(14),
	primary key 	(report_id,student_id)
);
CREATE TABLE message_event (
	id				int unsigned not null auto_increment,
	personid		int unsigned not null default '0',
	persontype 		enum('','u','s','g') not null default '',
	email			varchar(240) not null default '',
	phonenumber		varchar(22) not null default '',
	messagesubject 	text not null default '',
	messagebody 	text not null default '',
	date			date not null default '0000-00-00',
	success			enum('0', '1') not null default '0',
	time			timestamp(14),
	index 			index_person (personid,persontype),
	primary key 	(id)
);
