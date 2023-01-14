CREATE TABLE tx_jobrouterdata_domain_model_table (
	type smallint(5) unsigned DEFAULT '0' NOT NULL,
	handle varchar(30) DEFAULT '' NOT NULL,
	name varchar(255)  DEFAULT '' NOT NULL,
	connection int(11) unsigned DEFAULT '0' NOT NULL,
	table_guid varchar(36) DEFAULT '' NOT NULL,
	custom_table varchar(100) DEFAULT '' NOT NULL,
	columns int(11) unsigned DEFAULT '0' NOT NULL,
	datasets int(11) unsigned DEFAULT '0' NOT NULL,
	datasets_sync_hash varchar(40) DEFAULT '' NOT NULL,
	last_sync_date int(11) unsigned DEFAULT '0' NOT NULL,
	last_sync_error text,
	description text,

	UNIQUE KEY handle (handle),
	KEY type (type)
);

CREATE TABLE tx_jobrouterdata_domain_model_column (
	table_uid int(11) unsigned DEFAULT '0' NOT NULL,
	name varchar(20) DEFAULT ''  NOT NULL,
	label varchar(255) DEFAULT ''  NOT NULL,
	type smallint(5) unsigned DEFAULT '0' NOT NULL,
	decimal_places smallint(5) unsigned DEFAULT '0' NOT NULL,
	field_size smallint(5) unsigned DEFAULT '0' NOT NULL,
	alignment varchar(10) DEFAULT ''  NOT NULL,
	sorting_priority smallint(5) unsigned DEFAULT '0' NOT NULL,
	sorting_order varchar(5) DEFAULT ''  NOT NULL,

	KEY table_uid (table_uid)
);

CREATE TABLE tx_jobrouterdata_domain_model_dataset (
	uid int(11) unsigned NOT NULL AUTO_INCREMENT,
	table_uid int(11) unsigned DEFAULT '0' NOT NULL,
	jrid int(11) unsigned DEFAULT '0' NOT NULL,
	dataset text,

	PRIMARY KEY (uid),
	UNIQUE KEY tableuid_jrid (table_uid, jrid),
	KEY table_uid (table_uid)
);

CREATE TABLE tx_jobrouterdata_domain_model_transfer (
	table_uid int(11) unsigned DEFAULT '0' NOT NULL,
	correlation_id VARCHAR(255) DEFAULT '' NOT NULL,
	data text,
	transmit_success tinyint(1) unsigned DEFAULT '0' NOT NULL,
	transmit_date int(11) unsigned DEFAULT '0' NOT NULL,
	transmit_message text,

	KEY transmit_success (transmit_success)
);
