Internal Exception Handling
===========================

Inlinefluid provides a **lightweight internal exception handling mechanism**
that focuses on collecting exceptions raised during **Dynamic Data Provider**
execution.

The purpose is to make provider-related issues visible during backend editing
and preview rendering without breaking the overall Fluid rendering process.

Scope of Exception Handling
---------------------------

Inlinefluid handles exceptions in a **strictly limited scope**.

Handled:

- Exceptions thrown by Dynamic Data Providers
- Exceptions during provider instantiation
- Runtime errors inside classes extending
  ``AbstractExternalDataProvider``

Not handled:

- Exceptions thrown during Fluid template rendering
- Fluid syntax errors
- ViewHelper execution errors inside templates

Exceptions originating from Fluid rendering are **not intercepted** and are
handled by TYPO3’s default error handling.

Exception Collection
--------------------

When a Dynamic Data Provider throws an exception:

1. The exception is caught internally
2. Rendering continues where possible
3. The exception is added to an internal exception collection
4. Rendering of the content element is not aborted

This ensures that individual provider failures do not block the complete
rendering process.

Rendering Exceptions for Authenticated Users
--------------------------------------------

Collected exceptions are exposed to the Fluid rendering context via the variable
``exceptions``.

For security and clarity, exception output should only be displayed to
**authenticated backend users**.

Collected exceptions are available in the Fluid variable ``{exceptions}``
(an array of exception message strings).

Exception Handling in Templates
---------------------------------

To display provider exceptions to authenticated backend users, add the
following block to your Fluid template.  It renders nothing for anonymous
frontend visitors and shows a styled alert only when a backend user is logged
in:

.. code-block:: html

   <f:be.security.ifAuthenticated>
       <f:if condition="{exceptions}">
           <div class="alert alert-danger" role="alert">
               <ul>
               <f:for as="e" each="{exceptions}">
                   <li><strong>{e}</strong></li>
               </f:for>
               </ul>
           </div>
       </f:if>
   </f:be.security.ifAuthenticated>

How it works:

- ``<f:be.security.ifAuthenticated>`` — the outer guard renders its content
  only when a TYPO3 backend user session is active.  Frontend visitors see
  nothing.
- ``<f:if condition="{exceptions}">`` — skips the block entirely when the
  exceptions array is empty (no errors occurred).
- ``<f:for as="e" each="{exceptions}">`` — iterates over all collected
  exception messages and renders each as a list item.

This approach ensures:

- Exception messages are visible during backend editing and preview
- No sensitive information is exposed to frontend users
- Multiple exceptions from different providers are shown in a structured list

Recommended placement
~~~~~~~~~~~~~~~~~~~~~

Place this block **at the top of every template** that uses Dynamic Data
Providers.  This way errors are immediately visible when editing the content
element in the backend, without requiring a separate debug view.

.. code-block:: html

   <f:be.security.ifAuthenticated>
       <f:if condition="{exceptions}">
           <div class="alert alert-danger" role="alert">
               <ul>
               <f:for as="e" each="{exceptions}">
                   <li><strong>{e}</strong></li>
               </f:for>
               </ul>
           </div>
       </f:if>
   </f:be.security.ifAuthenticated>

   <!-- your actual template output below -->
   <f:for each="{myData}" as="item">
       …
   </f:for>

Design Rationale
----------------

The internal exception handling follows these principles:

- Fail-safe provider execution
- No silent swallowing of errors
- Clear visibility during development
- No interference with Fluid’s native rendering behavior

Fluid rendering exceptions are treated as structural errors and must be fixed
directly in the template.

Important Notes
---------------

- Only provider-related exceptions are collected
- Fluid rendering exceptions are **not** caught
- Exception output must be protected by authentication checks
- The mechanism is intended for debugging and development purposes

Summary
-------

Inlinefluid collects exceptions raised by Dynamic Data Providers and exposes them
to the Fluid context in a controlled way. Using authenticated output blocks,
developers can inspect provider errors without affecting frontend users or
TYPO3’s standard Fluid error handling.
