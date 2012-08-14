ALTER TABLE class DROP course_id, DROP stage;
ALTER TABLE classes ADD formgroup enum('N','Y') not null;
