ALTER TABLE class DROP primary KEY;
ALTER TABLE class CHANGE id
	  name varchar(20) not null default '';
ALTER TABLE class ADD 
	  id int unsigned not null auto_increment KEY FIRST;
ALTER TABLE class ADD 
	  cohort_id int unsigned not null;
UPDATE class, cohort 
	   SET class.cohort_id=cohort.id 
	   WHERE cohort.stage=class.stage AND cohort.course_id=class.course_id AND cohort.year='2012';
UPDATE tidcid, class 
	   SET tidcid.class_id=class.id 
	   WHERE class.name=tidcid.class_id;
UPDATE midcid, class 
	   SET midcid.class_id=class.id 
	   WHERE class.name=midcid.class_id;
UPDATE cidsid, class 
	   SET cidsid.class_id=class.id 
	   WHERE class.name=cidsid.class_id;
UPDATE attendance, class 
	   SET attendance.class_id=class.id 
	   WHERE class.name=attendance.class_id;
ALTER TABLE tidcid CHANGE class_id
	  class_id int unsigned not null;
ALTER TABLE midcid CHANGE class_id
	  class_id int unsigned not null;
ALTER TABLE cidsid CHANGE class_id
	  class_id int unsigned not null;
ALTER TABLE attendance CHANGE class_id
	  class_id int unsigned not null;