<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\DataProvider\External;

/**
 * Demonstrates how a dynamic data provider can read the variables that are
 * already assigned to the view - for example variables coming from the
 * "Variables" sheet of the content element.
 */
class ContextDemoDataProvider extends AbstractExternalDataProvider
{
    public function renderData(string $dataString): mixed
    {
        if ($this->renderingContext === null) {
            return 'ContextDemoDataProvider has no rendering context.';
        }

        $variableProvider = $this->renderingContext->getVariableProvider();
        if (!$variableProvider->exists('myVar')) {
            return 'ContextDemoDataProvider found no variable "myVar".';
        }

        return 'content of myVar rendered by ContextDemoDataProvider is : '
            . var_export($variableProvider->get('myVar'), true);
    }
}
