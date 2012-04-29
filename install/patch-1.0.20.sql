CREATE TABLE fees_applied (
	id				int unsigned not null auto_increment,
	student_id		int unsigned not null default 0,
	note			varchar(240) not null default '',
	tarif_id		int unsigned not null default 0,
	paymenttype		enum('0','1','2','3','4','5','6','7','8') not null default '0',
	index 			index_sid (student_id),
	primary key  	(id)
) type=myisam;
ALTER TABLE fees_tarif CHANGE amount 
	  amount decimal(10,2) unsigned not null default '0';
ALTER TABLE fees_account ADD 
	accountname	varbinary(120) not null default '' AFTER guardian_id;
ALTER TABLE fees_charge ADD 
	remittance_id int unsigned not null default 0 AFTER invoice_id;
ALTER TABLE fees_charge ADD 
	community_id int unsigned not null default 0 AFTER tarif_id;
ALTER TABLE fees_charge ADD 
	amount decimal(10,2) unsigned not null default '0' AFTER paymentdate;
ALTER TABLE fees_remittance ADD 
	enrolstatus	char(3) not null default 'C' AFTER concepts;
ALTER TABLE gidsid ADD 
	paymenttype enum('0','1','2','3','4','5','6','7','8') not null default '0' AFTER responsibility;
ALTER TABLE fees_charge CHANGE paymenttype
	paymenttype enum('0','1','2','3','4','5','6','7','8') not null default '0';
ALTER TABLE fees_charge CHANGE payment
	payment	enum('0','1','2') not null default '0';
ALTER TABLE fees_concept ADD
	community_type varchar(60) not null default '' AFTER inactive;
