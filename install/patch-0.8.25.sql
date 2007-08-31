UPDATE incidents SET closed='Y';
INSERT INTO categorydef (id , name, type, subtype, rating, 
	rating_name, subject_id, course_id, section_id) VALUES 
	(NULL , 'Warning', 'inc', 'teacher', '0', 'none', '%', '%', '0'), 
	(NULL , 'Relocated in class', 'inc', 'teacher', '1', 'none', '%', '%', '0'),
	(NULL , 'Written work', 'inc', 'teacher', '2', 'none', '%', '%', '0'),
	(NULL , 'Teacher detention', 'inc', 'teacher', '3', 'none', '%', '%', '0')
	(NULL , 'Interview with tutor', 'inc', 'tutor', '3', 'none', '%', '%', '0'),
	(NULL , 'Interview with year coord.', 'inc', 'year', '3', 'none', '%', '%', '0'),
	(NULL , 'Interview with head.', 'inc', 'section', '3', 'none', '%', '%', '0');
ALTER TABLE incidenthistory
	  CHANGE categorydef_id category varchar(30) not null default '';
