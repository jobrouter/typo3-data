.. include:: _includes.rst.txt

.. _introduction:

============
Introduction
============

`JobRouter® <https://www.jobrouter.com/>`_ is a scalable digitisation platform
which links processes, data and documents. The TYPO3 extension `TYPO3 JobRouter
Data <https://github.com/brotkrueml/typo3-jobrouter-data>`_ connects JobRouter®
JobData tables with TYPO3.

.. admonition:: Work In Progress

   Currently the TYPO3 JobRouter Data extension is in a beta phase. Although it
   can already be used, the API is still subject to changes.


What does it do?
================

TYPO3 JobRouter Data is a TYPO3 extension that

* Provides a module to manage JobData table links
* Synchronises data sets from JobData tables into TYPO3
* Transmits data sets from TYPO3 into JobData tables
* Provides a form finisher to store form data into a JobData table

A content element is available to display synchronised data sets on a web page.

This extension uses the :doc:`JobRouter Client <jobrouter-client:introduction>`
library and has the :doc:`TYPO3 JobRouter Connector <typo3-jobrouter-connector:introduction>`
extension as a requirement to define connections to JobRouter® installations.


.. _release-management:

Release management
==================

This extension uses `semantic versioning <https://semver.org/>`_ which
basically means for you, that

* Bugfix updates (e.g. 1.0.0 => 1.0.1) just includes small bug fixes or security
  relevant stuff without breaking changes.
* Minor updates (e.g. 1.0.0 => 1.1.0) includes new features and smaller tasks
  without breaking changes.
* Major updates (e.g. 1.0.0 => 2.0.0) includes breaking changes which can be
  refactorings, features or bug fixes.

The changes between the different versions can be found in the
:ref:`changelog <changelog>`.
