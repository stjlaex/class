ALTER TABLE grading
	DROP subject_id;
ALTER TABLE grading
	DROP course_id;
DROP incidenthistory;
CREATE TABLE incidenthistory (
	incident_id		int unsigned not null, 
	entryn			smallint unsigned not null auto_increment,
	comment			text not null default '',
	categorydef_id	int unsigned not null default '0',
	teacher_id		varchar(14) not null default '',
	entrydate		date not null,
   	primary key		(incident_id,entryn)
	);
ALTER TABLE incidents
	  CHANGE outcome closed enum('N','Y') not null;
ALTER TABLE guardian
	  ADD title varchar(20) not null default '' AFTER middlenames;
ALTER TABLE community
	DROP key indexcom;
ALTER TABLE community
 	ADD key indexcom (type,name);
