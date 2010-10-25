ALTER TABLE gidsid
	  CHANGE relationship relationship enum('NOT','CAR','DOC','FAM','OTH', 
				'PAM','PAF','STP','REL','SWR','HFA','AGN','GRM','GRF') not null;
ALTER TABLE guardian ADD
	private	enum('N','Y') not null;
