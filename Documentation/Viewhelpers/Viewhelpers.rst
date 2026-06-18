ViewHelper
==========

Inlinefluid provides a custom ViewHelper that allows rendering **Fluid inside Fluid**.
This is especially useful when Fluid markup itself is stored in variables, database
fields, external files, or even provided by **other TYPO3 extensions**.

Namespace Declaration
---------------------

Before using the Inline ViewHelper, the namespace must be declared in the Fluid template:

.. code-block:: html

   {namespace sg3w=Sg3w\Inlinefluid\ViewHelpers}

This makes the ViewHelper available under the ``sg3w`` prefix.

Inline ViewHelper
-----------------

The Inline ViewHelper is used as follows:

.. code-block:: html

   <sg3w:inline content="{text1}" />

Purpose
-------

Normally, Fluid does **not** evaluate Fluid syntax that is contained inside variables.
If a variable contains markup such as ``{variable}``, ``<f:if>``, or other ViewHelpers,
this content would be output as plain text.

The ``<sg3w:inline />`` ViewHelper explicitly tells Inlinefluid to **parse and render**
the given content again as Fluid.

This makes it possible to nest Fluid rendering safely and intentionally.

Cross-Extension Usage
---------------------

The Inline ViewHelper is **not limited to templates within the Inlinefluid extension**.
It can be used from:

- Fluid templates of **other TYPO3 extensions**
- Custom site packages
- External Fluid templates loaded from ``EXT:`` paths
- Shared or modular Fluid components

As long as the Inlinefluid extension is installed and the namespace declaration is
present, ``<sg3w:inline />`` can be used to evaluate Fluid content originating from
any external source.

Typical Use Cases
-----------------

- Rendering Fluid markup stored in database fields (e.g. ``bodytext`` or custom fields)
- Rendering Fluid templates loaded from external files
- Rendering Fluid markup provided by other extensions
- Rendering dynamically generated Fluid code
- Building configurable or CMS-driven layouts where editors provide Fluid-based markup
- Rendering demo or example templates shipped with an extension

Example
-------

Assume the variable ``text1`` contains the following content:

.. code-block:: html

   <f:if condition="{data.header}">
       <h2>{data.header}</h2>
   </f:if>

Without the Inline ViewHelper, this content would be rendered literally.
With Inlinefluid, it is evaluated correctly:

.. code-block:: html

   {namespace sg3w=Sg3w\Inlinefluid\ViewHelpers}
   <sg3w:inline content="{text1}" />

Resulting output (example):

.. code-block:: html

   <h2>My Header</h2>

Important Notes
---------------

- The Inline ViewHelper should be used **explicitly and intentionally**, as it enables
  dynamic template execution.
- Editor-provided or external content should be validated or restricted where necessary.
- The namespace declaration is required in every template where the ViewHelper is used.
- This mechanism complements the HTML Injection feature and helps keep Fluid templates
  clean and collision-free.

Summary
-------

The ``<sg3w:inline />`` ViewHelper is the core feature of Inlinefluid.
It enables controlled, nested Fluid rendering across extensions and allows Fluid code
to live outside traditional template files while still being rendered correctly.
