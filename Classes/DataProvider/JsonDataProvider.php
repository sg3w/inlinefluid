<?php
namespace  Sg3w\Inlinefluid\DataProvider;
class JsonDataProvider implements DataProviderInterface
{
    public function renderData(string $dataString) :mixed
    {
        return json_decode($dataString, true, 512, JSON_THROW_ON_ERROR);
    }
}