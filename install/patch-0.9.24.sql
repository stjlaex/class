ALTER TABLE component 
	  ADD weight smallint unsigned not null default '1' AFTER sequence;
INSERT INTO component (id,course_id,subject_id,status,sequence,weight) SELECT '',course_id,subject_id,'V','0','1' FROM cridbid;
DROP TABLE cridbid;