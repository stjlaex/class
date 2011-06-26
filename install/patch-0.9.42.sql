CREATE TABLE event_notice (
		id			int unsigned not null auto_increment,
		date		date not null default '0000-00-00',
		session		enum('AM','PM') not null default 'AM',
	    comment		text,
		primary key (id)
) type=myisam;
CREATE TABLE event_notidcomid (
		notice_id		int unsigned not null default '0',
		community_id	int unsigned not null default '0',
		inactive		enum('0','1') not null default '0',
		primary key (notice_id,community_id)
) type=myisam;
ALTER TABLE gidsid
	  CHANGE relationship relationship enum('NOT','CAR','DOC','FAM','OTH', 
				'PAM','PAF','STP','REL','SWR','HFA','AGN','GRM','GRF','TUT') not null;
