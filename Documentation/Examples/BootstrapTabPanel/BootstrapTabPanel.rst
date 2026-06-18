Bootstrap Tab Panel with XML Data
====================================

This example shows how to render a Bootstrap 5 Tab Panel (``nav-tabs``) where
the tab content is supplied by the built-in **XML Static Data Provider**.

No custom PHP class is needed — the tab data is entered directly in the
backend or loaded from an XML file.

How the XML Provider works
--------------------------

The XML provider parses the data string with ``simplexml_load_string()`` and
converts the result to a plain PHP array via a JSON round-trip.  The root
element becomes the top-level array, and each child element becomes a key.

Given a variable name of ``tabs`` and this XML structure::

   <tabs>
       <tab>
           <id>tab-overview</id>
           <label>Overview</label>
           <content>…</content>
       </tab>
       …
   </tabs>

Inlinefluid stores the parsed result in ``{tabs}``.  The repeated ``<tab>``
children are available as the array ``{tabs.tab}``.

Demo XML
--------

A ready-to-use demo file is shipped with the extension at:

``EXT:inlinefluid/Demo/tabs.xml``

.. code-block:: xml

   <?xml version="1.0" encoding="UTF-8"?>
   <tabs>
       <tab>
           <id>tab-overview</id>
           <label>Overview</label>
           <content>TYPO3 is a free and open-source enterprise Content Management System.
   It provides a stable and reliable foundation for building websites of all sizes.</content>
       </tab>
       <tab>
           <id>tab-features</id>
           <label>Features</label>
           <content>Inlinefluid lets editors write Fluid templates directly in the TYPO3
   backend — no sitepackage deployment needed for simple rendering tasks.</content>
       </tab>
       <tab>
           <id>tab-contact</id>
           <label>Contact</label>
           <content>For support and questions please visit the TYPO3 Slack workspace or
   open an issue in the extension repository.</content>
       </tab>
   </tabs>

Step 1 – Configure the Content Element (inline data)
------------------------------------------------------

Open the Inlinefluid backend element and switch to the **Data** tab.
Add a new **XML** static data provider entry:

- **Variable name**: ``tabs``
- **Data**: paste the XML from the demo file above

Alternatively use a **File** data provider entry:

- **Variable name**: ``tabs``
- **Format**: ``XML``
- **Filename**: ``EXT:inlinefluid/Demo/tabs.xml``

Step 2 – Enter the Fluid Template
-----------------------------------

Switch to the **Fluid** tab and enter the following template.
Two ``<f:for>`` loops iterate over ``{tabs.tab}`` — one builds the navigation
bar, the other builds the content panels:

.. code-block:: html

   <!-- Tab navigation -->
   <ul class="nav nav-tabs" id="inlinefluid-tabs" role="tablist">
     <f:for each="{tabs.tab}" as="tab" iteration="iterator">
       <li class="nav-item" role="presentation">
         <button class="nav-link{f:if(condition: iterator.isFirst, then: ' active')}"
                 id="{tab.id}-tab"
                 data-bs-toggle="tab"
                 data-bs-target="#{tab.id}-pane"
                 type="button"
                 role="tab"
                 aria-controls="{tab.id}-pane"
                 aria-selected="{f:if(condition: iterator.isFirst, then: 'true', else: 'false')}">
           {tab.label}
         </button>
       </li>
     </f:for>
   </ul>

   <!-- Tab content panels -->
   <div class="tab-content" id="inlinefluid-tabs-content">
     <f:for each="{tabs.tab}" as="tab" iteration="iterator">
       <div class="tab-pane fade{f:if(condition: iterator.isFirst, then: ' show active')}"
            id="{tab.id}-pane"
            role="tabpanel"
            aria-labelledby="{tab.id}-tab"
            tabindex="0">
         {tab.content}
       </div>
     </f:for>
   </div>

How it works
------------

1. Inlinefluid passes the XML string to ``XmlDataProvider::renderData()``.
2. ``simplexml_load_string()`` parses the XML; the result is converted to a
   PHP array via ``json_decode(json_encode(...), true)``.
3. The array is assigned to the Fluid variable ``tabs``.
4. ``{tabs.tab}`` is the list of tab items (children of the root
   ``<tabs>`` element).
5. The first ``<f:for>`` loop generates the ``<button>`` elements.
   ``iterator.isFirst`` adds the ``active`` class to the first tab button.
6. The second ``<f:for>`` loop generates the ``<div class="tab-pane">``
   panels.  ``iterator.isFirst`` adds ``show active`` to make the first
   panel visible on load.
7. Bootstrap's JavaScript handles the click behaviour automatically.

Debugging the data structure
-----------------------------

Use ``<f:debug>`` to inspect the parsed array while building the template:

.. code-block:: html

   <f:debug>{tabs}</f:debug>

The output shows the full array structure including the ``tab`` key and all
child properties.

Notes
-----

- Bootstrap 5 CSS and JS must be loaded on the page (e.g. via your
  sitepackage or a CDN link in the **HTML** tab of the content element).
- Keep the ``id`` on the wrapper element (``inlinefluid-tabs``) unique per
  page when using multiple tab components on the same page.
- If only **one** ``<tab>`` element is present in the XML, ``simplexml``
  returns a single object instead of a list — the Fluid loop will not work
  as expected.  Always provide at least two ``<tab>`` elements, or handle
  the single-item case with an ``<f:if>`` wrapper.
- Static data is cached with the page — no ``USER_INT`` is required.
