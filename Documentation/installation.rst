.. include:: _includes.txt

.. _installation:

============
Installation
============

Target group: **Administrators**

.. note::

   The extension works with TYPO3 9 LTS.


.. _installation-requirements:

Requirements
============

The extension has no PHP requirements in addition to TYPO3 and the TYPO3 JobRouter
Connector extension.


.. _installation-composer:

Composer
========

For now only the Composer-based installation is supported:

#. Add a dependency `brotkrueml/typo3-jobrouter-data` to your project's
   :file:`composer.json` file to install the current version:

   .. code-block:: shell

      composer req brotkrueml/typo3-jobrouter-data

#. Activate the extension in the Extension Manager.
