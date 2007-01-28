ALTER TABLE exclusions
	CHANGE reason reason text not null default '';
ALTER TABLE medical
	CHANGE detail detail text not null default '';
ALTER TABLE incidents
	CHANGE detail detail text not null default '';
ALTER TABLE incidents
	CHANGE outcome outcome text not null default '';
ALTER TABLE comments
	CHANGE detail detail text not null default '';

