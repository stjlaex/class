ALTER TABLE stats ADD
	date			date not null default '0000-00-00';
ALTER TABLE stats ADD
	profile_name	varchar(60) not null default '';
ALTER TABLE statvalues ADD
    value1		 	float not null default '0.0';
ALTER TABLE statvalues ADD
    value2		 	float not null default '0.0';
ALTER TABLE statvalues ADD
    value3		 	float not null default '0.0';
ALTER TABLE statvalues ADD
    value4		 	float not null default '0.0';
ALTER TABLE course 
	CHANGE section_id nextcourse_id	varchar(10) not null default '';
UPDATE course SET nextcourse_id='';
ALTER TABLE course DROP teacher_id;
