CREATE TABLE tx_jobrouterdata_domain_model_table
(
    name varchar(30)  DEFAULT '' NOT NULL,
    connection int(11) unsigned DEFAULT '0' NOT NULL,
    table_guid varchar(36) DEFAULT '' NOT NULL,

    UNIQUE KEY name (name),
);

CREATE TABLE tx_jobrouterdata_domain_model_dataset
(
    table_uid int(11) unsigned DEFAULT '0' NOT NULL,
    jrid int(11) unsigned DEFAULT '0' NOT NULL,
    dataset text,

    PRIMARY KEY (table_uid, jrid),
    KEY table_uid (table_uid),
);
