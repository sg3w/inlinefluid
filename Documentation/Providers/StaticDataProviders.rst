Static Data Providers
=====================

Inlinefluid supports **static data providers** for supplying predefined data
directly from the backend without executing PHP logic at runtime.
This is useful for configuration-driven templates, demos, fixtures, and
repeatable content structures.

Static data is parsed once per rendering cycle and exposed to Fluid as
structured data.

Supported Formats
-----------------

The following data formats are supported:

- **JSON**
- **YAML**
- **XML**
- **CSV**
- **Serialized PHP**
- **Plain Text**

Each static data block defines:

- A **variable name**
- A **data payload** (inline or file-based)

After parsing, the resulting data is exposed to Fluid as an **array** or
string (in case of plain text).

Static Data from Backend
------------------------

Static data can be entered directly in the backend using the corresponding
FlexForm fields.

Typical use cases include:

- Configuration lists
- Demo or example data
- Reusable content fragments
- Test fixtures for development
- Editor-defined data sets without PHP involvement

Static Data from File
--------------------

Static data can also be loaded from external files located in:

- ``fileadmin/`` paths
- ``EXT:`` paths of installed extensions

Supported file contents include:

- Structured data (JSON, YAML, XML, CSV)
- Serialized PHP data
- **Plain text files** (e.g. ``.txt``, ``.md``)

When loading plain text files:

- The file content is passed through unchanged
- No parsing or transformation is applied
- The content is exposed to Fluid as a **string**

This makes file-based static providers suitable for:

- Loading markup fragments
- Text templates
- Documentation snippets
- Predefined content blocks

Fluid Usage Example
-------------------

Example usage of static data in Fluid:

::

   <f:debug>{myStaticData}</f:debug>

   <f:for each="{myStaticData.items}" as="item">
       <div>{item.title}</div>
   </f:for>

When using plain text:

::

   <pre>{myStaticText}</pre>

Important Notes
---------------

- Static data is read-only during rendering
- No runtime logic is executed
- File paths must be accessible and readable
- Parsing errors result in empty or invalid data structures
- Plain text content is not parsed as Fluid unless explicitly rendered via the
  Inline ViewHelper

Summary
-------

Static Data Providers allow Inlinefluid to inject predefined data into Fluid
templates from the backend or from files. In addition to structured formats,
plain text files are supported, enabling flexible, configuration-driven
templates without PHP logic.
