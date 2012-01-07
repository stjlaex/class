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
CREATE TABLE fees_concept (
	id				int unsigned not null auto_increment,
	name			varchar(240) not null default '',
	inactive		enum('0','1') not null default '0',
	primary key  	(id)
) type=myisam;
CREATE TABLE fees_tarif (
	id				int unsigned not null auto_increment,
	concept_id		int unsigned not null default 0,
	name			varchar(240) not null default '',
	amount			smallint unsigned not null default '0',
	primary key  	(id)
) type=myisam;
CREATE TABLE fees_remittance (
	id				int unsigned not null auto_increment,
	name			varchar(240) not null default '',
	date			date not null default '0000-00-00',
	year			year not null default '0000',
	primary key  	(id)
) type=myisam;
ALTER TABLE fees_charge CHANGE
	  concept note varchar(240) not null default '';
ALTER TABLE fees_charge ADD
	  remittance_id int unsigned not null default 0 AFTER paymentdate;
ALTER TABLE sentype ADD 
	  senassessment ENUM ('I','E') NOT NULL AFTER sentype;
ALTER TABLE sentype DROP PRIMARY KEY,
	  ADD PRIMARY KEY (student_id,entryn,senassessment);
ALTER TABLE senhistory ADD 
	  assessmentdate date AFTER reviewdate;