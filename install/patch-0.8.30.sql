ALTER TABLE mark DROP visible;
ALTER TABLE mark DROP hidden;
ALTER TABLE mark
	CHANGE marktype marktype enum('score', 'sum', 'average', 'level', 
						'dif', 'compound', 'report', 'hw') not null;
ALTER TABLE grading CHANGE
	grades grades text NOT NULL DEFAULT '';
ALTER TABLE class CHANGE
	details detail varchar(240) not null default '';
ALTER TABLE address CHANGE
	street street varchar(160) not null default '';
UPDATE address SET street=CONCAT(building, ' ', street);
ALTER TABLE address DROP building;
ALTER TABLE address CHANGE
	neighbourhood neighbourhood varchar(160) not null default '';
ALTER TABLE address CHANGE
	town region varchar(160) not null default '';
UPDATE address SET region=CONCAT(region, ' ', county);
ALTER TABLE address DROP county;
ALTER TABLE info
	ADD secondnationality char(2) not null default '' AFTER nationality;
