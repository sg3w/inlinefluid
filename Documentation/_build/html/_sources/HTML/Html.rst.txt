HTML Before / After Rendering
=============================

Inlinefluid allows adding additional HTML **before and/or after** the Fluid
template is rendered.

This behavior is configured in the **HTML Content** tab of the FlexForm and is
applied outside of the actual Fluid rendering process.

Rendering Order
---------------

The rendering order is strictly defined:

1. HTML **Before Rendering**
2. Fluid template rendering
3. HTML **After Rendering**

This guarantees predictable output and clear separation of responsibilities.

Typical Use Cases
-----------------

- Wrapping markup around the rendered Fluid output
- Adding custom container or layout elements
- Including CSS and JavaScript via ``<style>`` / ``<script>`` blocks **outside**
  of Fluid templates
- Avoiding collisions with Fluid syntax (e.g. curly braces, ViewHelper-like
  markup, inline expressions)
- Adding debug or diagnostic output around the rendered content

Why Before / After Rendering
----------------------------

Placing CSS or JavaScript directly inside Fluid templates can lead to syntax
conflicts or reduced readability.

By using HTML before/after rendering:

- Asset-related markup is clearly separated from Fluid logic
- Fluid templates remain clean and focused on rendering
- No escaping or workaround logic is required
- The rendered output remains deterministic

Important Notes
---------------

- The HTML before/after blocks are **not parsed as Fluid**
- No variables or ViewHelpers are evaluated in these blocks
- The mechanism complements Inline ViewHelper rendering, but serves a different
  purpose

Summary
-------

HTML Before / After Rendering provides a safe and explicit way to surround Fluid
output with additional markup and assets, while keeping Fluid templates focused
on their primary responsibility: structured rendering.
