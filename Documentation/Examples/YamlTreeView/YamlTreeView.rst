Tree View from YAML (OS Family Tree)
======================================

This example shows how to render a **five-level nested tree** from a YAML file
using the built-in **YAML Static Data Provider**.

The demo data is a family tree of operating systems covering the lineages of
**DOS/Windows**, **Unix**, **Linux**, **Atari TOS**, and **Amiga OS**.

How the YAML Provider works
----------------------------

The YAML provider uses Symfony's ``Yaml::parse()`` internally, which converts
the YAML document directly into a PHP array — including arbitrarily deep
nested structures.  The result is assigned to the configured Fluid variable.

Because Fluid does not support recursive ViewHelper calls, deeply nested trees
are rendered with **explicitly nested** ``<f:for>`` loops, one per level.
Five loops cover the five levels defined in the demo data.

Demo YAML file
--------------

A ready-to-use demo file is shipped with the extension at:

``EXT:inlinefluid/Demo/os-tree.yaml``

Each node in the tree has three keys:

- ``name`` — display label
- ``year`` — year of first release
- ``children`` — list of child nodes (empty list ``[]`` at leaf nodes)

Excerpt:

.. code-block:: yaml

   name: "Operating Systems"
   children:
     - name: "Unix"
       year: 1969
       children:
         - name: "BSD Unix"
           year: 1977
           children:
             - name: "FreeBSD"
               year: 1993
               children:
                 - name: "macOS (Darwin)"
                   year: 2001
                   children:
                     - name: "macOS Ventura"
                       year: 2022
                       children: []

Step 1 – Configure the Content Element
----------------------------------------

Open the Inlinefluid backend element and switch to the **Data** tab.
Add a new **File** data provider entry:

- **Variable name**: ``ostree``
- **Format**: ``YAML``
- **Filename**: ``EXT:inlinefluid/Demo/os-tree.yaml``

Alternatively, add a **YAML** inline data provider and paste the YAML content
directly into the data field.

Step 2 – Enter the Fluid Template
------------------------------------

Switch to the **Fluid** tab and enter the following template.

The root node is ``{ostree}`` itself.  Five nested ``<f:for>`` loops handle
each level.  ``<f:if condition="{l1.children}">`` guards every inner loop so
that empty ``children: []`` lists do not produce empty ``<ul>`` elements:

.. code-block:: html

   <ul class="tree tree-level-1">

     <f:for each="{ostree.children}" as="l1">
       <li>
         <span class="tree-node">{l1.name}</span>
         <span class="tree-year">({l1.year})</span>

         <f:if condition="{l1.children}">
           <ul class="tree-level-2">
             <f:for each="{l1.children}" as="l2">
               <li>
                 <span class="tree-node">{l2.name}</span>
                 <span class="tree-year">({l2.year})</span>

                 <f:if condition="{l2.children}">
                   <ul class="tree-level-3">
                     <f:for each="{l2.children}" as="l3">
                       <li>
                         <span class="tree-node">{l3.name}</span>
                         <span class="tree-year">({l3.year})</span>

                         <f:if condition="{l3.children}">
                           <ul class="tree-level-4">
                             <f:for each="{l3.children}" as="l4">
                               <li>
                                 <span class="tree-node">{l4.name}</span>
                                 <span class="tree-year">({l4.year})</span>

                                 <f:if condition="{l4.children}">
                                   <ul class="tree-level-5">
                                     <f:for each="{l4.children}" as="l5">
                                       <li>
                                         <span class="tree-node">{l5.name}</span>
                                         <span class="tree-year">({l5.year})</span>
                                       </li>
                                     </f:for>
                                   </ul>
                                 </f:if>

                               </li>
                             </f:for>
                           </ul>
                         </f:if>

                       </li>
                     </f:for>
                   </ul>
                 </f:if>

               </li>
             </f:for>
           </ul>
         </f:if>

       </li>
     </f:for>

   </ul>

Optional CSS
------------

The following minimal CSS makes the tree visually readable without any
framework dependency.  Paste it into the ``<style>`` block or load it via
your sitepackage:

.. code-block:: html

   <style>
     .tree, .tree ul { list-style: none; padding-left: 1.4em; margin: 0; }
     .tree > li { padding-left: 0; }
     .tree li { position: relative; padding: 0.15em 0; }
     .tree li::before {
       content: "";
       position: absolute;
       left: -1em;
       top: 0.7em;
       width: 0.8em;
       height: 1px;
       background: #aaa;
     }
     .tree-node { font-weight: 500; }
     .tree-year { color: #888; font-size: 0.85em; margin-left: 0.4em; }
   </style>

How it works
------------

1. Inlinefluid passes the YAML string to ``YamlDataProvider::renderData()``.
2. Symfony ``Yaml::parse()`` converts the document into a nested PHP array.
3. The array is assigned to the Fluid variable ``ostree``.
4. ``{ostree.name}`` is the root label; ``{ostree.children}`` is the list of
   top-level branches (DOS/Windows, Unix, Linux, Atari, Amiga).
5. Five nested ``<f:for>`` loops traverse the five levels (``l1`` … ``l5``).
6. ``<f:if condition="{lN.children}">`` prevents empty ``<ul>`` tags at leaf
   nodes — Fluid treats an empty array as falsy.

Debugging the data structure
-----------------------------

Use ``<f:debug>`` to inspect the parsed array while building the template:

.. code-block:: html

   <f:debug>{ostree}</f:debug>

Notes
-----

- Fluid does not support recursive ViewHelper calls, so the nesting depth is
  fixed at five levels.  If your data can exceed five levels, consider a
  custom ViewHelper or a Dynamic Data Provider that renders the tree in PHP.
- YAML indentation must be consistent (spaces, no tabs).  The YAML provider
  will throw a parse exception for malformed input.
- Static data is cached with the page — no ``USER_INT`` is required.
