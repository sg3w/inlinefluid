Bootstrap Accordion with External Data
=======================================

This example shows how to render a Bootstrap 5 Accordion component where the
accordion items are supplied by a **Dynamic Data Provider** (an external PHP class).

Use case
--------

An accordion driven by external data is useful when the content items come from
an API, a custom database table, or any other runtime source that is not stored
in the standard TYPO3 content record.

Step 1 – Create the Data Provider
----------------------------------

Create a PHP class that extends ``AbstractExternalDataProvider`` and returns an
array of accordion items:

.. code-block:: php

   <?php
   namespace Vendor\MySitepackage\DataProvider;

   use Sg3w\Inlinefluid\DataProvider\External\AbstractExternalDataProvider;

   class AccordionDataProvider extends AbstractExternalDataProvider
   {
       public function renderData(string $dataString): mixed
       {
           // Replace this with your own data source (API call, repository, …)
           return [
               [
                   'id'      => 'item-1',
                   'title'   => 'What is TYPO3?',
                   'content' => 'TYPO3 is an open-source enterprise Content Management System.',
               ],
               [
                   'id'      => 'item-2',
                   'title'   => 'What is Inlinefluid?',
                   'content' => 'Inlinefluid lets editors write Fluid templates directly in the backend.',
               ],
               [
                   'id'      => 'item-3',
                   'title'   => 'Why Bootstrap?',
                   'content' => 'Bootstrap provides ready-to-use interactive components like accordions.',
               ],
           ];
       }
   }

Step 2 – Configure the Content Element
----------------------------------------

In the Inlinefluid backend element open the **Dynamic Data Providers** tab and
add a new entry:

- **Variable name**: ``accordionItems``
- **Provider class**: ``\Vendor\MySitepackage\DataProvider\AccordionDataProvider``

Step 3 – Enter the Fluid Template
-----------------------------------

In the **Fluid** tab of the content element enter the following template:

.. code-block:: html

   <div class="accordion" id="inlinefluid-accordion">

     <f:for each="{accordionItems}" as="item" iteration="iterator">

       <div class="accordion-item">

         <h2 class="accordion-header" id="heading-{item.id}">
           <button class="accordion-button{f:if(condition: iterator.isFirst, else: ' collapsed')}"
                   type="button"
                   data-bs-toggle="collapse"
                   data-bs-target="#collapse-{item.id}"
                   aria-expanded="{f:if(condition: iterator.isFirst, then: 'true', else: 'false')}"
                   aria-controls="collapse-{item.id}">
             {item.title}
           </button>
         </h2>

         <div id="collapse-{item.id}"
              class="accordion-collapse collapse{f:if(condition: iterator.isFirst, then: ' show')}"
              aria-labelledby="heading-{item.id}"
              data-bs-parent="#inlinefluid-accordion">
           <div class="accordion-body">
             {item.content}
           </div>
         </div>

       </div>

     </f:for>

   </div>

How it works
------------

1. Inlinefluid instantiates ``AccordionDataProvider`` and calls ``renderData()``.
2. The returned array is assigned to the Fluid variable ``accordionItems``.
3. The Fluid ``<f:for>`` loop iterates over the items.
4. ``iterator.isFirst`` is used to mark the first panel as expanded (``show``)
   and its button as active (without ``collapsed``).
5. Bootstrap's JavaScript handles the open/close behaviour at runtime.

Notes
-----

- Bootstrap 5 CSS and JS must be loaded on the page (e.g. via your sitepackage).
- The accordion ``id`` attribute (``inlinefluid-accordion``) is used by
  ``data-bs-parent`` to close other panels when one is opened.  Change it to a
  unique value if you place more than one accordion on the same page.
- For cacheable pages make sure ``renderData()`` returns deterministic data.
  Use ``USER_INT`` rendering if the data is volatile (e.g. live API responses).
