Bootstrap Carousel Demo
========================

This demo shows how Inlinefluid can be used to build an ad-hoc plugin for
a Bootstrap carousel.

Images are provided via the Media tab and rendered using a Fluid loop.

Example:

::

   <f:for each="{mediaImage}" as="img" iteration="iterator">
      <div class="carousel-item{f:if(condition: iterator.isFirst, then: ' active')}">
         <f:image image="{img}" />
      </div>
   </f:for>

This demonstrates how Inlinefluid can replace small, dedicated plugins.
