<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\DataProvider\External;

class ExternalDemoDataProvider extends AbstractExternalDataProvider
{
    public function renderData(string $dataString): mixed
    {
        return $_ENV;
    }
}
