.. _configuration:

=============
Configuration
=============

Target group: **Developers, Integrators**

.. contents::
   :local:

.. _include-typoscript-sets:

Include TypoScript sets
=======================

The extension ships some TypoScript code which needs to be included.

#. Switch to the root page of your site.

#. Switch to the :guilabel:`Site Management > TypoScript` and edit
   :guilabel:`Edit TypoScript Record` in the upper menu bar.

#. Press the link :guilabel:`Edit the whole template record` and switch to the
   tab :guilabel:`Advanced Options`.

#. Select :guilabel:`JobRouter Data` from the
   available items at the field :guilabel:`Include TypoScript sets:`

.. figure:: /Images/include-typoscript-sets.png
   :alt: Include static TypoScript

   Include static TypoScript

Or import the TypoScript setup file in your site package.

.. _configuration-content-element:

Content element
===============

It is possible to adjust the CSS classes of the :ref:`content element
<editor-content-element>` table via TypoScript:

.. code-block:: typoscript

   tt_content.tx_jobrouterdata_table {
      settings {
         cssClasses {
            # The class of the table tag
            table = ce-table

            # The class of table cells which should be aligned left
            left = ce-align-left

            # The class of table cells which should be aligned centered
            center = ce-align-center

            # The class of table cells which should be aligned right
            right = ce-align-right
         }
      }
   }

The alignment is selected when configuring the :ref:`table columns
<module-create-table-link-simple>`.


.. _configuration-logging:

Logging
=======

If separate logging is necessary to track synchronisations and possible warnings
or errors, you can set up :ref:`log writers <t3coreapi:logging-writers>`
depending on your needs.

**Example:** To log all warnings and higher levels of this extension into a
separate file, add this snippet to the :file:`ext_localconf.php` file of your
site package extension:

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['LOG']['JobRouter']['Typo3Data']['writerConfiguration'][\Psr\Log\Level::WARNING] = [
      \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
         'logFileInfix' => 'jobrouter_data'
      ]
   ];

The messages are then written to the
:file:`var/log/typo3_jobrouter_data_<hash>.log` file.
