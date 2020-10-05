.. include:: _includes.txt

.. _form-finisher:

=============
Form finisher
=============

Target group: **Integrators**, **Developers**

.. contents:: Table of Contents
   :depth: 3
   :local:

Transmit data
=============

A form finisher `JobRouterTransmitData` is available to transmit form fields to
a JobData table. After submitting a form, the form values are stored in a
transfer table. A command, hopefully executed regularly, takes these transfer
records and transmit this data. This is due the fact, that a JobRouter®
installation can be temporarily not available due to maintenance or network
problems. Also the submitting of a form should be as fast as possible.

.. note::

   The finisher can only be used in the yaml form definition, not in the
   :guilabel:`Form` GUI module.


Transmit to a JobData table
---------------------------

So, let's start with an example. The form finisher is defined in the YAML
configuration of the specific form:

.. code-block:: yaml

   finishers:
      -
         identifier: JobRouterTransmitData
         options:
            handle: 'website_contact'
            columns:
               name: '{preName} {lastName}'
               company: '{company}'
               email_address: '{email}'
               phone_number: '{phone}'
               message: '{message}'
               source: 'Website'

The `handle` is required as it connects the fields to the appropriate
:ref:`table link <module-create-table-link>`.

You can map the form fields to the JobData columns. As you can see in the
example above, you define the JobData column as the key (e.g `email_address`)
and then map it with the value to be stored. This can be the form field
identifier which is enclosed in curly brackets (e.g. `{email}`), a static value,
a combination of a static value with a form field or even multiple form fields.

.. note::
   Only columns that are configured in the :ref:`table link
   <module-create-table-link>` are possible. If a column is used that is not
   defined, an exception is thrown.

   If the value of a form field is an array, like from a multi checkbox, the
   array is converted to a csv string and stored in the given process table
   field.


Start multiple transmissions
----------------------------

It is also possible to start multiple transmissions – even on different
JobRouter® installations. Just use the array notation in :yaml:`options`:

.. code-block:: yaml

   finishers:
      -
         identifier: JobRouterTransmitData
         options:
            -
               handle: 'website_contact'
               columns:
                  name: '{preName} {lastName}'
                  company: '{company}'
                  email_address: '{email}'
                  phone_number: '{phone}'
                  message: '{message}'
                  source: 'Website'
            -
               handle: 'anonymous_messages'
               columns:
                  ANON_MESSAGE: '{message}'
                  FROM_URL: 'https://www.example.com/demo'


.. _form-finisher-variables:

Variables
---------

You can use variables as column values. For more information have a look into
the available :ref:`variable resolvers <base:variable-resolvers>`. You can also
write your :ref:`own variable resolvers <base:developer-variable-resolvers>`.
