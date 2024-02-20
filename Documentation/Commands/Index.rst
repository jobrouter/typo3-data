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
available. Run the following command in the project directory for a Composer
installation:

.. code-block:: shell

   vendor/bin/typo3 jobrouter:data:sync

In a non-Composer installation execute:

.. code-block:: shell

   php public/typo3/sysext/core/bin/typo3 jobrouter:data:sync

Hopefully you will receive a successful response:

.. code-block:: plaintext

   [OK] 2 table(s) processed

.. note::
   By default, only tables with changed datasets are really synchronised. The
   result only says that - in the example - two tables were considered for
   synchronization.

You can also synchronise just one table:

.. code-block:: shell

   vendor/bin/typo3 jobrouter:data:sync jobs

Where `jobs` is the handle of the table.

It is also possible to force the synchronisation of one or all tables. By
default, only changed datasets are synchronised. Use the force option:

.. code-block:: shell

   vendor/bin/typo3 jobrouter:data:sync --force

If an error occurs, the command issues a warning:

.. code-block:: plaintext

   [WARNING] 1 out of 2 table(s) had errors during processing

Other synchronisations are not affected by an error in one synchronisation.
According to your :ref:`logging configuration <configuration-logging>`, the
error is also logged.

.. note::
   Only one synchronisation command can run at a time. If a synchronisation
   starts while another is in progress, the second synchronisation is terminated
   and a warning is displayed.

The last run of the command is shown in the system information toolbar
(:guilabel:`Last Data Sync.`):

.. figure:: /Images/system-information-sync.png
   :alt: System information with last run of the sync command

   System information with last run of the sync command


.. _transmit-command:

Transmit data sets
==================

If you use the :ref:`transfer table <developer-transfer-data-sets>` to transmit
JobData data sets to a JobRouter® installation must also use the transmit
command from the project directory for a composer installation:

.. code-block:: shell

   vendor/bin/typo3 jobrouter:data:transmit

In a non-composer installation execute:

.. code-block:: shell

   php public/typo3/sysext/core/bin/typo3 jobrouter:data:transmit

In general you should receive a successful answer:

.. code-block:: plaintext

   [OK] 13 transfer(s) transmitted successfully

If an error occurs, the command issues a warning:

.. code-block:: plaintext

   [WARNING] 2 out of 6 transfer(s) had errors on transmission

Other transmissions are not affected by an error in one transmission. According
to your :ref:`logging configuration <configuration-logging>`, the error is
also logged.

.. note::
   Only one transmission can run at a time. If a transmission starts while
   another is in progress, the second transmission is terminated and a warning
   is displayed.

The last run of the command is shown in the system information toolbar
(:guilabel:`Last Data Transmiss.`):

.. figure:: /Images/system-information-transmit.png
   :alt: System information with last run of the transmit command

   System information with last run of the transmit command


.. _deleteoldtransfers-command:

Clean up transfers
==================

After successfully transmitting data sets from the transfer table, these
transfers are marked as successful. They may contain sensitive data and should
be deleted regularly. A command is available for this task:

.. code-block:: shell

   vendor/bin/typo3 jobrouter:data:cleanuptransfers

In a non-Composer installation execute:

.. code-block:: shell

   php public/typo3/sysext/core/bin/typo3 jobrouter:data:cleanuptransfers

In general you should receive a successful answer:

.. code-block:: plaintext

   [OK] 23 successful transfers older than 30 days deleted

By default, successful transfer records that are older than 30 days are deleted.
You can adjust this value by adding an argument to the command:

.. code-block:: shell

   vendor/bin/typo3 jobrouter:data:cleanuptransfers 7

Now successful transfer records that are older than seven days are deleted. If
you use `0` as argument, all successful transfers are deleted.

.. important::
   Erroneous transfers are not deleted and must be handled manually.
