ALTER TABLE report ADD commentlength SMALLINT DEFAULT '0' NOT NULL AFTER addcomment;
ALTER TABLE report ADD commentcomp ENUM( 'no', 'yes' ) DEFAULT 'no' NOT NULL AFTER commentlength;