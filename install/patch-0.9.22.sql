ALTER TABLE address ADD lat DECIMAL(10,6) not null AFTER country;
ALTER TABLE address ADD lng DECIMAL(10,6) not null AFTER lat;
ALTER TABLE community CHANGE type type enum('','academic','family','form','year','tutor','alumni','enquired','applied','accepted','trip','reg','transport','extra','house','accomodation') not null default '';
DROP TABLE transport;
DROP TABLE transportstop;

CREATE TABLE transport_bus (
	id				smallint unsigned auto_increment,
	name			varchar(30) not null default '',
	detail			text not null default '',
	route_id		smallint unsigned not null default 0,
	direction		enum('I','O') not null,
	day				enum('1','2','3','4','5','6','7','%') not null default '%',
	departuretime	time,
	teacher_id		varchar(14) not null default '',
	primary key		(id)
);

CREATE TABLE transport_route (
	id				smallint unsigned auto_increment,
	name			varchar(30) not null default '',
	detail			text not null default '',
	primary key  	(id)
);

CREATE TABLE transport_rtidstid (
	route_id		smallint unsigned not null default '0',
	stop_id			smallint unsigned not null default '0',
	sequence		smallint unsigned not null default '0',
	traveltime		time,
	primary key		(route_id, stop_id)
);

CREATE TABLE transport_stop (
	id				smallint unsigned auto_increment, 
	lat				decimal(10,6) not null,
	lng				decimal(10,6) not null,
	name			varchar(30) not null default '', 
	detail			text not null default '',
	primary key		(id)
);

CREATE TABLE transport_booking (
	id				int unsigned not null auto_increment,
	student_id		int unsigned not null default '0',
	journey_id		int unsigned not null default '0',
	direction		enum('I','O') not null,
	day				enum('1','2','3','4','5','6','7','%') not null default '%',
	status			enum('a','p') not null default 'p',
	startdate		date not null default '0000-00-00',
	enddate			date not null default '0000-00-00',
	comment			text,
	index			indexsidjid (student_id,journey_id),
	primary key		(id)
);

CREATE TABLE transport_journey (
	id				int unsigned not null auto_increment,
	bus_id			smallint unsigned not null default '0',
	stop_id			smallint unsigned not null default '0',
	cost			decimal(10,2) unsigned not null default '0',
	unique			indexjourney (bus_id,stop_id),
	primary key		(id)
);
