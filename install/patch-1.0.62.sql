ALTER TABLE fees_account ADD iban VARBINARY(50) NOT NULL AFTER banknumber;
ALTER TABLE fees_account ADD bic VARBINARY(40) NOT NULL AFTER iban;

