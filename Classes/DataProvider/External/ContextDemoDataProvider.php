<?php
namespace  Sg3w\Inlinefluid\DataProvider\External;

use Sg3w\Inlinefluid\DataProvider\External\AbstractExtrnalDataProvider;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
class ContextDemoDataProvider extends AbstractExternalDataProvider
{
    public function renderData(string $dataString) :mixed
    {


        /**
         * Accessing variables from the rendering context
         */

        /** @var RenderingContextInterface $renderingContext */
        $variableProvider = $this->renderingContext->getVariableProvider();


        if ($variableProvider->exists('myVar')) {
            $value = $variableProvider->get('myVar');
            debug($value);
        }
        return 'content of myVar rendered by ContextDemoDataProvider is : ' . $value;
    }
}