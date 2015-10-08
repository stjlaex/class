ALTER TABLE comidsid DROP PRIMARY KEY;
ALTER TABLE comidsid ADD memberid int(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY id(memberid);
ALTER TABLE comidsid ADD index indexmember (community_id, student_id);

ALTER TABLE mark ADD elgg_weblog_post_id INT;
ALTER TABLE fees_account ADD code VARBINARY(40) NOT NULL AFTER bic;

CREATE TABLE report_comments_length (
	id              int unsigned NOT NULL auto_increment,
        report_id   	int unsigned NOT NULL,
	subject_id	varchar(10) NOT NULL,
	comment_length	int unsigned NOT NULL,
        index           index_result(report_id),
        primary key     (id)
);
