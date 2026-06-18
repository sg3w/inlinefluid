<?php
namespace  Sg3w\Inlinefluid\DataProvider;
class SerializedDataProvider implements DataProviderInterface
{
    public function renderData(string $dataString) :mixed
    {
        return unserialize(trim($dataString));
    }
}