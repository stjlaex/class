ALTER TABLE fees_applied ADD INDEX index_tarifid (tarif_id);

ALTER TABLE fees_charge ADD INDEX index_invoid (invoice_id), ADD INDEX index_remid (remittance_id), 
					ADD INDEX index_tarifid (tarif_id), ADD INDEX index_payment (payment), 
					ADD INDEX index_paymenttype (paymenttype);

ALTER TABLE fees_invoice ADD INDEX index_accountid (account_id), ADD INDEX index_remid (remittance_id);

ALTER TABLE fees_remittance ADD INDEX index_accountid (account_id), ADD INDEX index_year (year);

ALTER TABLE fees_tarif ADD INDEX index_conceptid (concept_id);
