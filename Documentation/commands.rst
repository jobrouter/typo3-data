.. include:: _includes.txt

.. highlight:: shell

.. _commands:

========
Commands
========

Target group: **Administrators**

Surely you want to execute the commands regularly. Simply set up cron jobs that
will execute the commands regularly, e.g. once an hour or once a day, depending
on your needs.

.. contents::
   :depth: 1
   :local:

.. _sync-command:

Synchronise tables
==================

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

If an error occurs, the command issues a warning:

::

   [WARNING] 1 out of 2 table(s) had errors on synchronisation

Other synchronisations are not affected by an error in one synchronisation.
According to your :ref:`logging configuration <configuration-extension>`, the
error is also logged.

.. note::
   Only one synchronisation can run at a time. If a synchronisation starts while
   another is in progress, the second synchronisation is terminated and a
   warning is displayed.

The last run of the command is shown in the system information toolbar
(:guilabel:`Last Data Sync.`):

.. figure:: _images/system-information-sync.png
   :alt: System information with last run of the sync command

   System information with last run of the sync command


.. _transmit-command:

Transmit Data Sets
==================

If you use the :ref:`transfer table <developer-transfer-data-sets>` to transmit
JobData data sets to a JobRouter installation must also use the transmit
command:

::

   vendor/bin/typo3 jobrouter:data:transmit

In general you should receive a successful answer:

::

   [OK] 13 transfer(s) transmitted successfully

If an error occurs, the command issues a warning:

::

   [WARNING] 2 out of 6 transfer(s) had errors on transmission

Other transmissions are not affected by an error in one transmission. According
to your :ref:`logging configuration <configuration-extension>`, the error is
also logged.

.. note::
   Only one transmission can run at a time. If a transmission starts while
   another is in progress, the second transmission is terminated and a warning
   is displayed.

The last run of the command is shown in the system information toolbar
(:guilabel:`Last Data Transmiss.`):

.. figure:: _images/system-information-transmit.png
   :alt: System information with last run of the transmit command

   System information with last run of the transmit command


.. _deleteoldtransfers-command:

Clean Up Transfers
==================

After successfully transmitting data sets from the transfer table, these
transfers are marked as successful. They may contain sensitive data and should
be deleted regularly. A command is available for this task:

::

   vendor/bin/typo3 jobrouter:data:cleanuptransfers

In general you should receive a successful answer:

::

   [OK] 23 successful transfers older than 30 days deleted

By default, successful transfer records that are older than 30 days are deleted.
You can adjust this value by adding an argument to the command:

::

   vendor/bin/typo3 jobrouter:data:cleanuptransfers 7

Now successful transfer records that are older than seven days are deleted. If
you use `0` as argument, all successful transfers are deleted.

.. important::
   Erroneous transfers are not deleted and must be handled manually.

.. note::
   If there were deleted successful transfer records, the number of affected
   rows is logged as *notice*, if there were none it is logged as *info*.
