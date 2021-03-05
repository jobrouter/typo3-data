.. include:: _includes.txt

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

TYPO3 JobRouter Data is a TYPO3 extension for

* Providing a module to manage JobData table links
* Synchronising data sets from JobData tables into TYPO3
* Transmitting data sets from TYPO3 into JobData tables
* Providing a form finisher to store form data into a JobData table

A content element is available to display synchronised data sets on a web page.

This extension uses the :doc:`JobRouter Client <client:introduction>`
library and has the :doc:`TYPO3 JobRouter Connector <connector:introduction>`
extension as a requirement to define connections to JobRouter® installations.
