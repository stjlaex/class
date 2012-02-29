ALTER TABLE event_notidcomid CHANGE inactive
		seen tinyint(1) unsigned not null default '0';
ALTER TABLE event_notidcomid ADD 
		yeargroup_id smallint AFTER community_id;
