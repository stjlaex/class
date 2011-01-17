ALTER TABLE categorydef CHANGE rating rating smallint not null default '0';
UPDATE categorydef SET rating=rating-13;
UPDATE categorydef SET rating=0 WHERE rating='-13';
ALTER TABLE rating CHANGE value value smallint not null default '0';
UPDATE rating SET value=value-13;
ALTER TABLE categorydef ADD othertype varchar(20) not null default '';
ALTER TABLE subject CHANGE name name varchar(120) not null default '';
ALTER TABLE comidsid ADD special char(2) not null default '';
