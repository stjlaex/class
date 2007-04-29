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
	roomcategory		enum('N','Y') not null,
	invoice				enum('N','Y') not null,
	bookingdate			date null,
	level				char(12) not null default '',
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