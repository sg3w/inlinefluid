<?php
namespace  Sg3w\Inlinefluid\DataProvider\External;

use Sg3w\Inlinefluid\DataProvider\DataProviderInterface;
use Exception;

abstract class AbstractExternalDataProvider implements DataProviderInterface
{
    protected $controllerContext;

    protected $renderingContext;

    public function setRenderingContext($renderingContext)
    {
        $this->renderingContext = $renderingContext;
    }

    public function setControllerContext($controllerContext)
    {
        $this->controllerContext = $controllerContext;
    }

    public function renderData(string $dataString) :mixed
    {
        throw new Exception('Not implemented', 1665064863);
    }
}