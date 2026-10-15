<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\DataProvider;

use Symfony\Component\Yaml\Yaml;

class YamlDataProvider implements DataProviderInterface
{
    public function renderData(string $dataString): mixed
    {
        return Yaml::parse($dataString);
    }
}
