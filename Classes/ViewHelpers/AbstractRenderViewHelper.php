<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Sg3w\Inlinefluid\Rendering\FluidSourceRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Base class for all rendering ViewHelpers.
 *
 * If errors occur they can be graciously ignored and
 * replaced by a small error message or the error itself.
 *
 * The rendering is implemented in render() instead of renderStatic(): the
 * CompileWithContentArgumentAndRenderStatic trait was dropped with Fluid 5
 * (TYPO3 v14), while render() works on Fluid 2, 4 and 5 alike.
 */
abstract class AbstractRenderViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'onError',
            'string',
            'Optional error message to display if error occur while rendering. If NULL, lets the error Exception ' .
            'pass trough (and break rendering)'
        );
        $this->registerArgument(
            'graceful',
            'boolean',
            'If forced to FALSE, errors are not caught but rather "transmitted" as every other error would be',
            false,
            false
        );
    }

    /**
     * @param array<string, mixed> $arguments
     * @return string[]
     */
    protected static function getPreparedNamespaces(array $arguments): array
    {
        $namespaces = [];
        foreach ((array)($arguments['namespaces'] ?? []) as $namespaceIdentifier => $namespace) {
            $namespaces[] = '{namespace ' . $namespaceIdentifier . '=' . $namespace . '}';
        }

        return $namespaces;
    }

    /**
     * Renders the given Fluid source within the current rendering context,
     * honouring the "graceful" and "onError" arguments.
     */
    protected function renderFluidSource(string $templateSource): string
    {
        try {
            return FluidSourceRenderer::render($this->renderingContext, $templateSource);
        } catch (\Throwable $error) {
            if (!($this->arguments['graceful'] ?? false)) {
                throw $error;
            }
            $onError = $this->arguments['onError'] ?? null;

            return $onError !== null
                ? (string)$onError
                : $error->getMessage() . ' (' . $error->getCode() . ')';
        }
    }
}
