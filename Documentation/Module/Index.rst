.. _module:

======
Module
======

Target group: **Integrators, Administrators**

The links to JobData tables in JobRouter® installations are managed in the module
:guilabel:`JobRouter` > :guilabel:`Data`.

.. note::

   The module is only available in the live workspace.

On your first visit after installing the extension you will see the following
screen:

.. figure:: /Images/no-table-links-found.png
   :alt: Initial Data module screen

   Initial Data module screen


.. contents:: Table of Contents
   :depth: 3
   :local:

.. _module-create-table-link:

Create a table link
-------------------

To create a new table link, click the :guilabel:`+ Add table link` button on the
upper menu bar, which opens a form. Alternatively, you can use the
:guilabel:`Create new table link` button.


.. _module-create-table-link-types:

Types
~~~~~

Each table link has one of the following types:

.. figure:: /Images/table-link-types.png
   :alt: Table link types

   Table link types


.. _module-create-table-link-simple:

Simple synchronisation
~~~~~~~~~~~~~~~~~~~~~~

The data sets of the JobData table are synchronised in a table provided by this
extension. This is the recommended type if you only want to display data, for
example, with the :ref:`content element <editor-content-element>`. Have a look
at the developer corner to see the :ref:`schema <developer-simple-sync-table>`
of the table and how to use it in your code. The synchronisation is done with
the available :ref:`synchronisation command <sync-command>`.

.. note::
   The simple synchronisation should only be used for an overseeable number of
   data sets, especially when using the content element. The reason is that
   extracting and sorting data sets is done in PHP and not by the database.

.. figure:: /Images/create-table-link-simple-synchronisation.png
   :alt: Create a table link of type Simple synchronisation

   Create a table link of type "Simple synchronisation"

The following fields are available:

General
'''''''
.. include:: /_TableLinkColumns/connection.rst.txt
.. include:: /_TableLinkColumns/handle.rst.txt
.. include:: /_TableLinkColumns/name.rst.txt
.. include:: /_TableLinkColumns/jobdata-table-guid.rst.txt
.. include:: /_TableLinkColumns/columns-simple.rst.txt

Access
''''''
.. include:: /_TableLinkColumns/enabled.rst.txt

Status
''''''
.. include:: /_TableLinkColumns/last-sync-date.rst.txt
.. include:: /_TableLinkColumns/last-sync-error.rst.txt

Notes
'''''
.. include:: /_TableLinkColumns/description.rst.txt


.. _module-create-table-link-custom:

Synchronisation in custom table
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You have to define a table yourself in an extension with the needed columns
from the JobData table. This is the recommended way when you want to display the
data yourself, for example, with filtering by some columns or with joins to
other data. The synchronisation is carried out with the available
:ref:`synchronisation command <sync-command>`.

.. figure:: /Images/create-table-link-custom-table.png
   :alt: Create a table link of type Synchronisation in custom table

   Create a table link of type "Synchronisation in custom table"

The following fields are available:

General
'''''''
.. include:: /_TableLinkColumns/connection.rst.txt
.. include:: /_TableLinkColumns/handle.rst.txt
.. include:: /_TableLinkColumns/name.rst.txt
.. include:: /_TableLinkColumns/jobdata-table-guid.rst.txt
.. include:: /_TableLinkColumns/custom-table.rst.txt

Access
''''''
.. include:: /_TableLinkColumns/enabled.rst.txt

Status
''''''
.. include:: /_TableLinkColumns/last-sync-date.rst.txt
.. include:: /_TableLinkColumns/last-sync-error.rst.txt

Notes
'''''
.. include:: /_TableLinkColumns/description.rst.txt


.. _module-create-table-link-form-finisher:

Form finisher
~~~~~~~~~~~~~

The fields from a form are stored into a JobData table. An intermediate transfer
table is used, so you have to activate the :ref:`transmit command
<transmit-command>`.

.. figure:: /Images/create-table-link-form-finisher.png
   :alt: Create a table link of type Form finisher

   Create a table link of type "Form finisher"

The following fields are available:

General
'''''''
.. include:: /_TableLinkColumns/connection.rst.txt
.. include:: /_TableLinkColumns/handle.rst.txt
.. include:: /_TableLinkColumns/name.rst.txt
.. include:: /_TableLinkColumns/jobdata-table-guid.rst.txt
.. include:: /_TableLinkColumns/columns.rst.txt

Access
''''''
.. include:: /_TableLinkColumns/enabled.rst.txt

Status
''''''
.. include:: /_TableLinkColumns/last-sync-date.rst.txt
.. include:: /_TableLinkColumns/last-sync-error.rst.txt

Notes
'''''
.. include:: /_TableLinkColumns/description.rst.txt


.. _module-create-table-link-other:

Other usage
~~~~~~~~~~~

You only define the link to a JobData table – there is no automatic
synchronisation. This type can be used for the TYPO3 JobRouter Form extension to
push the field values of a submitted form into a JobData table. Also you can
synchronise data sets yourself and enrich the data with additional information.

.. figure:: /Images/create-table-link-other-usage.png
   :alt: Create a table link of type Other usage

   Create a table link of type "Other usage"

The following fields are available:

General
'''''''
.. include:: /_TableLinkColumns/connection.rst.txt
.. include:: /_TableLinkColumns/handle.rst.txt
.. include:: /_TableLinkColumns/name.rst.txt
.. include:: /_TableLinkColumns/jobdata-table-guid.rst.txt

Access
''''''
.. include:: /_TableLinkColumns/enabled.rst.txt

Notes
'''''
.. include:: /_TableLinkColumns/description.rst.txt


.. _module-table-links-overview:

Table links overview
--------------------

After you have created one or more table links, you will see an overview of the
table links when you open the module:

.. figure:: /Images/table-links-overview.png
   :alt: Overview of available table links

   Overview of available table links

If a table link is not enabled, this is indicated by the addition "(disabled)"
in the name.

There are three buttons available for each table link:

.. image:: /Images/table-link-buttons.png

- You can edit a table link with the pencil. Alternatively click on the name of
  the table to open the edit form.
- Click on the bug icon to test a table link connection.
- The last icon is a link to the JobData table definition in the JobRouter®
  installation. Alternatively click on the table GUID to open the JobData table
  definition.

The table link records are stored under the root page. You can edit a table link
also inside the :guilabel:`List` module.

.. _module-delete-table:

Delete a table link
-------------------

To delete a table link, open the edit page of the table link. In the upper
menu bar you will find the :guilabel:`delete` button.
