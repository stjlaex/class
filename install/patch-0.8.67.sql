ALTER TABLE groups
	CHANGE type type enum('a','p','b','s','u') not null default 'a' AFTER name;
INSERT INTO groups (name,type) VALUES ('admin','u');
ALTER TABLE ordersupplier 
	  ADD email varchar(240) not null default '' AFTER phonenumber3;
