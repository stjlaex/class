ALTER TABLE categorydef CHANGE name name varchar(240) not null default '';
ALTER TABLE categorydef ADD stage char(3) not null default '' AFTER course_id;
