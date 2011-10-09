ALTER TABLE fees_charge 
	ADD paymentdate	date not null default '0000-00-00';
ALTER TABLE info 
	ADD passportdate date not null default '0000-00-00';
