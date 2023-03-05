.. include:: _includes.rst.txt

.. _developer:

================
Developer corner
================

Target group: **Developers**

.. contents:: Table of Contents
   :depth: 2
   :local:


Modify data sets on synchronisation
===================================

The records that are synchronised with the table types
:ref:`module-create-table-link-simple` or :ref:`module-create-table-link-custom`
can be adapted to the needs of the website or rejected during synchronisation.
This can be useful if only records with a date column are used where the date is
relevant in the future. Another scenario would be to match the content of one
column with the content of another table.


Example
-------

A :ref:`PSR-14 event listener <t3coreapi:EventDispatcher>` can be used for this
case. In the following example, a record is rejected under a certain condition
and a static string is added to each "position" column value.

.. rst-class:: bignums-xxl

#. Create the event listener

   .. code-block:: php
      :caption: EXT:my_extension/Classes/EventListener/AdjustJobsDataset.php

      <?php
      declare(strict_types=1);

      namespace MyVendor\MyExtension\EventListener;

      use Brotkrueml\JobRouterData\Event\ModifyDatasetOnSynchronisationEvent;

      final class AdjustJobsDataset
      {
         public function __invoke(ModifyDatasetOnSynchronisationEvent $event): void
         {
            // Only the table with the handle "jobs" should be considered
            if ($event->getTable()->handle !== 'jobs') {
               return;
            }

            $dataset = $event->getDataset();
            if ($dataset['jrid'] === 3) {
               // For some reason we don't like jrid = 3, so we reject it
               // and it doesn't get synchronised
               $event->setRejected();
            }

            $dataset['POSITION'] .= ' (approved by me™)';
            $event->setDataset($dataset);
         }
      }

#. Register your event listener

   .. code-block:: yaml
      :caption: EXT:my_extension/Configuration/Services.yaml

      services:
         MyVendor\MyExtension\EventListener\AdjustJobsDataset:
            tags:
               - name: event.listener
                 identifier: 'adjustJobsDataset'


Retrieve data sets from the different table link types
======================================================

.. _developer-simple-sync-table:

Simple synchronisation table
----------------------------

When a JobData table is synchronised with the :ref:`Simple synchronisation
<module-create-table-link-simple>` type, the data sets are stored in a table
provided by this extension. This is the simplest type, as no programming
knowledge is required. The data sets are stored JSON-encoded in a provided table
and can be displayed on the website with a :ref:`content element
<editor-content-element>`.

However, you can also retrieve the data sets independently.


Schema of the table `tx_jobrouterdata_domain_model_dataset`
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

========= ======================================================================
Column    Description
========= ======================================================================
uid       Unique ID of the data set (auto increment)
--------- ----------------------------------------------------------------------
table_uid Relation to a defined table link
--------- ----------------------------------------------------------------------
jrid      jrid of the Jobdata table data set
--------- ----------------------------------------------------------------------
dataset   JSON-encoded data set with the synchronised JobData table row
========= ======================================================================


Get the data sets of a table link programmatically
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

A repository and an entity class are available which can be used in a
TYPO3 context:

.. code-block:: php
   :caption: EXT:my_extension/Classes/MyClass.php

   <?php
   declare(strict_types = 1);

   namespace MyVendor\MyExtension;

   use Brotkrueml\JobRouterData\Domain\Entity\Dataset;
   use Brotkrueml\JobRouterData\Domain\Repository\DatasetRepository;

   final class MyClass
   {
      public function __construct(
         private readonly DatasetRepository $datasetRepository,
      ) {
      }

      public function doSomething(): void
      {
         /** @var Dataset[] $datasets */
         $datasets = $this->datasetRepository->findByTableUid(1);

         foreach ($datasets as $dataset) {
            // Show the jrid
            var_dump($dataset->jrid);

            // Get the content of the column "TRAINING"
            var_dump($dataset->dataset('TRAINING'));
         }
      }
   }


.. _developer-custom-table:

Custom table
------------

Synchronising a JobData table into a :ref:`custom table
<module-create-table-link-custom>` has some advantages and disadvantages
compared to the simple synchronisation type described above:

-  Flexibility: You can filter the content of a synchronised table with specific
   SQL queries, because all JobData columns are stored in separate columns in
   the TYPO3 table.
-  Joining data: You can join the table with other tables in your domain.
-  Easy usage: e.g. in TCA select boxes against the simple synchronisation.
-  More Work: You have to implement the logic yourself.

But let's start:

