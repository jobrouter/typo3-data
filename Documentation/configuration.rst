.. include:: _includes.rst.txt

.. highlight:: typoscript

.. _configuration:

=============
Configuration
=============

Target group: **Administrators**


.. _configuration-extension:

Extension configuration
=======================

To configure the extension, go to :guilabel:`Admin Tools` > :guilabel:`Settings`
> :guilabel:`Extension Configuration` and click on the :guilabel:`Configure
extensions` button. Open the :guilabel:`jobrouter_data` configuration:

.. figure:: _images/extension-configuration.png
   :alt: Options in the extension configuration

   Options in the extension configuration

Log
---

Log into file
~~~~~~~~~~~~~

If this option is activated, the log output is written to the file
:file:`var/log/typo3_jobrouter_data_<hash>.log` (for Composer-based
installations). It is enabled by default.

Log into table
~~~~~~~~~~~~~~

Activate this option to log into the table `tx_jobrouterbase_log`. It is
disabled by default.

.. hint::
   To display the log entries of this table in the TYPO3 backend, install the
   extension `co-stack/logs`_.

Log level
~~~~~~~~~

Using the drop down menu you can select the log level for the activated log
options. :guilabel:`warning` is selected by default.


.. _configuration-templates:

Templates
=========

It is possible to adjust the layout of the :ref:`content element
<editor-content-element>` table. By default the layout from the
core content element `Table` is used.

If you want to use other classes, you have to override the template. Just
copy the template file
:file:`Resources/Private/Template/ContentElement/Table.html` into your own
site package extension and add the path via TypoScript, e.g.::

   tt_content.tx_jobrouterdata_table {
      templateRootPaths.10 = EXT:your_extension/Resources/Private/Template/JobRouterData/
   }


.. _co-stack/logs: https://extensions.typo3.org/extension/logs
