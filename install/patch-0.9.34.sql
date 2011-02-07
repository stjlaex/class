ALTER TABLE orderinvoice ADD exchange decimal(10,2) unsigned not null default '0' AFTER credit;
ALTER TABLE users ADD medrole enum('0','1') not null AFTER senrole;
ALTER TABLE info ADD siblings enum('N','Y') not null AFTER appdate;
