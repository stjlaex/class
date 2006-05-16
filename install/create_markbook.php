<?php 

/* Three predefined marktypes: level, sum and average which are
secondary calculated values based on the values of marks pointed to by
the mids in mid_list.  Or a compound marktype which associates several
marks in one column All other marktypes (indicated by marktype=score)
are user-defined and are given their definition in the table:markdef
and their values in the table:score.  */

mysql_query("CREATE TABLE mark ( 
	id int unsigned not null auto_increment, 
	entrydate date not null default '0000-00-00', 
	marktype enum('score', 'sum', 'average', 'level', 'compound', 'report') not null, 
	topic varchar(60) not null default '', 
	comment varchar(100) not null default '', 
	def_name varchar(20) not null default '', 
	midlist text, 
	levelling_name varchar(20) not null default '', 
	total smallint unsigned not null default '0', 
	assessment enum('no','yes') not null, 
	hidden enum('no','yes') not null, 
	visible varchar(200) not null default '',
	author varchar(14) not null default '', 
	component_id varchar(10) not null default '', 
	primary key (id) 
);");

/*
	marktype is the definition of a mark. The values in table:score
	can be any one of scoretype=(comment, value, grade).  Any one of
	those could be given a tier=tier as well. Alternatively the score
	might only be an explicit tier without any value indicated by
	scoretype=tier.  A scoretype=percentage indicates a raw numerical
	score.value and an score.outoftotal are to be used to generate a
	rounded percentage in score.grade.
*/

mysql_query("
CREATE TABLE markdef (
       name				varchar(20) not null default '',
       scoretype		enum('comment','value','grade','percentage','tier') not null
												default 'value',
       tier				enum('none','tier') not null,
		outoftotal		smallint unsigned not null default '0',
		grading_name	varchar(20) not null default '',
		comment			text,
		 course_id		varchar(10) not null default '',
		 subject_id		varchar(10) not null default '',
		 author			varchar(14) not null default '',
		index			index_bid (subject_id),
       primary key	(course_id, name)
);");

mysql_query("
CREATE TABLE score (
       mark_id			int unsigned not null default '0',
       student_id		int unsigned not null default '0',	 		
       grade			smallint default null,
       value		 	float default null,
       comment		 	text,
       tier		 		smallint unsigned not null default '0',
       outoftotal	  	smallint unsigned not null default '0',
       primary key	(mark_id, student_id)
);");

/*
	Provides user-defined level boundaries for the raw values stored in table:score
*/
mysql_query("
CREATE TABLE levelling (
       name				varchar(20) not null default '',
       levels			varchar(200) not null default '',
       grading_name		varchar(20) not null default '',
       comment			text,
		course_id		varchar(10) not null default '',
		subject_id		varchar(10) not null default '',
		author			varchar(14) not null default '',
		index			index_crid (course_id),
		index			index_bid (subject_id),
       primary key		(course_id, name)
);");

/*
	Provides user-defined grade names for the raw grades stored in table:score
*/
mysql_query("
CREATE TABLE grading (
       name				varchar(20) not null default '',
       grades			varchar(200) not null default '',
       comment			text,
		course_id		varchar(10) not null default '',
       subject_id		varchar(10) not null default '',
       author			varchar(14) not null default '',
		index			index_bid (subject_id),	
       primary key		(course_id, name)
);");


/*
	Lookup index providing marks for classes
*/
mysql_query("
CREATE TABLE midcid (
		 class_id		varchar(10) not null default '',
		 mark_id		int unsigned not null default '0',
		 primary key 	(class_id, mark_id)
);");
?>