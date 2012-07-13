ALTER TABLE users ADD 
	    personalemail	varchar(200) not null default '' AFTER nologin;
ALTER TABLE users ADD 
	    jobtitle		varchar(240) not null default '' AFTER nologin;
