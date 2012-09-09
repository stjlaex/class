ALTER TABLE users ADD 
	  education varchar(240) not null default '' AFTER contractdate;
ALTER TABLE users ADD 
	  education2 varchar(240) not null default '' AFTER education;
ALTER TABLE component ADD
	  year year not null default '0000';
UPDATE component SET year='2013';
