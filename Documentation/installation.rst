.. include:: _includes.rst.txt

.. _installation:

============
Installation
============

Target group: **Administrators**

.. contents::
   :depth: 1
   :local:


.. _installation-requirements:

Requirements
============

The extension in version |release| is available for TYPO3 v11 LTS and TYPO3 v12
LTS.

To use the :ref:`dashboard widgets <dashboard-widgets>`, install and activate
the Dashboard system extension. To use the :ref:`form finisher <form-finisher>`
install and activate the Form system extension.


.. _version-matrix:

Version matrix
==============

============== ========== ===========
JobRouter Data PHP        TYPO3
============== ========== ===========
2.0            8.1 - 8.2  11.5 / 12.4
-------------- ---------- -----------
1.1            7.4 - 8.2  10.4 / 11.5
-------------- ---------- -----------
0.14 / 1.0     7.3 - 8.1  10.4 / 11.5
-------------- ---------- -----------
0.13           7.2 - 7.4  10.4
============== ========== ===========


.. _installation-composer:

Installation via composer
=========================

The recommended way to install this extension is by using Composer. In your
Composer-based TYPO3 project root, just type::

   composer req brotkrueml/typo3-jobrouter-data

and the recent version will be installed.

The extension offers some configuration which is explained in the
:ref:`Configuration <Configuration>` chapter.


.. _installation-extension-manager:

Installation in Extension Manager
=================================

You can also install the extension from the `TYPO3 Extension Repository (TER)`_.
See :ref:`t3start:extensions_legacy_management` for a manual how to
install an extension.


.. _TYPO3 Extension Repository (TER): https://extensions.typo3.org/extension/jobrouter_data/
