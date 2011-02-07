<?php
/**								create_orderbook.php	
 */
	
mysql_query("
CREATE TABLE orderorder (
	id				int unsigned not null auto_increment, 
	budget_id		int unsigned not null default '0',
	supplier_id		int unsigned not null default '0',
	entrydate		date not null,
	ordertype	   	enum('0','1','2','3','4','5') not null,
	currency		enum('0','1','2','3','4') not null,
	teacher_id		varchar(14) not null default '',
	detail			text not null default '',
	primary key (id)
);");
mysql_query("
CREATE TABLE orderbudget (
	id				int unsigned not null auto_increment, 
	gid 			int(10) NOT NULL default '0',
	code			varchar(8) not null default '',
	yearcode	   	char(2) not null default '',
	name			varchar(160) not null default '',
	costlimit		decimal(10,2) unsigned not null default '0',
	section_id		smallint not null default 0,
	overbudget_id	int unsigned not null default '0',
	unique			indexbudget (code,yearcode),
	primary key (id)
);");
mysql_query("
CREATE TABLE ordersupplier (
	id				int unsigned not null auto_increment, 
	name			varchar(160) not null default '',
	phonenumber1	varchar(22) not null default '',
	phonenumber2	varchar(22) not null default '',
	phonenumber3	varchar(22) not null default '',
	email			varchar(240) not null default '',
	address_id		int unsigned not null default '0',
	specialaction	enum('0','1') not null default '0',
	inactive		enum('0','1') not null default '0',
	primary key (id)
);");
mysql_query("
CREATE TABLE orderaction (
	order_id		int unsigned not null default '0', 
	entryn			tinyint unsigned not null auto_increment,
	invoice_id		int unsigned not null default '0', 
	action			enum('1','2','3','4','5') not null,
	detail			text not null default '',
	teacher_id		varchar(14) not null default '',
	actiondate		date not null,
   	primary key		(order_id,entryn)
);");
mysql_query("
CREATE TABLE ordermaterial (
	order_id		int unsigned not null default '0', 
	entryn			tinyint unsigned not null auto_increment,
	quantity		smallint unsigned not null default '0',
	unitcost		decimal(10,2) unsigned not null default '0',
	detail			text not null default '',
	materialtype	int unsigned not null default '0',
   	primary key		(order_id,entryn)
);");
mysql_query("
CREATE TABLE orderinvoice (
	id				int unsigned not null auto_increment, 
	invoicedate		date not null,
	reference		varchar(40) not null default '',
	deliverycost	decimal(10,2) unsigned not null default '0',
	taxcost			decimal(10,2) unsigned not null default '0',
	discountcost	decimal(10,2) unsigned not null default '0',
	totalcost		decimal(10,2) unsigned not null default '0',
	debitcost		decimal(10,2) unsigned not null default '0',
	currency		enum('0','1','2','3','4') not null,
	debitcost		decimal(10,2) unsigned not null default '0',
	credit			tinyint(1) not null default '0',
	exchange		decimal(10,2) unsigned not null default '0'
   	primary key		(id)
);");
?>