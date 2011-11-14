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
CREATE TABLE fees_charge (
	id				int unsigned not null auto_increment,
	student_id		int unsigned not null default 0,
	concept			varchar(10) not null default '',
	quantity		smallint unsigned not null default '0',
	catalogue_id	int unsigned not null default 0,
	budget_id		int unsigned not null default 0,
	tarif_id		int unsigned not null default 0,
	paymenttype		enum('0','1') not null default '0',
	payment			enum('0','1') not null default '0',
	paymentdate		date not null default '0000-00-00',
	index 			index_sid (student_id),
	primary key  	(id)
) type=myisam;
