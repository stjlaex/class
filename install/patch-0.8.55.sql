ALTER TABLE community
	ADD count smallint unsigned not null default 0 AFTER capacity;
ALTER TABLE incidents
	ADD category varchar(30) not null default '' AFTER yeargroup_id;
