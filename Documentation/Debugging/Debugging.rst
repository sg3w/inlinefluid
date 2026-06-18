Debugging
=========

To inspect available variables during rendering, the Fluid ``<f:debug>``
ViewHelper can be used.

This is especially useful when working with backend-authored Fluid templates,
Dynamic Data Providers, or variable-based configurations.

Example
-------

::

   <f:debug inline="true">{_all}</f:debug>

This outputs **all variables** currently available in the Fluid rendering
context, including:

- Record data
- Variables defined via the Variables tab
- Static Data Providers
- Dynamic Data Providers
- Media references
- Internal helper variables

Fallback Behavior
-----------------

If **no Fluid content** is entered in the content element (e.g. an empty
``bodytext`` field), Inlinefluid automatically falls back to rendering the
equivalent of:

::

   <f:debug inline="true">{_all}</f:debug>

This ensures that:

- The content element still produces visible output
- Developers can immediately inspect the available context
- Misconfiguration or missing content is easy to diagnose

Important Notes
---------------

- ``<f:debug>`` output is intended for development and debugging only
- It should not be used in production-facing templates
- The output may expose internal or sensitive data
- Always remove or guard debug output before deployment

Summary
-------

Debugging in Inlinefluid is straightforward and intentionally transparent.
Using ``<f:debug>{_all}</f:debug>`` allows full inspection of the rendering
context and also serves as a built-in fallback when no Fluid content is defined,
making development and troubleshooting significantly easier.
