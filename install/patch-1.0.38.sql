ALTER TABLE info 
	ADD language2 char(4) not null default '';
ALTER TABLE info
	ADD languagetype2 enum('NOT','F','M','H','T','S','C') not null AFTER language2;
ALTER TABLE info 
	ADD language3 char(4) not null default '' AFTER languagetype2;
ALTER TABLE info
	ADD languagetype3 enum('NOT','F','M','H','T','S','C') not null AFTER language3;
