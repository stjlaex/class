UPDATE groups SET yeargroup_id='-9999' WHERE name='admin';
ALTER TABLE groups DROP name;
ALTER TABLE groups ADD
	community_id int(10) not null default '0' AFTER yeargroup_id;
ALTER TABLE groups CHANGE
	type type enum('a','p','b','s','u','c') not null default 'a';
INSERT INTO groups (community_id,type,yeargroup_id) SELECT community.id, 'p', form.yeargroup_id FROM community JOIN form ON community.name=form.name WHERE community.type='form';
UPDATE form, groups SET form.name=groups.gid WHERE groups.community_id=ANY(SELECT community.id FROM community WHERE community.type='form' AND community.name=form.id);
INSERT INTO perms (uid,gid,r,w,x,e) SELECT users.uid,form.name,'1','1','1','1' FROM users JOIN form ON users.username=form.teacher_id;
DROP table form;