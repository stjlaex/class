ALTER TABLE community
	ADD count smallint unsigned not null default 0 AFTER capacity;
