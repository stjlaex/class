ALTER TABLE event_notidcomid ADD 
		yeargroup_id smallint AFTER community_id;
ALTER TABLE ordercatalogue ADD 
		catalogue_id int unsigned not null default 0 AFTER supplier_id;
ALTER TABLE orderorder ADD 
		catalogue_id int unsigned not null default 0 AFTER supplier_id;
ALTER TABLE classes ADD
	  description text not null default '' AFTER block;