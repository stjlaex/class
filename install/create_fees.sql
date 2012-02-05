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
	note			varchar(240) not null default '',
	quantity		smallint unsigned not null default '0',
	catalogue_id	int unsigned not null default 0,
	budget_id		int unsigned not null default 0,
	tarif_id		int unsigned not null default 0,
	paymenttype		enum('0','1') not null default '0',
	payment			enum('0','1') not null default '0',
	paymentdate		date not null default '0000-00-00',
	invoice_id		int unsigned not null default 0,
	index 			index_sid (student_id),
	primary key  	(id)
) type=myisam;
CREATE TABLE fees_remittance (
	id				int unsigned not null auto_increment,
	name			varchar(240) not null default '',
	concepts		varchar(240) not null default '',
	duedate			date not null default '0000-00-00',
	issuedate		date not null default '0000-00-00',
	year			year not null default '0000',
	account_id		int unsigned not null default 0,
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
	concept_id		int not null default 0,
	name			varchar(240) not null default '',
	amount			smallint unsigned not null default '0',
	primary key  	(id)
) type=myisam;
CREATE TABLE fees_invoice (
	id				int unsigned not null auto_increment, 
	reference		varchar(240) not null default '',
	account_id		int unsigned not null default 0,
	remittance_id	int unsigned not null default 0,
   	primary key		(id)
) type=myisam;

