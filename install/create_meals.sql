/**
 * Meals list is where are stored all meals (name,detail,day)
 * Meals booking is where the students are attached to the correspondant meal (startdate and enddate)
 */

CREATE TABLE meals_list (
	id				smallint unsigned auto_increment,
	name			varchar(30) not null default '',
	type			varchar(30) not null default '',
	detail			text not null default '',
	time			varchar(14) not null default '',
	day				enum('1','2','3','4','5','6','7','%') not null default '%',
	primary key		(id)
) ENGINE=MYISAM;

CREATE TABLE meals_booking (
	id				int unsigned not null auto_increment,
	student_id		int unsigned not null default '0',
	meal_id			int unsigned not null default '0',
	day				enum('1','2','3','4','5','6','7','%') not null default '%',
	startdate		date not null default '0000-00-00',
	enddate			date not null default '0000-00-00',
	index			indexsidmid (student_id,meal_id),
	comment			text,
	primary key		(id)
) ENGINE=MYISAM;
