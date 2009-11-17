DROP TABLE message_event;
CREATE TABLE message_event (
  id bigint(20) NOT NULL default '0',
  create_time datetime NOT NULL default '0000-00-00 00:00:00',
  time_to_send datetime NOT NULL default '0000-00-00 00:00:00',
  sent_time datetime default NULL,
  id_user bigint(20) NOT NULL default '0',
  ip varchar(20) NOT NULL default 'unknown',
  sender varchar(50) NOT NULL default '',
  recipient text NOT NULL,
  headers text NOT NULL,
  body longtext NOT NULL,
  try_sent tinyint(4) NOT NULL default '0',
  delete_after_send tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (id),
  KEY time_to_send (time_to_send),
  KEY id_user (id_user)
);
