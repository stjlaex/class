CREATE TABLE report_comments_length (
	id              int unsigned NOT NULL auto_increment,
    report_id   	int unsigned NOT NULL,
	subject_id		varchar(10) NOT NULL,
	component_id	varchar(10) NOT NULL,
	comment_length	int unsigned NOT NULL,
    index           index_result(report_id),
    primary key     (id)
);
