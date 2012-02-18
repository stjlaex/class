ALTER TABLE event_notidcomid CHANGE inactive
		seen tinyint(1) unsigned not null default '0';
