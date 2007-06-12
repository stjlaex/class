UPDATE info SET nationality='TR' WHERE nationality='Turkish';
UPDATE info SET nationality='TR' WHERE nationality='Turkey';
UPDATE info SET nationality='JP' WHERE nationality='Japan';
UPDATE info SET nationality='RU' WHERE nationality='Russian';
UPDATE info SET nationality='RU' WHERE nationality='Russia';
UPDATE info SET nationality='GN' WHERE nationality='Guinea';
UPDATE info SET nationality='GN' WHERE nationality='Guinean';
UPDATE info SET nationality='VN' WHERE nationality='Vietnam';
UPDATE info SET nationality='CN' WHERE nationality='China';
UPDATE info SET nationality='CN' WHERE nationality='Chinese';
UPDATE info SET nationality='TW' WHERE nationality='Taiwan';
UPDATE info SET nationality='HK' WHERE nationality='Hong Kong';
UPDATE info SET nationality='FR' WHERE nationality='France';
UPDATE info SET nationality='TH' WHERE nationality='Thai';
UPDATE info SET nationality='NG' WHERE nationality='Nigeria';
UPDATE info SET nationality='IN' WHERE nationality='India';
UPDATE info SET nationality='MX' WHERE nationality='Mexico';
UPDATE info SET nationality='AZ' WHERE nationality='Azerbaijan';
UPDATE info SET nationality='DE' WHERE nationality='Germany';
UPDATE info SET nationality='BR' WHERE nationality='Brazil';
UPDATE info SET nationality='KZ' WHERE nationality='Kazakhstan';
UPDATE info SET nationality='HU' WHERE nationality='Hungary';
UPDATE info SET nationality='UA' WHERE nationality='Ukraine';
UPDATE info SET nationality='ES' WHERE nationality='Spanish';
UPDATE info SET nationality='KR' WHERE nationality='Korea';
UPDATE info SET nationality='MY' WHERE nationality='Malaysia';

UPDATE address SET country='TR', county='' WHERE county='Turkish';
UPDATE address SET country='TR', county='' WHERE county='Turkey';
UPDATE address SET country='JP', county=''  WHERE county='Japan';
UPDATE address SET country='RU', county=''  WHERE county='Russian';
UPDATE address SET country='RU', county=''  WHERE county='Russia';
UPDATE address SET country='GN', county=''  WHERE county='Guinea';
UPDATE address SET country='GN', county=''  WHERE county='Rep de Guinee';
UPDATE address SET country='GN', county=''  WHERE county='Guinean';
UPDATE address SET country='VN', county=''  WHERE county='Vietnam';
UPDATE address SET country='CN', county=''  WHERE county='China';
UPDATE address SET country='CN', county=''  WHERE county='CHINA';
UPDATE address SET country='CN', county=''  WHERE county='Chinese';
UPDATE address SET country='TW', county=''  WHERE county='Taiwan';
UPDATE address SET country='HK', county=''  WHERE county='Hong Kong';
UPDATE address SET country='FR', county=''  WHERE county='France';
UPDATE address SET country='TH', county=''  WHERE county='Thai';
UPDATE address SET country='TH', county=''  WHERE county='Thailand';
UPDATE address SET country='TH', county=''  WHERE county='THAILAND';
UPDATE address SET country='NG', county=''  WHERE county='Nigeria';
UPDATE address SET country='IN', county=''  WHERE county='India';
UPDATE address SET country='MX', county=''  WHERE county='Mexico';
UPDATE address SET country='AZ', county=''  WHERE county='Azerbaijan';
UPDATE address SET country='DE', county=''  WHERE county='Germany';
UPDATE address SET country='BR', county=''  WHERE county='Brazil';
UPDATE address SET country='KZ', county=''  WHERE county='Kazakhstan';
UPDATE address SET country='HU', county=''  WHERE county='Hungary';
UPDATE address SET country='UA', county=''  WHERE county='Ukraine';
UPDATE address SET country='ES', county=''  WHERE county='Spanish';
UPDATE address SET country='KR', county=''  WHERE county='Korea';
UPDATE address SET country='MY', county=''  WHERE county='Malaysia';
UPDATE address SET country='NL', county=''  WHERE county='Holland';
UPDATE address SET country='VE', county=''  WHERE county='Venezuela';

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
ALTER TABLE community
	CHANGE type type enum('','academic','family','form','year','tutor','alumni','enquired','applied','accepted','trip','reg','stop','extra','accomodation') not null default '';
ALTER TABLE community
	ADD capacity smallint unsigned not null default 0 AFTER type;
ALTER TABLE community
	ADD season enum('','S','W','M','1','2','3','4','5','6','7','8','9','a','b','c') NOT NULL DEFAULT '' AFTER type;
ALTER TABLE community
	ADD year year not null default '0000' AFTER type;
ALTER TABLE community
	DROP key indexcom;
ALTER TABLE community
 	ADD unique key indexcom (type,name,year,season);
UPDATE community SET name='AP:', year='2007' WHERE name='AP';
UPDATE community SET name='AC:', year='2007' WHERE name='AC';
UPDATE community SET name='EN:', year='2007' WHERE name='EN';
DROP table accomodation;
CREATE TABLE accomodation (
	id					int unsigned not null auto_increment,
	student_id			int unsigned not null, 
	community_id		int unsigned not null, 
	roomcategory		char(2) not null default '',
	building			char(2) not null default '',
	room				varchar(4) not null default '',
	bed					varchar(4) not null default '',
	invoice				varchar(80),
	bookingdate			date null,
	arrivaldate			date,
	arrivaltime			time,
	arrivalairport		varchar(240) not null default '',
	arrivalflight		varchar(240) not null default '',
	departuredate		date,
	departuretime		time,
	departureairport	varchar(240) not null default '',
	departureflight		varchar(240) not null default '',
	index				index_student (student_id),
   	primary key			(id)
);

CREATE TABLE derivation (
	resultid		int unsigned not null default '0',
	operandid		int unsigned not null default '0',
	type		    enum('A', 'M') default 'A' not null,
	element			char(3) not null default '',
	primary key 	(resultid, operandid, type)
);

ALTER TABLE mark
	CHANGE marktype	marktype enum('score', 'sum', 'average', 'level', 'dif', 'compound', 'report') not null;
