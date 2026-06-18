Content Elements (CType)
========================

Inlinefluid registers two custom TYPO3 content element types.
Both elements are technically identical in structure and backend configuration,
but differ in **rendering strategy and cache behavior**.

The content elements are registered under the **“Special”** section in the
TYPO3 content element wizard.

Due to their technical nature and the ability to execute Fluid code, these
content elements are **only very limitedly suitable for editors** and are
primarily intended for developers or technically experienced users.

Overview
--------

The following content element types are available:

inlinefluid_editor
------------------

This variant uses **non-cached rendering** via ``USER_INT``.

Characteristics:

- Rendering happens on every request
- Suitable for dynamic, request-based, or user-specific output
- Recommended for API-driven data, session-dependent logic, or frequently
  changing content
- Higher runtime cost compared to cached rendering

inlinefluid_editorcached
------------------------

This variant uses **cached rendering** via ``USER``.

Characteristics:

- Output is cached according to TYPO3’s caching framework
- Suitable for static or semi-static content
- Better performance for content that does not change per request
- Requires deterministic rendering logic

Shared Configuration
--------------------

Both content elements share:

- The same FlexForm configuration
- The same backend editing interface
- The same support for:
  - Backend Fluid editing
  - Variable Providers
  - Dynamic Data Providers
  - HTML Before / After Rendering
  - Inline ViewHelper rendering

The only difference is the cacheability of the rendering.

Editorial Considerations
------------------------

Inlinefluid content elements expose **raw Fluid editing capabilities** in the
backend. This implies:

- No WYSIWYG abstraction
- No validation of Fluid syntax at save time
- Potential rendering errors caused by invalid templates
- Full access to the Fluid variable scope

For these reasons, Inlinefluid content elements:

- Are **not intended for standard editorial workflows**
- Should be used in controlled environments
- Are best suited for:
  - Developers
  - Technical power users
  - Prototyping and demo setups
  - Extension and site package development

Summary
-------

Inlinefluid provides two CType variants that differ only in cache behavior.
Both are intentionally placed under **Special** and should be used with care.
They offer maximum flexibility and control at the cost of increased technical
responsibility.
