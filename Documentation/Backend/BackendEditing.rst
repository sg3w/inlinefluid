Backend Editing
===============

Inlinefluid allows editing Fluid templates **directly in the TYPO3 backend**.
The main Fluid template is entered into the content element ``bodytext`` field and
is treated as executable Fluid at runtime.

Inline Editor in the Backend
----------------------------

The ``bodytext`` field uses the TYPO3 **code editor (T3Editor)** and provides:

- Syntax highlighting for Fluid templates
- Indentation and basic code formatting
- Improved readability for complex Fluid structures

This enables editors and developers to work with Fluid in a controlled backend
environment without creating or modifying separate template files.

Editable Fluid Content
----------------------

The backend editor supports the full Fluid feature set, including:

- Standard Fluid ViewHelpers (``<f:if>``, ``<f:for>``, ``<f:render>``, etc.)
- Custom ViewHelpers from installed extensions
- Inlinefluid-specific ViewHelpers
- Variable output and expressions
- TypoLink and URI generation

Example
-------

Example content entered in the ``bodytext`` field:

::

   <f:debug>{_all}</f:debug>

   <sg3w:inline content="{text1}" />

   <f:link.typolink parameter="{link}">
       Link
   </f:link.typolink>

This example demonstrates:

- Access to the full Fluid variable scope via ``{_all}``
- Nested Fluid rendering using the Inline ViewHelper
- Standard TYPO3 link generation inside backend-authored Fluid

Interaction with the Inline Editor
----------------------------------

The backend-edited Fluid template acts as the **primary rendering layer**.
Additional Fluid content can be injected via:

- Variables assigned in the controller or DataProvider
- FlexForm fields
- External sources (files, extensions, APIs)

Nested Fluid execution only occurs where explicitly enabled using
``<sg3w:inline />``.

Safety and Control
------------------

To avoid unintended behavior:

- Fluid execution in the backend is always explicit
- No automatic evaluation of variable content occurs
- Dynamic rendering must be triggered intentionally
- Cache behavior follows the configured CType (``USER`` or ``USER_INT``)

Typical Use Cases
-----------------

- Rapid prototyping of Fluid templates
- CMS-driven layout customization
- Demo or showcase content elements
- Extension development without additional template files
- Advanced editor workflows with controlled Fluid access

Summary
-------

Backend Editing is a core feature of Inlinefluid.
It brings Fluid template authoring directly into the TYPO3 backend while preserving
explicit execution, clear separation of responsibilities, and full TYPO3 compatibility.
