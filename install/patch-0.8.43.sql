ALTER TABLE guardian 
	ADD note text not null default '' AFTER companyname;
ALTER TABLE ordersupplier 
	ADD inactive enum('0','1') not null default '0' AFTER address_id;
ALTER TABLE orderbudget 
	ADD overbudget_id int unsigned not null default '0' AFTER section_id;
