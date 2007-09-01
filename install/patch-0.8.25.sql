UPDATE incidents SET closed='Y';
INSERT INTO categorydef (name, type, subtype, rating, rating_name, subject_id, course_id, section_id) VALUES 
	('Warning', 'inc', 'teacher', '0', 'none', '%', '%', '0'), 
	('Relocated in class', 'inc', 'teacher', '1', 'none', '%', '%', '0'),
	('Written work', 'inc', 'teacher', '2', 'none', '%', '%', '0'),
	('Detention with teacher', 'inc', 'teacher', '3', 'none', '%', '%', '0'),
	('Interview with tutor', 'inc', 'tutor', '3', 'none', '%', '%', '0'),
	('Interview with year coord.', 'inc', 'year', '3', 'none', '%', '%', '0'),
	('Interview with head of subject', 'inc', 'year', '3', 'none', '%', '%', '0'),
	('Weekly report', 'inc', 'section', '3', 'none', '%', '%', '0'),
	('Detention with head of subject', 'inc', 'teacher', '3', 'none', '%', '%', '0'),
	('Detention with year coord.', 'inc', 'year', '3', 'none', '%', '%', '0'),
	('Interview with head', 'inc', 'section', '3', 'none', '%', '%', '0'),
	('Formal school detention', 'inc', 'section', '4', 'none', '%', '%', '0'),
	('Isolation', 'inc', 'section', '5', 'none', '%', '%', '0'),
	('Internal suspension', 'inc', 'section', '5', 'none', '%', '%', '0'),
	('External suspension', 'inc', 'section', '5', 'none', '%', '%', '0'),
	('Interview with parents', 'inc', 'section', '5', 'none', '%', '%', '0'),
	('Contract', 'inc', 'section', '5', 'none', '%', '%', '0');
ALTER TABLE incidenthistory
	  CHANGE categorydef_id category varchar(30) not null default '';
