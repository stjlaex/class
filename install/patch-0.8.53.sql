ALTER TABLE orderinvoice
	ADD credit tinyint(1) not null default '0' AFTER currency;
