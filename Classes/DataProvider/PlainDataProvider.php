<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\DataProvider;

class PlainDataProvider implements DataProviderInterface
{
    public function renderData(string $dataString): mixed
    {
        return $dataString;
    }
}
