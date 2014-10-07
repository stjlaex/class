ALTER TABLE event CHANGE period period ENUM('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','lunch') NOT NULL DEFAULT '0';

CREATE TABLE meals_attendance (
	booking_id		int unsigned not null,
	event_id			int unsigned not null,
	status			enum('a','p') not null default 'a',
	comment			text,
	logtime			timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	primary key		(booking_id,event_id)
) ENGINE=MYISAM;
