.. _introduction:

============
Introduction
============

`JobRouter®`_ is a scalable digitalisation platform which links processes, data
and documents. The TYPO3 extension `TYPO3 JobRouter Data`_ connects JobRouter®
JobData tables with TYPO3.


What does it do?
================

TYPO3 JobRouter Data is a TYPO3 extension that

*  Provides a module to manage JobData table links
*  Synchronises data sets from JobData tables into TYPO3
*  Transmits data sets from TYPO3 into JobData tables
*  Provides a form finisher to store form data into a JobData table

A content element is available to display synchronised data sets on a web page.

This extension uses the `JobRouter REST Client`_ library and has the
:ref:`TYPO3 JobRouter Connector <ext_jobrouter_connector:introduction>`
extension as a requirement to define connections to JobRouter® installations.

.. note::
   If you find a bug or want to propose a feature, please use the
   `issue tracker on GitHub`_.


.. _release-management:

Release management
==================

This extension uses `semantic versioning`_ which basically means for you, that

*  Bugfix updates (for example, 1.0.0 => 1.0.1) just includes small bug fixes or
   security relevant stuff without breaking changes.
*  Minor updates (for example, 1.0.0 => 1.1.0) includes new features and smaller
   tasks without breaking changes.
*  Major updates (for example, 1.0.0 => 2.0.0) includes breaking changes which
   can be refactorings, features or bug fixes.

The changes between the different versions can be found in the
:ref:`changelog <changelog>`.


.. _issue tracker on GitHub: https://github.com/jobrouter/typo3-data/issues
.. _JobRouter®: https://www.jobrouter.com/
.. _JobRouter REST Client: https://github.com/jobrouter/php-rest-client
.. _semantic versioning: https://semver.org/
.. _TYPO3 JobRouter Data: https://github.com/jobrouter/typo3-data
