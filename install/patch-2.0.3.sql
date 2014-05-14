CREATE TABLE api (
	id				smallint unsigned auto_increment,
	username			varchar(30) not null default '',
	device			varchar(130) not null default '',
	register_status	enum('0','1') not null default '0',
	register_timestamp	timestamp not null default CURRENT_TIMESTAMP,
	token			varchar(100) not null default '',
	last_use			timestamp,
	ip				varchar(15) binary not null default '', 
	expire			timestamp,
	primary key		(id)
) ENGINE=MYISAM;
