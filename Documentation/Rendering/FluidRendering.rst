Rendering Fluid
===============

Inlinefluid renders content using **TYPO3 Fluid at runtime**.
The rendering process is designed to allow dynamic, editor-driven Fluid code while
keeping execution explicit and controlled.

Depending on the selected CType configuration, rendering is executed via:

- ``USER`` objects (cacheable)
- ``USER_INT`` objects (non-cacheable)

This allows Inlinefluid to integrate cleanly into TYPO3’s rendering and caching
pipeline while still enabling highly dynamic output.

Fluid Rendering in the Inline Editor
------------------------------------

A central feature of Inlinefluid is the **Inline Editor**, which allows editors or
developers to enter Fluid markup directly in the TYPO3 backend.

Unlike standard TYPO3 content fields, Inlinefluid treats this content as **executable
Fluid**, not just text.

Key characteristics of the Inline Editor:

- Fluid code is stored as plain text (e.g. in FlexForm fields or database columns)
- Rendering happens **explicitly at runtime**
- No implicit Fluid execution occurs without Inlinefluid logic
- Editors can define dynamic structures using Fluid syntax

This makes the Inline Editor suitable for:

- Prototyping layouts
- Demo and example templates
- CMS-driven layout definitions
- Dynamic markup generation without creating separate template files

Fluid-in-Fluid Rendering
------------------------

By default, Fluid does **not** render Fluid syntax contained in variables.
Inlinefluid intentionally overrides this limitation by providing a second rendering
step.

This enables **Fluid-in-Fluid rendering**.

Example use case:

- An editor enters Fluid markup into the Inline Editor
- The content is passed into a variable (e.g. ``text1``)
- Inlinefluid renders this content again as Fluid

This approach ensures:

- Clear separation between template structure and executable Fluid content
- Explicit control over where Fluid is evaluated
- Reduced risk of accidental or implicit execution


Summary
-------

Inlinefluid enables controlled, runtime Fluid rendering with full TYPO3 integration.
The Inline Editor allows Fluid to be authored outside classic template files, while
the Inline ViewHelper provides a safe and explicit mechanism for executing that Fluid
content.
