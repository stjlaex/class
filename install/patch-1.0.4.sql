ALTER TABLE report ADD 
	  nextsubject_id varchar(10) not null default '' AFTER year;
ALTER TABLE attendance ADD 
	  class_id varchar(10) not null default '' AFTER teacher_id;
