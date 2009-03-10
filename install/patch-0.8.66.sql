ALTER TABLE mark
	  CHANGE assessment assessment enum('no','yes','other') not null default 'no';
