<?php
namespace  Sg3w\Inlinefluid\DataProvider;

use Symfony\Component\Yaml;
class YamlDataProvider implements DataProviderInterface
{
    public function renderData(string $dataString) :mixed
    {
        $yaml = new Yaml\Yaml();
        return $yaml->parse($dataString);
    }
}