Predefined Template Variables
=============================

Inlinefluid automatically injects a set of **predefined core variables** into the
Fluid rendering context. These variables are always available in the template and
do not need to be configured manually.

All predefined variables are provided **exclusively in uppercase**.
No aliases or lowercase variants are created.

Available Variables
-------------------

The following variables are predefined and assigned before template rendering:

Request Object
~~~~~~~~~~~~~~

- Variable: ``{REQUEST}``

Represents the current TYPO3 request object.

Typical use cases:

- Accessing request parameters
- Inspecting routing or request attributes
- Debugging request-related context

Example:

::

   <f:debug>{REQUEST}</f:debug>


Content Record
~~~~~~~~~~~~~~

- Variable: ``{CONTENTOBJECT}``

Contains the **current ``tt_content`` record** as an array.
This represents the content element that is currently being rendered.

Typical use cases:

- Accessing fields such as ``uid``, ``header``, ``bodytext``, or custom columns
- Inspecting CType-specific configuration
- Content element–specific rendering logic

Example:

::

   <f:debug>{CONTENTOBJECT}</f:debug>


Current Page Record
~~~~~~~~~~~~~~~~~~~

- Variable: ``{PAGE}``

Contains the current page record (``pages`` table).

Typical use cases:

- Accessing page fields such as title, uid, or custom columns
- Page-dependent rendering logic

Example:

::

   <h1>{PAGE.title}</h1>


Frontend Controller
~~~~~~~~~~~~~~~~~~~

- Variable: ``{TSFE}``

Represents the TYPO3 frontend controller instance.

Typical use cases:

- Access to frontend state
- Language and site information
- Debugging frontend behavior

Example:

::

   <f:debug>{TSFE}</f:debug>


TypoScript Setup
~~~~~~~~~~~~~~~~

- Variable: ``{TSSETUP}``

Contains the resolved TypoScript setup array.

Typical use cases:

- Reading TypoScript configuration values
- Conditional rendering based on setup flags
- Debugging TypoScript output

Example:

::

   <f:debug>{TSSETUP}</f:debug>


Design Considerations
---------------------

These variables are injected intentionally and unconditionally:

- No name collision checks are performed
- Existing variables with the same name will be overwritten
- Variables should be treated as **read-only**
- Usage requires in-depth TYPO3 and Fluid knowledge

These variables are intended for **developer and technical use cases** and are
not suitable for standard editorial workflows.

Summary
-------

Inlinefluid provides a fixed set of uppercase, predefined template variables that
expose TYPO3 runtime context directly to Fluid. This enables advanced rendering
and debugging scenarios while requiring careful and responsible usage.
