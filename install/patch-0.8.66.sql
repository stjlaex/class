ALTER TABLE mark
	  CHANGE assessment assessment enum('no','yes','other') not null default 'no';
ALTER TABLE ordermaterial
	  CHANGE materialtype materialtype int unsigned not null default '0';
UPDATE ordermaterial SET materialtype=0;
INSERT INTO categorydef (name, type, subtype, rating, rating_name, subject_id, course_id, section_id) VALUES 
	('Software', 'mat', '', '0', 'none', '%', '%', '0'), 
	('Books', 'mat', '', '0', 'none', '%', '%', '0'), 
	('Consumables', 'mat', '', '0', 'none', '%', '%', '0'), 
	('Materials', 'mat', '', '0', 'none', '%', '%', '0'), 
	('Other', 'mat', '', '0', 'none', '%', '%', '0');
