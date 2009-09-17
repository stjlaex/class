ALTER TABLE report
	  ADD year year not null default '0000' AFTER rating_name;
UPDATE report SET year='2005' WHERE date>'2004-08-15';
UPDATE report SET year='2006' WHERE date>'2005-08-15';
UPDATE report SET year='2007' WHERE date>'2006-08-15';
UPDATE report SET year='2008' WHERE date>'2007-08-15';
UPDATE report SET year='2009' WHERE date>'2008-08-15';
UPDATE report SET year='2010' WHERE date>'2009-08-15';
