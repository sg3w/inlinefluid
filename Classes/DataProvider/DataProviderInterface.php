<?php

namespace Sg3w\Inlinefluid\DataProvider;
interface DataProviderInterface
{
    public function renderData(string $dataString): mixed;
}