#. Create a new extension or use an existing one. Consult the TYPO3 manual how
   to do this.

#. Add or append the table definition to the file :file:`ext_tables.sql`:

   .. code-block:: sql
      :caption: EXT:my_extension/ext_tables.sql

      CREATE TABLE tx_myextension_domain_model_jobs (
         uid int(11) unsigned NOT NULL AUTO_INCREMENT,
         jrid int(11) unsigned DEFAULT '0' NOT NULL,
         position varchar(255)  DEFAULT '' NOT NULL,
         active smallint(5) unsigned DEFAULT '0' NOT NULL,

         PRIMARY KEY (uid),
         UNIQUE KEY tableuid_jrid (table_uid, jrid),
         KEY table_uid (table_uid)
      );

   The table name must start with `tx_` to be recognised as an custom table in
   the module.

   It is recommended to add a primary key to the table. In this example it is
   calls `uid` to be in line with TYPO3.

   It must also have a column `jrid`. Add a unique or primary key for the `jrid`
   column.

   Add the columns to be synchronised from the JobData table (in the example
   `position` and `active` in this jobs example. The column type must be named
   as in the JobData table.

#. Go to the :guilabel:`Admin Tools > Maintenance` module, click on
   the :guilabel:`Analyse database` button and create the table.

#. Add a table link in the :ref:`backend module <module-create-table-link-custom>`.

This is the minimal setup to synchronise a JobData table into a custom TYPO3
table. How you use the table depends on your use case.


Other usage
-----------

Links to JobData tables are also centralised in the :guilabel:`JobRouter > Data`
module, in contrast to the definition in PHP code.

The table link type :ref:`Other usage <module-create-table-link-other>` can be
used to facilitate the access to a JobData table. Links to JobData tables are
also centralised in the :guilabel:`Data` module, in contrast to the definition
in PHP code.

Here is an example to get the table link and initialise the JobRouter Client:

.. code-block:: php
   :caption: EXT:my_extension/Classes/MyClass.php

   <?php
   declare(strict_types = 1);

   namespace MyVendor\MyExtension;

   use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
   use Brotkrueml\JobRouterConnector\Domain\Entity\Connection;
   use Brotkrueml\JobRouterConnector\Domain\Repository\ConnectionRepository;
   use Brotkrueml\JobRouterConnector\Exception\ConnectionNotFoundException;
   use Brotkrueml\JobRouterData\Domain\Entity\Table;
   use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
   use Brotkrueml\JobRouterData\Exception\TableNotFoundException;

   public function MyClass
   {
      public function __construct(
         private readonly ConnectionRepository $connectionRepository,
         private readonly TableRepository $tableRepository,
      ) {
      }

      public function doSomething(): void
      {
         try {
            /** @var Table $table */
            $table = $this->tableRepository->findOneByHandle('contacts');

            /** @var Connection $connection */
            $connection = $this->connectionRepository->findByUid($table->connectionUid);

            // Create a JobRouter Client RestClient object
            $client = (new RestClientFactory())->create($connection);
            $response = $restClient->request(
               'GET',
               sprintf('application/jobdata/tables/%s/datasets', $table->getTableGuid())
            );

            // Do something with the response ...
         } catch (TableNotFoundException|ConnectionNotFoundException) {
         }
      }
   }

Have a look into the :doc:`JobRouter Client <jobrouter-client:introduction>`
documentation how to use it. The library eases the access to the JobRouter® REST
API.


.. _developer-transfer-data-sets:

Transfer data sets to a JobRouter installation
==============================================

Sometimes it is necessary to transfer data sets from TYPO3 to a JobRouter®
installation. An API and a :ref:`transmit command
<transmit-command>` are available for this use case.

Data sets are transferred asynchronously, since a JobRouter® installation may be
unavailable or in maintenance mode and to avoid long page loads. Let's take a
look at the flow:

.. figure:: _images/transfer-flow.png
   :alt: Transferring data sets

   Transferring data sets

As you can see from the diagram, you can prepare multiple data sets. The
different data sets can be transmitted to different JobRouter® installations –
depending on the configuration of the table link in the
:ref:`Data module <module>`.


Preparing the data sets
-----------------------

If you want to transfer data sets programmatically to a JobRouter® installation,
you can use the :php:`Preparer` class within TYPO3, e.g. in an Extbase
controller:

.. code-block:: php
   :caption: EXT:my_extension/Classes/Controller/MyController.php

   <?php
   declare(strict_types=1);

   namespace MyVendor\MyExtension\Controller;

   use Brotkrueml\JobRouterData\Exception\PrepareException;
   use Brotkrueml\JobRouterData\Transfer\Preparer;
   use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
   use Psr\Http\Message\ResponseInterface;

   final class MyController extends ActionController
   {
      public function __construct(
         private readonly Preparer $preparer,
      ) {
      }

      public function myAction(): ResponseInterface
      {
         // ... some other code

         try {
            $this-preparer->store(
                // The table link uid
               42,
               // Some descriptive identifier for the source of the dataset
               'some identifier',
               // Your JSON encoded data set
               '{"your": "data", "to": "transfer"}'
            );
         } catch (PrepareException $e) {
            // In some rare cases an exception may be thrown
            var_dump($e->getMessage());
         }

         // ... some other code
      }

The :ref:`transmit command <transmit-command>` must be activated
with a cron job to periodically transmit the data sets to the JobRouter®
installation(s).

.. important::
   It is not advised to insert the data sets directly into the transfer table,
   as the table schema can be changed in future versions. Use the API described
   above.


Using the JobDataRepository
===========================

The :php:`\Brotkrueml\JobRouterData\Domain\Repository\JobRouter\JobDataRepository`
provides methods to access the JobData REST API in TYPO3, e.g. in a command or a
controller.

The following methods are available:

.. option:: add(string $tableHandle, array $dataset): array

   Adds a dataset to a JobData table and returns the stored dataset.

.. option:: remove(string $tableHandle, int ...$jrid): void

   Removes one or more datasets from a JobData table.

.. option:: update(string $tableHandle, int $jrid, array $dataset): array

   Updates the dataset with the given jrid for a JobData table and returns the
   stored dataset.

.. option:: findAll(string $tableHandle): array

   Returns all datasets of the JobData table;

.. option:: findByJrId(string $tableHandle, int $jrid): array

   Returns the dataset for the given jrid of a JobData table.


Example
-------

.. code-block:: php
   :caption: EXT:my_extension/Classes/Command/MyCommand.php

   <?php
   declare(strict_types=1);

   namespace MyVendor\MyExtension\Command;

   final class MyCommand extends Command
   {
      public function __construct(
         private readonly JobDataRepository $jobDataRepository,
      ) {
        parent::__construct();
      }

      protected function execute(InputInterface $input, OutputInterface $output): int
      {
         $datasets = $this->jobDataRepository->findAll('some_handle');

         // ... your logic

         return 0;
      }
   }


.. _customise-column-formatting:

Customising the formatting of a table column in the content element
===================================================================

The extension comes with four formatters that are used when rendering the
column content in the :ref:`content element <editor-content-element>`:

*  DateFormatter
*  DateTimeFormatter
*  DecimalFormatter
*  IntegerFormatter

These are implemented as :ref:`PSR-14 event listeners <t3coreapi:EventDispatcher>`
and are located in the :file:`Classes/EventListener` folder of this extension.
They receive a :php:`Brotkrueml\JobRouterData\Event\ModifyColumnContentEvent`
event with the following methods:

.. option:: getTable()

   The table entity.

   Return value
      The entity class of a table
      (:php:`Brotkrueml\JobRouterData\Domain\Entity\Table`).

.. option:: getColumn()

   The column entity.

   Return value
      The entity class of a column
      (:php:`Brotkrueml\JobRouterData\Domain\Entity\Column`).

.. option:: getContent()

   The content of a table cell.

   Return value
      The value of the content (types: float, int, string).

.. option:: setContent($content)

   Set a content for a table cell.

   Parameter
      :php:`float|int|string $content`
      The formatted content of a table cell.

.. option:: getLocale()

   The locale of the website page.

   Return value
      The locale (for example, "de-DE" or "en-US") is retrieved from the
      configured site language field `locale`.

      .. note::
         Using TYPO3 v11, the locale might be something like "de_DE.utf8" or
         "en_US". With the introduction of the :php:`Locale` class in TYPO3 v12
         the locale follows the `IETF RFC 5646 language tag standard`_.

.. _IETF RFC 5646 language tag standard: https://www.rfc-editor.org/rfc/rfc5646.html

Custom formatters
-----------------

As a PSR-14 event is dispatched for formatting a cell content, a custom
event listener can be used. Have a look into the existing formatter event
listeners.

.. note::
   Only the first suitable formatter is used. When the content is adjusted
   with the :php:`setContent()` method of the event the propagation of other
   events is stopped. So be sure to add your custom event listener before
   existing ones.
