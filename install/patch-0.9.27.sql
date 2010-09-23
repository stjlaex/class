ALTER TABLE community
	  ADD charge decimal(10,2) unsigned not null default '0' AFTER detail;
ALTER TABLE community
	  ADD chargetype enum('0','1','2') not null;
ALTER TABLE community
	  ADD sessions varchar(240) not null default '' AFTER chargetype;
ALTER TABLE community
	  ADD startdate date not null AFTER sessions;
ALTER TABLE community
	  ADD enddate date not null AFTER startdate;
UPDATE community SET sessions=detail WHERE type='tutor';