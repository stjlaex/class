ALTER TABLE community CHANGE type type ENUM('','academic','family','form','year','tutor','alumni','enquired','applied','accepted','trip','reg','transport','extra','house','accomodation','new','transfer') NOT NULL DEFAULT '';
ALTER TABLE report  ADD type ENUM('wrapper','subject','profile') NOT NULL DEFAULT 'wrapper';
