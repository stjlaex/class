ALTER TABLE assessment
 	ADD key indexcrid (course_id);
ALTER TABLE assessment
 	ADD key indexstage (stage);
ALTER TABLE assessment
 	ADD key indexyear (year);
DROP TABLE IF EXISTS ordercatalogue;
CREATE TABLE ordercatalogue (
	id				int unsigned not null auto_increment, 
	supplier_id		int unsigned not null default '0', 
	unitcost		decimal(10,2) unsigned not null default '0',
	currency		enum('0','1','2','3','4') not null,
	detail			text not null default '',
	refno			varchar(240) not null default '',
	isbn			varchar(240) not null default '',
	materialtype	int unsigned not null default '0',
	subject_id		varchar(10) not null default '',
	index			index_mat (materialtype,supplier_id),
   	primary key		(id)
) type=myisam;
ALTER TABLE ordermaterial
	ADD catalogue_id int unsigned not null default '0' AFTER invoice_id;
ALTER TABLE ordersupplier
	CHANGE specialaction specialaction enum('0','1','2') not null default '0';
DROP TABLE IF EXISTS fees_charge;
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
	index			index_sid (student_id),
	primary key  	(id)
) type=myisam;

