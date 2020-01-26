.. include:: _includes.txt

.. highlight:: shell

.. _configuration:

=============
Configuration
=============

Target group: **Administrators**


.. _configuration-extension:

Extension Configuration
=======================

To configure the extension, go to :guilabel:`Admin Tools` > :guilabel:`Settings`
> :guilabel:`Extension Configuration` and click on the :guilabel:`Configure
extensions` button. Open the :guilabel:`jobrouter_data` configuration:

.. figure:: _images/extension-configuration.png
   :alt: Options in the extension configuration

   Options in the extension configuration

log.logIntoFile
---------------

If this option is activated, the log output is written to the file
:file:`var/log/typo3_jobrouter_data_<hash>.log` (for Composer-based
installations). It is enabled by default.

log.logIntoTable
----------------

Activate this option to log into the table `tx_jobrouterdata_log`. It is
disabled by default.

.. hint::

   To see the log entries of this table in the TYPO3 backend, install the
   extension `vertexvaar/logs <https://github.com/vertexvaar/logs>`_.

log.logLevel
------------

Using the drop down menu you can select the log level for the activated log
options. :guilabel:`warning` is selected by default.


.. _configuration-commands:

Commands
========

.. _configuration-sync-command:

Synchronising tables
--------------------

To synchronise the tables from JobRouter installations in TYPO3 a command is
available. Run the following command in the project directory:

::

   vendor/bin/typo3 jobrouter:data:sync

Hopefully you will receive a successful response:

::

   [OK] 2 table(s) synchronised successfully

You can also synchronise just one table:

::

   vendor/bin/typo3 jobrouter:data:sync 1

Where `1` is the uid of the table.

Surely you want to execute the command regularly. Simply set up a cron job that
will execute the command regularly, e.g. once an hour or once a day, depending
on your needs.

If an error occurs, the command issues a warning. Other synchronisations are not
affected by an error in one synchronisation. According to your :ref:`logging
configuration <configuration-extension>`, the error is also logged.

