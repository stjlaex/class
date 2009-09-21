ALTER TABLE course 
	CHANGE section_id nextcourse_id	varchar(10) not null default '';
UPDATE course SET nextcourse_id='';
ALTER TABLE course DROP teacher_id;
