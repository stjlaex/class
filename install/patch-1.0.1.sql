ALTER TABLE ordermaterial ADD 
	  invoice_id int unsigned not null default '0' AFTER materialtype;
