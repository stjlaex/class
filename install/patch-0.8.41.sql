ALTER TABLE groups
	ADD	type enum('a','p','b','s') not null default 'a' AFTER name;
UPDATE groups SET type='p' WHERE course_id='' AND subject_id='';
ALTER TABLE section 
	ADD	sequence smallint unsigned not null default '0';
ALTER TABLE section 
	ADD	address_id int unsigned not null default '0';
ALTER TABLE section 
	CHANGE name name varchar(240) not null default '';
ALTER TABLE section 
	ADD gid int(10) NOT NULL default '0';
ALTER TABLE orderbudget 
	CHANGE section_id section_id smallint unsigned not null default '0';
ALTER TABLE ordersupplier 
	DROP street;
ALTER TABLE ordersupplier 
	DROP neighbourhood;
ALTER TABLE ordersupplier 
	DROP region;
ALTER TABLE ordersupplier 
	DROP postcode;
ALTER TABLE ordersupplier 
	DROP country;
ALTER TABLE ordersupplier 
	ADD	address_id int unsigned not null default '0';
ALTER TABLE orderaction 
	CHANGE action action enum('1','2','3','4','5') not null;
ALTER TABLE ordermaterial 
	ADD materialtype char(2) not null default '';
