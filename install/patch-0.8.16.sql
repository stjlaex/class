ALTER TABLE guardian
	ADD companyname	varchar(240) NOT NULL default '' AFTER profession;
ALTER TABLE guardian
	CHANGE profession profession varchar(10) NOT NULL DEFAULT '';
ALTER TABLE guardian
	CHANGE nationality nationality char(2) NOT NULL DEFAULT '';
ALTER TABLE info
	CHANGE nationality nationality char(2) NOT NULL DEFAULT '';
ALTER TABLE info
	CHANGE countryoforigin countryoforigin char(2) NOT NULL DEFAULT '';
ALTER TABLE info
	CHANGE firstlanguage language char(4) not null default '';
ALTER TABLE info
	ADD languagetype enum('NOT','F','M','H','T','S','C') not null AFTER language;
ALTER TABLE info
	ADD birthplace varchar(240) NOT NULL default '' AFTER nationality;
ALTER TABLE info
	DROP transportroute;