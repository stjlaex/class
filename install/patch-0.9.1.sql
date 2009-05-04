ALTER TABLE component 
	CHANGE status status enum('N','V','O','U') not null default 'N';
ALTER TABLE assessment  
	CHANGE component_status component_status enum('None','N','V','O','A') NOT NULL DEFAULT 'None';
ALTER TABLE assessment  
	CHANGE strand_status strand_status enum('None','N','V','O','A') NOT NULL DEFAULT 'None';
