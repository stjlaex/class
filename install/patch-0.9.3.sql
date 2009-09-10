CREATE TABLE merits (
	id				int unsigned not null auto_increment, 
	student_id		int unsigned not null,
	date			date not null default '0000-00-00',
	year			year not null default '0000',
	activity		varchar(100) not null default '',
	detail			text not null default '',
	result			varchar(30) not null default '',
    value		 	smallint not null default '0',
	teacher_id		varchar(14) not null default '',
	subject_id		varchar(10) not null default '',
	component_id	varchar(10) not null default '',
	index			index_result(student_id),
   	primary key		(id)
);
ALTER TABLE community 
	  CHANGE type type enum('','academic','family','form','year','tutor','alumni','enquired','applied','accepted','trip','reg','stop','extra','house','accomodation') not null default '';
