DROP TABLE medical;
INSERT INTO categorydef (name, type, subtype, rating, rating_name, subject_id, course_id, section_id) VALUES 
	('Allergies', 'med', 'md1', '0', 'none', '%', '%', '0'),
	('Special diet', 'med', 'md2', '0', 'none', '%', '%', '0'),
	('Regular medication', 'med', 'md3', '0', 'none', '%', '%', '0'),
	('Persistent problems', 'med', 'md4', '0', 'none', '%', '%', '0'),
	('Medical problems', 'med', 'md5', '0', 'none', '%', '%', '0');
