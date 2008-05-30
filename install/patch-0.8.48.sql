ALTER TABLE classes 
	ADD sp smallint unsigned not null default 0 AFTER many;
ALTER TABLE classes 
	ADD dp smallint unsigned not null default 0 AFTER sp;
ALTER TABLE classes 
	ADD block char(3) not null default '' AFTER dp;
ALTER TABLE component 
	ADD sequence smallint unsigned not null default '0' AFTER status;
ALTER TABLE component 
	CHANGE status status enum('N','V','U') not null default 'N';
ALTER TABLE orderaction 
	CHANGE action action enum('1','2','3','4','5','6') not null;
