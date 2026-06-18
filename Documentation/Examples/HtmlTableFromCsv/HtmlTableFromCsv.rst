HTML Table from CSV
===================

This example shows how to render an HTML ``<table>`` from a CSV file using the
built-in **CSV Static Data Provider**.  No PHP class is needed — the data is
loaded from a file or entered inline in the backend.

How the CSV Provider works
--------------------------

The CSV provider uses TYPO3's ``CsvUtility::csvToArray()`` internally.  It
returns a **two-dimensional, numerically-indexed array**:

- **Row 0** — the header row (column labels)
- **Row 1 … n** — the data rows

Each row is itself a numerically-indexed array of cell values.

Example for the demo file below::

   {rows.0} → ['Name', 'Department', 'Location', 'Phone']
   {rows.1} → ['Alice Müller', 'Engineering', 'Berlin', '+49 30 123456']
   …

Demo CSV file
-------------

A ready-to-use demo file is shipped with the extension at:

``EXT:inlinefluid/Demo/employees.csv``

.. code-block:: text

   Name,Department,Location,Phone
   Alice Müller,Engineering,Berlin,+49 30 123456
   Bob Schmidt,Marketing,Hamburg,+49 40 654321
   Clara Weber,Design,Munich,+49 89 112233
   David Koch,Engineering,Berlin,+49 30 778899
   Eva Braun,Sales,Frankfurt,+49 69 445566

Step 1 – Configure the Content Element
----------------------------------------

Open the Inlinefluid backend element and switch to the **Data** tab.
Add a new **File** data provider entry:

- **Variable name**: ``rows``
- **Format**: ``CSV``
- **Filename**: ``EXT:inlinefluid/Demo/employees.csv``

Alternatively, add a **CSV** inline data provider entry and paste the CSV
content directly into the data field.

Step 2 – Enter the Fluid Template
------------------------------------

Switch to the **Fluid** tab and enter the following template.

The first row (``{rows.0}``) is rendered as ``<th>`` header cells.
All remaining rows are rendered as ``<td>`` data cells using a nested
``<f:for>`` loop.  ``iterator.isFirst`` skips the header row inside the
outer loop so it is not duplicated in ``<tbody>``:

.. code-block:: html

   <table class="table table-striped table-bordered">

     <thead>
       <tr>
         <f:for each="{rows.0}" as="header">
           <th>{header}</th>
         </f:for>
       </tr>
     </thead>

     <tbody>
       <f:for each="{rows}" as="row" iteration="iterator">
         <f:if condition="{iterator.isFirst}">
           <f:else>
             <tr>
               <f:for each="{row}" as="cell">
                 <td>{cell}</td>
               </f:for>
             </tr>
           </f:else>
         </f:if>
       </f:for>
     </tbody>

   </table>

How it works
------------

1. Inlinefluid passes the CSV string to ``CsvDataProvider::renderData()``.
2. ``CsvUtility::csvToArray()`` splits the input into a 2D array.
3. The array is assigned to the Fluid variable ``rows``.
4. ``{rows.0}`` accesses the first row directly by its numeric key — these
   are the column headers rendered as ``<th>`` inside ``<thead>``.
5. The outer ``<f:for>`` loop iterates over all rows.
   ``iterator.isFirst`` skips row 0 so the header is not repeated in
   ``<tbody>``.
6. The inner ``<f:for>`` loop iterates over the cells of each data row and
   renders them as ``<td>``.

Debugging the data structure
-----------------------------

Use ``<f:debug>`` to inspect the parsed array while building the template:

.. code-block:: html

   <f:debug>{rows}</f:debug>

Notes
-----

- Bootstrap 5 table classes (``table``, ``table-striped``, etc.) require
  Bootstrap CSS to be loaded on the page.
- The delimiter defaults to a comma (``,``).  If your CSV uses semicolons or
  tabs, the raw ``CsvDataProvider`` does not expose delimiter configuration —
  consider converting the file to comma-separated format in that case.
- Static data is cached with the page — no ``USER_INT`` is required.
- Empty trailing lines in the CSV result in empty rows in the rendered table.
  Make sure the file does not end with a blank line when using inline data.
