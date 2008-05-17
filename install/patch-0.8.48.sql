ALTER TABLE classes 
	ADD sp smallint unsigned not null default 0 AFTER many;
ALTER TABLE classes 
	ADD dp smallint unsigned not null default 0 AFTER sp;
ALTER TABLE classes 
	ADD block char(3) not null default '' AFTER dp;
