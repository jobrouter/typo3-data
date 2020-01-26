CREATE TABLE tx_jobrouterdata_domain_model_table (
	type smallint(5) unsigned DEFAULT '0' NOT NULL,
	name varchar(255)  DEFAULT '' NOT NULL,
	connection int(11) unsigned DEFAULT '0' NOT NULL,
	table_guid varchar(36) DEFAULT '' NOT NULL,
	own_table varchar(100) DEFAULT '' NOT NULL,
	columns int(11) unsigned DEFAULT '0' NOT NULL,
	datasets int(11) unsigned DEFAULT '0' NOT NULL,
	datasets_sync_hash varchar(40) DEFAULT '' NOT NULL
);

CREATE TABLE tx_jobrouterdata_domain_model_column (
	table_uid int(11) unsigned DEFAULT '0' NOT NULL,
	name varchar(20) DEFAULT ''  NOT NULL,
	label varchar(255) DEFAULT ''  NOT NULL,
	type smallint(5) unsigned DEFAULT '0' NOT NULL,
	decimal_places smallint(5) unsigned DEFAULT '0' NOT NULL,

	KEY table_uid (table_uid)
);

CREATE TABLE tx_jobrouterdata_domain_model_dataset (
	table_uid int(11) unsigned DEFAULT '0' NOT NULL,
	jrid int(11) unsigned DEFAULT '0' NOT NULL,
	dataset text,

	UNIQUE KEY tableuid_jrid (table_uid, jrid),
	KEY table_uid (table_uid)
);

CREATE TABLE tx_jobrouterdata_domain_model_transfer (
	table_uid int(11) unsigned DEFAULT '0' NOT NULL,
	identifier VARCHAR(255) DEFAULT '' NOT NULL,
	data text,
	transmit_success tinyint(1) unsigned DEFAULT '0' NOT NULL,
	transmit_date int(11) unsigned DEFAULT '0' NOT NULL,
	transmit_message text,

	KEY transmit_success (transmit_success)
);

CREATE TABLE tx_jobrouterdata_log (
	uid int(11) unsigned NOT NULL AUTO_INCREMENT,
	request_id varchar(13) DEFAULT '' NOT NULL,
	time_micro double(16,4) NOT NULL default '0.0000',
	component varchar(255) DEFAULT '' NOT NULL,
	level tinyint(1) unsigned DEFAULT '0' NOT NULL,
	message text,
	data text,

	PRIMARY KEY (uid),
	KEY request (request_id)
);
