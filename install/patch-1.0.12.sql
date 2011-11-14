CREATE TABLE fees_account (
	id				int unsigned not null auto_increment, 
	guardian_id		int unsigned not null default 0,
	bankname		varbinary(120) not null default '',
	bankcountry		varbinary(20) not null default '',
	bankcode		varbinary(40) not null default '',
	bankbranch		varbinary(40) not null default '',
	bankcontrol 	varbinary(20) not null default '',
	banknumber 		varbinary(60) not null default '',
	index 			index_gid (guardian_id),
	primary key  	(id)
) type=myisam;
ALTER TABLE sentype ADD 
	  senassessment ENUM ('I','E') NOT NULL AFTER sentype;
ALTER TABLE sentype DROP PRIMARY KEY ,
ADD PRIMARY KEY (student_id,entryn,senassessment);
ALTER TABLE senhistory ADD 
	  assessmentdate date AFTER reviewdate;