ALTER TABLE address ADD lat DECIMAL(10,6) not null AFTER country;
ALTER TABLE address ADD lng DECIMAL(10,6) not null AFTER lat;