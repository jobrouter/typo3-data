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

.. versionadded:: 1.0.0

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

   ::

      <?php
         declare(strict_types=1);

         namespace YourVendor\YourExtension\EventListener;

         use Brotkrueml\JobRouterData\Event\ModifyDatasetOnSynchronisationEvent;

         final class AdjustJobsDataset
         {
            public function __invoke(ModifyDatasetOnSynchronisationEvent $event): void
            {
               // Only the table with the handle "jobs" should be considered
               if ($event->getTable()->getHandle() !== 'jobs') {
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

#. Register your event listener in :file:`Configuration/Services.yaml`

   .. code-block:: yaml

      services:
         YourVendor\YourExtension\EventListener\AdjustJobsDataset:
            tags:
               - name: event.listener
                 identifier: 'adjustJobsDataset'
                 event: Brotkrueml\JobRouterData\Event\ModifyDatasetOnSynchronisationEvent


Retrieve data sets from the different table link types
======================================================

.. _developer-simple-sync-table:

Simple synchronisation table
----------------------------

When a JobData table is synchronised with the :ref:`Simple synchronisation
<module-create-table-link-simple>` type, the data sets are stored in a table
provided by this extension. This is the simplest type, as no programming
knowledge is required. The data sets are stored JSON encoded in a provided table
and can be displayed on the website with a :ref:`content element
<editor-content-element>`.

However, you can also retrieve the data sets independently.


Schema of the table `tx_jobrouterdata_domain_model_dataset`
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

========= ======================================================================
Column    Description
========= ======================================================================
uid       TYPO3-specific column: unique id of the data set (auto increment)
--------- ----------------------------------------------------------------------
pid       TYPO3-specific column: parent id of the data set (always 0)
--------- ----------------------------------------------------------------------
table_uid Relation to a defined table link
--------- ----------------------------------------------------------------------
jrid      jrid of the Jobdata table data set
--------- ----------------------------------------------------------------------
dataset   JSON-encoded data set with the synchronised JobData table row
========= ======================================================================


Get the data sets of a table link programmatically
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

There is an Extbase repository and a domain model available which you can use
in a TYPO3 context::

   <?php
   use Brotkrueml\JobRouterData\Domain\Repository\DatasetRepository;
   use TYPO3\CMS\Core\Utility\GeneralUtility;

   $datasetRepository = GeneralUtility::makeInstance(DatasetRepository::class);
   $datasets = $datasetRepository->findByTableUid(1);

   foreach ($datasets as $dataset) {
      // Show the jrid
      var_dump($dataset->getJrid());

      // Show the JSON-encoded data set
      var_dump($dataset->getDataset());

      // Get the content of a column
      var_dump($dataset->getDatasetContentForColumn('TRAINING'));
   }


.. _developer-custom-table:

Custom table
------------

Synchronising a JobData table into an :ref:`custom table
<module-create-table-link-custom>` has some advantages and disadvantages
compared to the simple synchronisation type described above:

- Flexibility: You can filter the content of a synchronised table with specific
  SQL queries, because all JobData columns are stored in separate columns in the
  TYPO3 table.
- Joining data: You can join the table with other tables in your domain.
- Easy usage: e.g. in TCA select boxes against the simple synchronisation.
- More Work: You have to implement the logic yourself.

But let's start:

#. Create a new extension or use an existing one. Consult the TYPO3 manual how
   to do this.

#. Add or append the table definition to the file :file:`ext_tables.php`:

   .. code-block:: sql

      CREATE TABLE tx_acmejobs_domain_model_jobs (
         jrid int(11) unsigned DEFAULT '0' NOT NULL,
         position varchar(255)  DEFAULT '' NOT NULL,
         active smallint(5) unsigned DEFAULT '0' NOT NULL,

         UNIQUE KEY jrid (jrid)
      );

   The table name must start with `tx_` to be recognised as an custom table in
   the module.

   It must also have a column `jrid`. Add a unique or primary key for the `jrid`
   column. If you want to use the table in a TCA/Extbase context, you must
   create a unique key, because the primary key is implicitly generated by TYPO3
   for the uid.

   Add the columns to be synchronised from the JobData table (in the example
   `position` and `active`. The column type should be the same as in the JobData
   table.

#. Go to the :guilabel:`Admin Tools` > :guilabel:`Maintenance` module, click on
   the :guilabel:`Analyse database` button and create the table.

#. Add a table link in the :ref:`backend module <module-create-table-link-custom>`.

This is the minimal setup to synchronise a JobData table into an custom TYPO3
table. How you will use the table depends on your use case.


Other usage
-----------

Links to JobData tables are also centralised in the Data module, in contrast to
the definition in PHP code.

The table link type :ref:`Other usage <module-create-table-link-other>` can be
used to facilitate the access a JobData table. Links to JobData tables are
also centralised in the :guilabel:`Data` module, in contrast to the definition
in PHP code.

Here is an example to get the table link and initialise the JobRouter Client:

::

   <?php
   use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
   use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
   use TYPO3\CMS\Core\Utility\GeneralUtility;
   use TYPO3\CMS\Extbase\Object\ObjectManager;

   $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
   $tableRepository = $objectManager->get(TableRepository::class);
   $table = $tableRepository->findOneByHandle('contacts');

   $connection = $table->getConnection();
   if ($connection) {
      // Create a JobRouter Client RestClient object
      $client = (new RestClientFactory())->create($connection);
      $response = $restClient->request(
         'GET',
         \sprintf('application/jobdata/tables/%s/datasets', $table->getTableGuid())
      );

      // Do something with the response ...
   }

Have a look into the :doc:`JobRouter Client <jobrouter-client:introduction>` documentation
how to use it. The library eases the access to the JobRouter® REST API.


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

::

   <?php
   declare(strict_types=1);

   namespace Vendor\Extension\Controller;

   use Brotkrueml\JobRouterData\Exception\PrepareException;
   use Brotkrueml\JobRouterData\Transfer\Preparer;
   use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

   final class MyController extends ActionController
   {
      private Preparer $preparer;

      // It's important to use dependency injection to inject all necessary
      // dependencies into the preparer
      public function __construct(Preparer $preparer)
      {
         $this->preparer = $preparer;
      }

      public function myAction()
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
            // In some rare cases an exception can be thrown
            var_dump($e->getMessage());
         }
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

The :php:`JobDataRepository` provides methods to access the JobData REST API
in TYPO3, e.g. in a command or a controller.

The following methods are available:

.. option:: add(array $dataset): array

   Adds a dataset to a JobData table and returns the stored dataset.

.. option:: remove(int ...$jrid): void

   Removes one or more datasets from a JobData table.

.. option:: update(int $jrid, array $dataset): array

   Updates the dataset with the given jrid for a JobData table and returns the
   stored dataset.

.. option:: findAll(): array

   Returns all datasets of the JobData table;

.. option:: findByJrId(int $jrid): array

   Returns the dataset for the given jrid of a JobData table.


Example
-------

Configure in :file:`Configuration/Services.yaml` an alias for the
:php:`JobDataRepository` and add it to the command configuration:

.. code-block:: yaml

   services:
      jobDataRepository.yourTableHandle:
         class: 'Brotkrueml\JobRouterData\Domain\Repository\JobRouter\JobDataRepository'
         arguments:
            $tableHandle: 'yourTableHandle'

      Vendor\Extension\Command\YourCommand:
         tags:
            - name: 'console.command'
              command: 'vendor:yourcommand'
         arguments:
            $jobDataRepository: '@jobDataRepository.yourTableHandle'

Then inject the :php:`JobDataRepository` into the command and use the
appropriate method:

::

   <?php
   declare(strict_types=1);

   namespace Vendor\Extension\Command;

   final class YourCommand extends Command
   {
      private $jobDataRepository;

      public function __construct(JobDataRepository $jobDataRepository)
      {
        $this->jobDataRepository = $jobDataRepository;

        parent::__construct();
      }

      protected function execute(InputInterface $input, OutputInterface $output): int
      {
         $datasets = $this->jobDataRepository->findAll();

         // ... your logic

         return 0;
      }
   }


.. _customise-column-formatting:

Customising the formatting of a table column in the content element
===================================================================

.. versionadded:: 1.0.0

The extension comes with four formatters that are used when rendering the
column content in the :ref:`content element <editor-content-element>`:

- DateFormatter
- DateTimeFormatter
- DecimalFormatter
- IntegerFormatter

These are implemented as :ref:`PSR-14 event listeners <t3coreapi:EventDispatcher>`
and are located in the :file:`Classes/EventListener` folder of this extension.
They receive a :php:`Brotkrueml\JobRouterData\Event\ModifyColumnContentEvent`
event with the following methods:

.. option:: getTable()

The table model.

Return value
   The domain model of a table
   (:php:`Brotkrueml\JobRouterData\Domain\Model\Table`).

.. option:: getColumn()

The column model.

Return value
   The domain model of a column
   (:php:`Brotkrueml\JobRouterData\Domain\Model\Column`).

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
   The locale (for example, "de_DE.utf8" or "en_US") is retrieved from the
   configured site language field `locale`.


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
