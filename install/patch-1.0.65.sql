ALTER TABLE report ADD addphotos enum('no', 'yes') NOT NULL AFTER addcategory;
ALTER TABLE report ADD attendancestartdate DATE NOT NULL AFTER date;
