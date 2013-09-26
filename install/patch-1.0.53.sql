ALTER TABLE transport_stop CHANGE name name varchar(120) not null default '';
ALTER TABLE update_event ADD import TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  		ADD ip VARCHAR( 15 ) NOT NULL DEFAULT '000.000.000.000';
