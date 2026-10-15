<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\Rendering;

use TYPO3Fluid\Fluid\Core\Parser\TemplateProcessorInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperVariableContainer;
use TYPO3Fluid\Fluid\View\TemplateView;

/**
 * Renders a Fluid template that exists as a string instead of a file.
 *
 * TYPO3 v11 to v13 offered StandaloneView for that job, but it was removed in v14.
 * Rather than branching over the version specific view factories, this renderer
 * re-uses the rendering context of the surrounding view: that keeps the TYPO3
 * ViewHelper resolver, the template cache, the request and the layout/partial root
 * paths intact on every supported core version, while Fluid's own
 * RenderingContextInterface has been stable from Fluid 2 through Fluid 5.
 */
final class FluidSourceRenderer
{
    /**
     * @param array<string, mixed>|null $variables Variables for the rendering, defaults to the ones of $renderingContext
     */
    public static function render(
        RenderingContextInterface $renderingContext,
        string $templateSource,
        ?array $variables = null
    ): string {
        $context = clone $renderingContext;

        // Fluid 2 (TYPO3 v11/v12) has no RenderingContext::__clone(), so parser, compiler
        // and processors would still be the ones of the surrounding view. They all get
        // their own instance here, and the compiler is reset on top of that: while the
        // outer template is being compiled - which happens on every cold Fluid cache -
        // its compiler holds that template as "currently processing state", and
        // AbstractTemplateView::getCurrentParsedTemplate() prefers that state over the
        // template source set below. Without the reset the outer template would be
        // rendered again, recursing until the memory limit is reached.
        $templateParser = clone $renderingContext->getTemplateParser();
        $context->setTemplateParser($templateParser);

        $templateCompiler = clone $renderingContext->getTemplateCompiler();
        $templateCompiler->reset();
        $context->setTemplateCompiler($templateCompiler);

        $context->setTemplateProcessors(array_map(
            static fn(TemplateProcessorInterface $processor): TemplateProcessorInterface => clone $processor,
            $renderingContext->getTemplateProcessors()
        ));

        // Cloned, so that setting the source does not leak into the surrounding view.
        $templatePaths = clone $renderingContext->getTemplatePaths();
        $templatePaths->setTemplateSource($templateSource);
        $context->setTemplatePaths($templatePaths);

        $context->setVariableProvider(new StandardVariableProvider(
            $variables ?? (array)$renderingContext->getVariableProvider()->getAll()
        ));

        // A fresh container on purpose: AbstractTemplateView::setRenderingContext() registers
        // the view in it, and a shared container would leave the outer view pointing here.
        $context->setViewHelperVariableContainer(new ViewHelperVariableContainer());

        $view = new TemplateView($context);

        return (string)$view->render();
    }

    /**
     * Same as render(), but turns a rendering error into the returned string.
     *
     * @param array<string, mixed>|null $variables
     */
    public static function renderGraceful(
        RenderingContextInterface $renderingContext,
        string $templateSource,
        ?array $variables = null,
        ?string $onError = null
    ): string {
        try {
            return self::render($renderingContext, $templateSource, $variables);
        } catch (\Throwable $error) {
            return $onError ?? $error->getMessage() . ' (' . $error->getCode() . ')';
        }
    }
}
