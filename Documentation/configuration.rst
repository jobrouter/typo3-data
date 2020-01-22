.. include:: _includes.txt

.. highlight:: shell

.. _configuration:

=============
Configuration
=============

Target group: **Administrators**


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

   [OK] All tables synchronised successfully

You can also synchronise just one table:

::

   vendor/bin/typo3 jobrouter:data:sync 1

Where `1` is the uid of the table.

Surely you want to execute the command regularly. Just set up a cron job which
runs the command e.g. once an hour.

If an error occurs, it is logged in a file. Look in the log folder of your TYPO3
installation (for composer-based installations it is
:file:`var/log/typo3_jobrouter_data_<hash>.log`). By default only warnings and
errors are logged. However, if errors occur, it may be helpful to also display
the debug messages. Simply add this snippet to the :file:`ext_tables.php` file
in your extension to override the default:

.. code-block:: php

   <?php
   $GLOBALS['TYPO3_CONF_VARS']['LOG']['Brotkrueml']['JobRouterData']['writerConfiguration'] = [
      // This is the loglevel to use: DEBUG
      \TYPO3\CMS\Core\Log\LogLevel::DEBUG => [
         \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
            'logFileInfix' => $extensionKey,
         ],
      ],
   ];

Alternatively, you can simply change the :php:`WARNING` in the
:file:`ext_tables.php` file of the `jobrouter_data` extension to :php:`DEBUG`.
