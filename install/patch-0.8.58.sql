ALTER TABLE info 
	  ADD otherpn1 char(13) not null default '' AFTER formerupn;
ALTER TABLE info 
	  ADD otherpn2 char(13) not null default '' AFTER otherpn1;
