ALTER TABLE orderinvoice
	ADD credit tinyint(1) not null default '0' AFTER currency;
ALTER TABLE attendance
	CHANGE code code char(2) not null default '';
