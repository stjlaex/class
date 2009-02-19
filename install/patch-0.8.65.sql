ALTER TABLE eidsid
	  CHANGE result result varchar(30) not null default '';
ALTER TABLE report
	  ADD rating_name varchar(30) not null default '';
UPDATE categorydef SET type='cat' WHERE type='rep';