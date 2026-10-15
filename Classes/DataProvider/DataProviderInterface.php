<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\DataProvider;

interface DataProviderInterface
{
    /**
     * Turns the given raw data into something Fluid can work with.
     */
    public function renderData(string $dataString): mixed;
}
