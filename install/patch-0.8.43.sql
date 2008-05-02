ALTER TABLE guardian 
	ADD note text not null default '' AFTER companyname;
ALTER TABLE ordersupplier 
	ADD inactive enum('0','1') not null default '0' AFTER address_id;
ALTER TABLE orderbudget 
	ADD overbudget_id int unsigned not null default '0' AFTER section_id;
ALTER TABLE sentypes RENAME AS sentype;
ALTER TABLE sentype
	DROP primary key;
ALTER TABLE sentype
 	ADD entryn smallint unsigned not null AFTER student_id;
ALTER TABLE sentype
 	ADD primary key (student_id,entryn);
ALTER TABLE sentype
 	CHANGE entryn entryn smallint unsigned not null auto_increment;
ALTER TABLE sentype
 	CHANGE sentype sentype char(4) not null default '';
