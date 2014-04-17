ALTER TABLE rating  ADD test INT NOT NULL DEFAULT '0';

ALTER TABLE history ADD classis_version varchar(10) NOT NULL,
	ADD browser_version varchar(150) NOT NULL,
	ADD ip varchar(15) NOT NULL DEFAULT '',
	ADD INDEX(uid),
	ADD INDEX(classis_version);
