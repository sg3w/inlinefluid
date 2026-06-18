Dynamic Data Providers
======================

Inlinefluid can provide **dynamic data** to templates by executing custom PHP classes
at runtime. This mechanism is intended for cases where data is not (or not only)
available via the standard TYPO3 record, but must be assembled from external sources,
APIs, custom repositories, or calculated values.

Concept
-------

Each dynamic provider defines:

- A **variable name** (the name under which the data becomes available in Fluid)
- A **fully qualified PHP class name** (FQCN) of the provider implementation

Example provider class:

::

   \Sg3w\Inlinefluid\DataProvider\External\ExternalDemoDataProvider

At runtime Inlinefluid instantiates the provider class and assigns the returned value
into the Fluid rendering context. The template can then access the data through the
configured variable name.

Mandatory Base Class
--------------------

All external dynamic data providers **must implement** the abstract base class:

::

   \Sg3w\Inlinefluid\DataProvider\External\AbstractExternalDataProvider

This abstract class defines the required contract for Inlinefluid-compatible providers
and ensures consistent integration into the rendering pipeline.

By extending ``AbstractExternalDataProvider``, a provider automatically gains:

- Access to the Fluid ``RenderingContextInterface``
- Access to the VariableProvider
- A standardized execution lifecycle
- A clearly defined entry point for data generation

Providers that do not extend this abstract class are **not supported** and will not be
executed by Inlinefluid.

Requirements for Provider Classes
---------------------------------

A valid provider class must:

- Extend ``AbstractExternalDataProvider``
- Be instantiable at runtime
- Implement the required abstract methods defined by the base class
- Return data that is directly usable in Fluid (arrays, objects, scalars)
- Avoid side effects where possible (especially in cacheable contexts)

Parameter Passing via Rendering Context
---------------------------------------

Providers often require input parameters (for example configuration values or record
data). Inlinefluid passes such parameters via the **Fluid rendering context**.

Example: accessing parameters from the rendering context inside a provider.

.. code-block:: php

   /**
    * Accessing variables from the rendering context
    */

   /** @var \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext */
   $variableProvider = $this->renderingContext->getVariableProvider();

   if ($variableProvider->exists('myVar')) {
       $value = $variableProvider->get('myVar');
       debug($value);
   }

How this works
--------------

1. Inlinefluid assigns variables into the rendering context (e.g. record data,
   FlexForm values, or runtime flags).
2. The provider (extending ``AbstractExternalDataProvider``) is instantiated.
3. The provider reads required parameters from the VariableProvider.
4. The provider generates and returns data.
5. Inlinefluid assigns the returned data to the configured variable name.

Typical Parameters
------------------

Common context variables used as parameters include:

- Record-based information (``data``)
- Runtime flags (e.g. ``isPreview``)
- Custom configuration values (e.g. ``myVar``)

Fluid Usage Example
-------------------

Assuming a provider assigns data to the variable ``demo``:

.. code-block:: html

   <f:debug>{demo}</f:debug>

   <f:for each="{demo.items}" as="item">
       <div>{item.title}</div>
   </f:for>

Design Notes
------------

- Providers should be deterministic in cacheable contexts.
- Use ``USER_INT`` if the provider relies on volatile or external data sources.
- The abstract base class ensures a stable API and future extensibility.

Summary
-------

Dynamic Data Providers allow Inlinefluid to enrich Fluid templates with runtime data
from custom PHP classes. By extending ``AbstractExternalDataProvider``, providers
integrate cleanly with Inlinefluid and can receive parameters via the Fluid rendering
context in a consistent and predictable manner.
