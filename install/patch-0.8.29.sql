UPDATE address SET building=CONCAT(building, streetno);
ALTER TABLE address DROP streetno;
ALTER TABLE users 
	ADD emailuser varchar(60) not null default '' AFTER email;
ALTER TABLE attendance 
	ADD logtime timestamp(14) AFTER teacher_id;

