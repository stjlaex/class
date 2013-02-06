DROP TABLE IF EXISTS fees_remittance;
CREATE TABLE fees_remittance (
	id				int unsigned not null auto_increment,
	name			varchar(240) not null default '',
	concepts		varchar(240) not null default '',
	duedate			date not null default '0000-00-00',
	issuedate		date not null default '0000-00-00',
	year			year not null default '0000',
	account_id		int unsigned not null default 0,
	primary key  	(id)
) ENGINE=MYISAM;
DROP TABLE IF EXISTS fees_charge;
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
) ENGINE=MYISAM;
DROP TABLE IF EXISTS fees_invoice;
CREATE TABLE fees_invoice (
	id				int unsigned not null auto_increment, 
	reference		varchar(240) not null default '',
	account_id		int unsigned not null default 0,
	remittance_id	int unsigned not null default 0,
   	primary key		(id)
) ENGINE=MYISAM;
DROP TABLE IF EXISTS update_event;
CREATE TABLE update_event (
	id				int unsigned not null auto_increment, 
	student_id		int unsigned not null default '0',
	updatedate		date not null default '0000-00-00',
	export			enum('0', '1') not null,
	exportdate		date not null default '0000-00-00',
	primary key 	(id)
) ENGINE=MYISAM;
DROP TABLE IF EXISTS message_text_event;
CREATE TABLE message_text_event (
	id		 		int unsigned not null auto_increment,
	some_id			int unsigned not null default '0',
	texttype		enum('s', 'g', 'u') not null,
	phonenumber		varchar(22) not null default '',
	textbody		text not null default '',
	date			date not null default '0000-00-00',
	success			enum('0', '1') not null,
	time			timestamp,
	try				tinyint(4) not null default '0',
	primary key 	(id)
) ENGINE=MYISAM;
ALTER TABLE info CHANGE boarder
	boarder char(2) not null default 'N';
