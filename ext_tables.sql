CREATE TABLE tx_jobrouterdata_domain_model_table
(
    name varchar(30)  DEFAULT '' NOT NULL,
    connection int(11) unsigned DEFAULT '0' NOT NULL,
    table_guid varchar(36) DEFAULT '' NOT NULL,
    columns int(11) unsigned DEFAULT '0' NOT NULL,

    UNIQUE KEY name (name),
);

CREATE TABLE tx_jobrouterdata_domain_model_column (
    parent int(11) unsigned DEFAULT '0' NOT NULL,
    name varchar(20) DEFAULT ''  NOT NULL,
    type smallint(5) unsigned DEFAULT '0' NOT NULL,
    label varchar(255) DEFAULT ''  NOT NULL,
);

CREATE TABLE tx_jobrouterdata_domain_model_dataset
(
    table_uid int(11) unsigned DEFAULT '0' NOT NULL,
    jrid int(11) unsigned DEFAULT '0' NOT NULL,
    dataset text,

    PRIMARY KEY (table_uid, jrid),
    KEY table_uid (table_uid),
);
