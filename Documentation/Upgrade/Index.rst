.. include:: /Includes.rst.txt

.. _upgrade:

=======
Upgrade
=======

Target group: **Developers**


From version 2.0 to 3.0
=======================

The namespace of the JobRouter TYPO3 Data classes have changed from

.. code-block:: text

   \Brotkrueml\JobRouterData

to

.. code-block:: text

   \JobRouter\Addon\Typo3Data

The easiest way to update your code to the new namespace is to use
search/replace in your project.

The package name (used in :file:`composer.json`) has changed from
`brotkrueml/jobrouter-typo3-data` to `jobrouter/typo3-data`.

From version 1.x to version 2
=============================

Version 2 of this extension introduced some breaking changes, notably:

-  The repository classes are no longer based on Extbase. They are now using the
   connection object or the query builder provided by TYPO3 and Doctrine DBAL.

-  The Extbase model classes are gone. Instead there are now immutable entity
   classes for column, dataset, table and transfer under the namespace
   :php:`Brotkrueml\JobRouterData\Domain\Entity`. There are also no getters
   available anymore, instead just use the public properties (which are
   readonly).

-  The :php:`Dataset` entity does not provide a
   :php:`getDatasetContentForColumn()` method as the previous Extbase model.
   Instead the dataset property now always holds an array of the JSON-decoded
   dataset.

-  The :php:`JobDataRepository` is now injectable via constructor. All method
   signatures have changed and now require the table handle as first argument.

-  The :php:`getLocale()` method of the PSR-14 event
   :ref:`ModifyColumnContentEvent <customise-column-formatting>` returns always
   a `IETF RFC 5646`_ compatible locale string in TYPO3 v12.


.. _IETF RFC 5646: https://www.rfc-editor.org/rfc/rfc5646.html
