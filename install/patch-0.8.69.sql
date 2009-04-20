ALTER TABLE event
	CHANGE period session enum('AM','PM') not null default 'AM';
ALTER TABLE event
	ADD period enum('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14') not null default '0';
ALTER TABLE event
	DROP key indexcom;
ALTER TABLE event
 	ADD unique key indexeve (date,session,period);
