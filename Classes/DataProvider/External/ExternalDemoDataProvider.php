<?php
namespace  Sg3w\Inlinefluid\DataProvider\External;

use Sg3w\Inlinefluid\DataProvider\External\AbstractExtrnalDataProvider;
class ExternalDemoDataProvider extends AbstractExternalDataProvider
{
    public function renderData(string $dataString) :mixed
    {
        return $_ENV;
    }
}