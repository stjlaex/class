CREATE TABLE file (
 id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 owner enum('s','g','u') NOT NULL,
 owner_id int(10) UNSIGNED NOT NULL DEFAULT '0',
 folder_id int(10) UNSIGNED NOT NULL DEFAULT '0',
 title varchar(255) NOT NULL DEFAULT '',
 originalname varchar(255) NOT NULL DEFAULT '',
 description varchar(255) NOT NULL DEFAULT '',
 location varchar(255) NOT NULL DEFAULT '',
 access varchar(20) NOT NULL DEFAULT '',
 size int(11) NOT NULL DEFAULT '0',
 PRIMARY KEY (id),
 KEY fileowner (owner,owner_id,folder_id)
 );

CREATE TABLE file_folder (
 id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 owner enum('s','g','u') NOT NULL,
 owner_id int(10) UNSIGNED NOT NULL DEFAULT '0',
 parent_folder_id int(10) UNSIGNED NOT NULL DEFAULT '0',
 name varchar(128) NOT NULL DEFAULT '',
 access varchar(20) NOT NULL DEFAULT '',
 PRIMARY KEY (id),
 KEY folderowner (owner,owner_id),
 KEY name (name)
);
