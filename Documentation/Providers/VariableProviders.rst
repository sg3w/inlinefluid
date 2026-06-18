Variable Providers
==================

Inlinefluid allows defining **runtime variables** directly via the FlexForm.
These variables are injected into the Fluid rendering context and are available
during template rendering, inline execution, and dynamic data provider processing.

Variables Tab in the FlexForm
-----------------------------

Variables are configured in the **Variables** tab of the FlexForm.

Each variable provider defines:

- A **variable name** (the identifier used in Fluid)
- A **variable value** (the content assigned to the variable)

⚠️ **Important:**
There is **no validation or collision check** for variable names.
If a variable name is already present in the rendering context, it will be
**overwritten** by the FlexForm-defined variable.

Supported Variable Types
------------------------

The following variable types are supported:

- **Input** – simple scalar values
- **Text** – multiline text values
- **Link** – TYPO3 link fields, resolved at runtime
- **Rich Text** – RTE-based content, including HTML markup

All variables are accessible in Fluid using standard notation:

::

   {myVariable}

Runtime Fluid Rendering
----------------------

Variable values are treated as **plain data by default**.
If a variable contains Fluid syntax, it is **not executed automatically**.

To explicitly render variable content as Fluid, the
``<sg3w:inline />`` ViewHelper must be used.

Refer to the **ViewHelper** section for details on controlled Fluid-in-Fluid rendering.

Interaction with Dynamic Data Providers
---------------------------------------

Variables defined via the FlexForm are part of the Fluid rendering context and can
be accessed by Dynamic Data Providers via the VariableProvider.

This allows:

- Using variables as input parameters for providers
- Deriving dynamic data based on editor configuration
- Keeping configuration (FlexForm) and logic (PHP providers) clearly separated

Important Notes
---------------

- Variable names are **not validated** and may override existing context variables
- Fluid execution is always **explicit** via the Inline ViewHelper
- Rich Text values may contain HTML and should be handled accordingly
- Cache behavior follows the CType configuration (``USER`` or ``USER_INT``)

Summary
-------

Variable Providers form the configuration layer of Inlinefluid.
They allow editors to define runtime variables without implicit execution, while
developers retain full control over naming, scope, and Fluid rendering behavior.
