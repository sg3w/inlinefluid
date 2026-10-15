<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\DataProvider\External;

use Sg3w\Inlinefluid\DataProvider\DataProviderInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

abstract class AbstractExternalDataProvider implements DataProviderInterface
{
    protected ?RenderingContextInterface $renderingContext = null;

    public function setRenderingContext(RenderingContextInterface $renderingContext): void
    {
        $this->renderingContext = $renderingContext;
    }

    public function getRenderingContext(): ?RenderingContextInterface
    {
        return $this->renderingContext;
    }

    public function renderData(string $dataString): mixed
    {
        throw new \RuntimeException('Not implemented', 1665064863);
    }
}
