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
