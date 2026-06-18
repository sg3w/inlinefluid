Target Audience
===============

Inlinefluid is designed for **TYPO3 developers and integrators** who need a high
degree of flexibility and control over Fluid rendering.

The extension deliberately exposes low-level rendering capabilities and therefore
assumes solid technical knowledge of TYPO3 and Fluid.

Intended Users
--------------

Inlinefluid is suitable for users who:

- Develop TYPO3 extensions or site packages
- Are familiar with Fluid templating and ViewHelpers
- Understand TYPO3 content elements and CType behavior
- Work with FlexForms and structured backend configuration
- Have experience with caching concepts (``USER`` vs ``USER_INT``)
- Need to render dynamic or externally provided data at runtime

Typical roles include:

- TYPO3 developers
- Technical integrators
- Agency developers
- Solution architects

Not Intended for Editors
------------------------

Inlinefluid is **not intended for standard editorial usage**.

Reasons include:

- Direct editing of Fluid templates in the backend
- No syntax validation at save time
- Potential runtime errors caused by invalid Fluid
- Access to low-level TYPO3 runtime variables
- Explicit execution of nested Fluid code

Using Inlinefluid without sufficient technical knowledge can lead to:

- Broken frontend output
- Hard-to-debug rendering issues
- Performance problems due to incorrect cache usage

Required Knowledge
------------------

Users of Inlinefluid should be comfortable with:

- Fluid syntax and rendering lifecycle
- TYPO3 frontend rendering pipeline
- FlexForm configuration
- FAL media handling
- TypoScript basics
- Debugging using ``<f:debug>``

Summary
-------

Inlinefluid targets experienced TYPO3 developers and integrators who require
maximum flexibility and are willing to take responsibility for rendering logic,
performance, and stability. It is a powerful tool, but intentionally not a
low-threshold editor feature.
