ALTER TABLE transport_rtidstid CHANGE traveltime
	  traveltime smallint unsigned not null default '0';
ALTER TABLE transport_stop CHANGE name
	  	name varchar(60) not null default '';
